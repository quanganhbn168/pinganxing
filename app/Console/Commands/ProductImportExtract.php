<?php

namespace App\Console\Commands;

use App\Models\ProductImportBatch;
use App\Models\ProductImportProfile;
use App\Services\ProductImport\RowNormalizer;
use App\Services\ProductImport\SpreadsheetExtractor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductImportExtract extends Command
{
    protected $signature = 'product-import:extract
        {path? : Absolute file path, or path relative to storage/app/private}
        {--batch= : Existing product_import_batches id to re-extract}
        {--profile= : Product import profile id}
        {--normalize : Normalize rows after extracting}';

    protected $description = 'Extract product rows and embedded images from an Excel file into staging tables';

    public function handle(SpreadsheetExtractor $extractor, RowNormalizer $normalizer): int
    {
        $batch = $this->option('batch')
            ? ProductImportBatch::query()->findOrFail((int) $this->option('batch'))
            : $this->createBatchFromPath((string) $this->argument('path'));

        $this->info("Extracting batch #{$batch->id}: {$batch->original_filename}");

        $extractor->extract($batch);

        if ($this->option('normalize')) {
            $this->info('Normalizing rows...');
            $normalizer->normalize($batch->refresh());
        }

        $batch->refresh();

        $this->info("Done. Sheets: {$batch->total_sheets}, rows: {$batch->total_rows}, assets: {$batch->assets_count}");
        $this->line("Ready: {$batch->ready_rows}, review: {$batch->review_rows}, skipped: {$batch->skipped_rows}");

        return self::SUCCESS;
    }

    protected function createBatchFromPath(string $path): ProductImportBatch
    {
        if ($path === '') {
            $this->error('Please provide a file path, or pass --batch.');
            exit(self::FAILURE);
        }

        $disk = (string) config('product_import.source_disk', 'local');

        if (is_file($path)) {
            $sourcePath = $path;
            $originalName = basename($path);
        } elseif (Storage::disk($disk)->exists($path)) {
            $sourcePath = Storage::disk($disk)->path($path);
            $originalName = basename($path);
        } else {
            $this->error("File not found: {$path}");
            exit(self::FAILURE);
        }

        $hash = hash_file('sha256', $sourcePath);
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION)) ?: 'xlsx';
        $baseName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME));
        $baseName = $baseName !== '' ? $baseName : 'import';
        $storedPath = trim((string) config('product_import.source_directory', 'product-imports/sources'), '/')
            .'/'.now()->format('Ymd-His').'-'.substr($hash, 0, 12).'-'.$baseName.'.'.$extension;

        Storage::disk($disk)->put($storedPath, file_get_contents($sourcePath));

        $profileId = $this->option('profile') ? (int) $this->option('profile') : null;

        if ($profileId) {
            ProductImportProfile::query()->findOrFail($profileId);
        }

        return ProductImportBatch::create([
            'product_import_profile_id' => $profileId,
            'original_filename' => $originalName,
            'disk' => $disk,
            'stored_path' => $storedPath,
            'source_hash' => $hash,
            'status' => ProductImportBatch::STATUS_PENDING,
        ]);
    }
}
