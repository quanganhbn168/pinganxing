<?php

namespace App\Console\Commands;

use App\Models\ProductImportBatch;
use App\Services\ProductImport\RowNormalizer;
use Illuminate\Console\Command;

class ProductImportNormalize extends Command
{
    protected $signature = 'product-import:normalize {batch : Product import batch id}';

    protected $description = 'Normalize extracted product import rows';

    public function handle(RowNormalizer $normalizer): int
    {
        $batch = ProductImportBatch::query()->findOrFail((int) $this->argument('batch'));

        $normalizer->normalize($batch);

        $batch->refresh();
        $this->info("Done. Ready: {$batch->ready_rows}, review: {$batch->review_rows}, skipped: {$batch->skipped_rows}");

        return self::SUCCESS;
    }
}
