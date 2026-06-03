<?php

namespace App\Services\ProductImport;

use App\Models\ProductImportAsset;
use App\Models\ProductImportBatch;
use App\Models\ProductImportRow;
use App\Models\ProductImportSheet;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\BaseDrawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use Throwable;

class SpreadsheetExtractor
{
    public function __construct(protected CuratorMediaWriter $mediaWriter)
    {
    }

    public function extract(ProductImportBatch $batch): ProductImportBatch
    {
        $batch->update([
            'status' => ProductImportBatch::STATUS_EXTRACTING,
            'started_at' => now(),
            'finished_at' => null,
            'errors' => null,
        ]);

        $batch->assets()->delete();
        $batch->rows()->delete();
        $batch->sheets()->delete();

        try {
            $path = Storage::disk($batch->disk)->path($batch->stored_path);
            $reader = IOFactory::createReaderForFile($path);
            $reader->setReadDataOnly(false);

            $spreadsheet = $reader->load($path);

            $this->extractWorksheets($spreadsheet, $batch);
            $this->attachAssetsToRows($batch);

            $batch->refresh()->update([
                'status' => ProductImportBatch::STATUS_EXTRACTED,
                'total_sheets' => $batch->sheets()->count(),
                'total_rows' => $batch->rows()->count(),
                'assets_count' => $batch->assets()->count(),
                'finished_at' => now(),
            ]);

            $spreadsheet->disconnectWorksheets();

            return $batch->refresh();
        } catch (Throwable $e) {
            $batch->update([
                'status' => ProductImportBatch::STATUS_FAILED,
                'errors' => [[
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]],
                'finished_at' => now(),
            ]);

            throw $e;
        }
    }

    protected function extractWorksheets(Spreadsheet $spreadsheet, ProductImportBatch $batch): void
    {
        foreach ($spreadsheet->getAllSheets() as $index => $worksheet) {
            $highestRow = (int) $worksheet->getHighestDataRow();
            $highestColumn = Coordinate::columnIndexFromString($worksheet->getHighestDataColumn());
            $highestColumn = min($highestColumn, (int) config('product_import.extract.max_columns', 80));

            $sheet = ProductImportSheet::create([
                'product_import_batch_id' => $batch->id,
                'sheet_index' => $index + 1,
                'name' => $worksheet->getTitle(),
                'highest_row' => $highestRow,
                'highest_column' => $highestColumn,
                'status' => 'extracted',
            ]);

            $headingRows = [];

            for ($rowNumber = 1; $rowNumber <= $highestRow; $rowNumber++) {
                $rawCells = $this->readRow($worksheet, $rowNumber, $highestColumn);

                if ($rowNumber <= 10 && $rawCells !== []) {
                    $headingRows[$rowNumber] = $rawCells;
                }

                if ($rawCells === []) {
                    continue;
                }

                ProductImportRow::create([
                    'product_import_batch_id' => $batch->id,
                    'product_import_sheet_id' => $sheet->id,
                    'row_number' => $rowNumber,
                    'raw_cells' => $rawCells,
                    'status' => ProductImportRow::STATUS_RAW,
                ]);
            }

            $sheet->update(['headings' => $headingRows]);
            $this->extractDrawings($worksheet->getDrawingCollection(), $batch, $sheet);
        }
    }

    protected function readRow($worksheet, int $rowNumber, int $highestColumn): array
    {
        $cells = [];

        for ($columnNumber = 1; $columnNumber <= $highestColumn; $columnNumber++) {
            $column = Coordinate::stringFromColumnIndex($columnNumber);
            $coordinate = $column.$rowNumber;
            $value = $worksheet->getCell($coordinate)->getFormattedValue();
            $value = $this->normalizeCellValue($value);

            if ($value !== '') {
                $cells[$column] = $value;
            }
        }

        return $cells;
    }

    protected function normalizeCellValue(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        $value = trim((string) $value);
        $value = preg_replace("/[ \t]+/u", ' ', $value) ?? $value;

        return trim($value);
    }

    protected function extractDrawings(iterable $drawings, ProductImportBatch $batch, ProductImportSheet $sheet): void
    {
        $assetDisk = (string) config('product_import.asset_disk', 'public');
        $assetDirectory = trim((string) config('product_import.asset_directory', 'product-imports/assets'), '/');

        foreach ($drawings as $drawing) {
            if (! $drawing instanceof BaseDrawing) {
                continue;
            }

            $coordinate = $drawing->getCoordinates() ?: 'A1';
            [$column, $rowNumber] = Coordinate::coordinateFromString($coordinate);
            $columnNumber = Coordinate::columnIndexFromString($column);
            $bytes = $this->drawingContents($drawing, $mime, $extension);

            if ($bytes === null || $bytes === '') {
                continue;
            }

            $hash = hash('sha256', $bytes);
            $extension = $this->normalizeExtension($extension, $mime);
            $baseName = Str::slug(pathinfo($this->drawingFilename($drawing), PATHINFO_FILENAME));
            $baseName = $baseName !== '' ? $baseName : 'image';
            $filename = sprintf('%s-r%s-c%s-%s.%s', $baseName, $rowNumber, $columnNumber, substr($hash, 0, 12), $extension);
            $storagePath = "{$assetDirectory}/{$batch->id}/{$sheet->sheet_index}/{$filename}";

            Storage::disk($assetDisk)->put($storagePath, $bytes);

            $media = $this->mediaWriter->ensure($assetDisk, $storagePath, [
                'alt' => $drawing->getDescription() ?: $drawing->getName() ?: null,
                'title' => $drawing->getName() ?: null,
            ]);

            ProductImportAsset::create([
                'product_import_batch_id' => $batch->id,
                'product_import_sheet_id' => $sheet->id,
                'media_id' => $media->id,
                'sheet_name' => $sheet->name,
                'drawing_name' => $drawing->getName() ?: null,
                'picture_name' => $this->drawingFilename($drawing),
                'row_number' => (int) $rowNumber,
                'column_number' => (int) $columnNumber,
                'coordinate' => $coordinate,
                'disk' => $assetDisk,
                'storage_path' => $storagePath,
                'filename' => $filename,
                'ext' => $extension,
                'mime' => $mime,
                'hash' => $hash,
                'size' => strlen($bytes),
                'width' => $media->width,
                'height' => $media->height,
                'is_ignored' => $this->shouldIgnoreImage($rowNumber, $columnNumber, strlen($bytes)),
                'meta' => [
                    'offset_x' => $drawing->getOffsetX(),
                    'offset_y' => $drawing->getOffsetY(),
                    'width' => $drawing->getWidth(),
                    'height' => $drawing->getHeight(),
                ],
            ]);
        }
    }

    protected function drawingContents(BaseDrawing $drawing, ?string &$mime, ?string &$extension): ?string
    {
        $mime = null;
        $extension = null;

        if ($drawing instanceof MemoryDrawing) {
            ob_start();
            call_user_func($drawing->getRenderingFunction(), $drawing->getImageResource());
            $bytes = ob_get_clean();

            $mime = $drawing->getMimeType();
            $extension = str($mime)->after('/')->lower()->toString();

            return is_string($bytes) ? $bytes : null;
        }

        if ($drawing instanceof Drawing) {
            $path = $drawing->getPath();

            if (str_starts_with($path, 'data:image/')) {
                [$meta, $payload] = explode(',', $path, 2);
                $bytes = base64_decode($payload, true);
                $mime = str($meta)->between('data:', ';')->toString() ?: null;
                $extension = str($mime ?: 'image/png')->after('/')->lower()->toString();

                return is_string($bytes) ? $bytes : null;
            }

            $bytes = @file_get_contents($path);

            if ($bytes === false) {
                return null;
            }

            $extension = strtolower($drawing->getExtension());

            try {
                $mime = $drawing->getImageMimeType();
            } catch (Throwable) {
                $mime = null;
            }

            return $bytes;
        }

        return null;
    }

    protected function drawingFilename(BaseDrawing $drawing): string
    {
        if ($drawing instanceof Drawing) {
            return $drawing->getFilename() ?: $drawing->getIndexedFilename();
        }

        if ($drawing instanceof MemoryDrawing) {
            return $drawing->getIndexedFilename();
        }

        return $drawing->getName() ?: 'image';
    }

    protected function normalizeExtension(?string $extension, ?string $mime): string
    {
        $extension = strtolower(trim((string) $extension, '.'));

        if ($extension === 'jpeg') {
            return 'jpg';
        }

        if ($extension !== '') {
            return $extension;
        }

        return match ($mime) {
            'image/jpeg' => 'jpg',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            default => 'png',
        };
    }

    protected function shouldIgnoreImage(int $rowNumber, int $columnNumber, int $size): bool
    {
        $ignoreBeforeRow = (int) config('product_import.extract.ignore_images_before_row', 2);
        $ignoreBeforeColumn = (int) config('product_import.extract.ignore_images_before_column', 1);
        $minBytes = (int) config('product_import.extract.min_image_bytes', 1024);

        if ($rowNumber <= $ignoreBeforeRow && $columnNumber <= $ignoreBeforeColumn) {
            return true;
        }

        return $size < $minBytes;
    }

    protected function attachAssetsToRows(ProductImportBatch $batch): void
    {
        $batch->assets()
            ->where('is_ignored', false)
            ->each(function (ProductImportAsset $asset): void {
                $row = ProductImportRow::query()
                    ->where('product_import_batch_id', $asset->product_import_batch_id)
                    ->where('product_import_sheet_id', $asset->product_import_sheet_id)
                    ->where('row_number', $asset->row_number)
                    ->first();

                if ($row) {
                    $asset->update(['product_import_row_id' => $row->id]);
                }
            });
    }
}
