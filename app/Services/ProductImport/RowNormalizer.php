<?php

namespace App\Services\ProductImport;

use App\Models\Product;
use App\Models\ProductImportBatch;
use App\Models\ProductImportRow;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class RowNormalizer
{
    public function __construct(
        protected PriceParser $priceParser,
        protected CategoryResolver $categoryResolver,
    ) {
    }

    public function normalize(ProductImportBatch $batch): ProductImportBatch
    {
        $batch->loadMissing('profile');
        $batch->update(['status' => ProductImportBatch::STATUS_NORMALIZING]);

        $batch->rows()
            ->with(['sheet', 'assets'])
            ->orderBy('product_import_sheet_id')
            ->orderBy('row_number')
            ->chunkById(200, function ($rows) use ($batch): void {
                foreach ($rows as $row) {
                    $this->normalizeRow($row, $batch);
                }
            });

        $batch->update([
            'status' => ProductImportBatch::STATUS_READY,
            'ready_rows' => $batch->rows()->where('status', ProductImportRow::STATUS_READY)->count(),
            'review_rows' => $batch->rows()->where('status', ProductImportRow::STATUS_NEEDS_REVIEW)->count(),
            'skipped_rows' => $batch->rows()->where('status', ProductImportRow::STATUS_SKIPPED)->count(),
            'failed_rows' => $batch->rows()->where('status', ProductImportRow::STATUS_FAILED)->count(),
        ]);

        return $batch->refresh();
    }

    protected function normalizeRow(ProductImportRow $row, ProductImportBatch $batch): void
    {
        $raw = $row->raw_cells ?? [];

        if ($raw === []) {
            $row->update(['status' => ProductImportRow::STATUS_SKIPPED]);

            return;
        }

        $map = $this->columnMapForRow($batch, (string) $row->sheet?->name);
        $startRow = (int) ($map['start_row'] ?? 1);

        if ($row->row_number < $startRow || $this->looksLikeHeader($raw)) {
            $row->update([
                'status' => ProductImportRow::STATUS_SKIPPED,
                'normalized' => ['reason' => 'header_or_before_start_row'],
            ]);

            return;
        }

        $code = $this->readMappedField($raw, $map, 'code') ?: $this->guessCode($raw);
        $name = $this->readMappedField($raw, $map, 'name') ?: $this->guessName($raw, $code);
        $description = $this->readMappedField($raw, $map, 'description');
        $content = $this->readMappedField($raw, $map, 'content');
        $specifications = $this->readMappedField($raw, $map, 'specifications');
        $categoryPath = $this->readMappedField($raw, $map, 'category_path');
        $priceValue = $this->readMappedField($raw, $map, 'price');
        $price = $this->priceParser->parse($priceValue) ?? $this->guessPrice($raw);
        $category = $this->categoryResolver->resolve($row, $categoryPath);
        $mediaIds = $row->assets()
            ->where('is_ignored', false)
            ->whereNotNull('media_id')
            ->orderBy('column_number')
            ->pluck('media_id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        if (filled($name) && mb_strlen((string) $name) > 255) {
            $description = $description ?: (string) $name;
            $firstLine = strtok((string) $name, "\n") ?: (string) $name;
            $name = Str::limit($firstLine, 250, '');
        }

        $warnings = [];

        if (! filled($name)) {
            $warnings[] = 'missing_name';
        }

        if (! filled($code)) {
            $warnings[] = 'missing_code_will_generate';
        }

        if (! $price) {
            $warnings[] = 'missing_price';
        }

        if (! $category['category_id']) {
            $warnings[] = 'missing_category';
        }

        if ($mediaIds === []) {
            $warnings[] = 'missing_image';
        }

        $existingProductId = filled($code)
            ? Product::query()->where('code', $code)->value('id')
            : null;

        if ($existingProductId) {
            $warnings[] = 'existing_product_will_update';
        }

        $status = filled($name)
            ? ProductImportRow::STATUS_READY
            : ProductImportRow::STATUS_NEEDS_REVIEW;

        if (! filled($name) && ! filled($code) && ! $price) {
            $status = ProductImportRow::STATUS_SKIPPED;
        }

        $row->update([
            'normalized' => [
                'column_map' => $map,
                'category_confidence' => $category['confidence'],
                'existing_product_id' => $existingProductId,
            ],
            'code' => filled($code) ? Str::limit(trim((string) $code), 255, '') : null,
            'name' => filled($name) ? Str::limit(trim((string) $name), 255, '') : null,
            'description' => $description ?: null,
            'content' => $content ?: null,
            'specifications' => $specifications ?: null,
            'price' => $price,
            'category_id' => $category['category_id'],
            'suggested_category_id' => $category['category_id'],
            'category_path' => $category['category_path'],
            'image_id' => $mediaIds[0] ?? null,
            'gallery' => $mediaIds,
            'status' => $status,
            'warnings' => $warnings ?: null,
            'errors' => null,
        ]);
    }

    protected function columnMapForRow(ProductImportBatch $batch, string $sheetName): array
    {
        $default = config('product_import.default_column_map.default', []);
        $configured = $batch->profile?->column_map ?: [];
        $profileDefault = Arr::get($configured, 'default', []);
        $profileSheet = Arr::get($configured, 'sheets.'.$sheetName, []);

        return array_filter(array_replace($default, $profileDefault, $profileSheet), fn ($value) => $value !== null);
    }

    protected function readMappedField(array $raw, array $map, string $field): ?string
    {
        $columns = $map[$field] ?? null;

        if (! $columns) {
            return null;
        }

        $columns = is_array($columns) ? $columns : [$columns];

        $values = collect($columns)
            ->map(fn ($column) => strtoupper(trim((string) $column)))
            ->map(fn ($column) => $raw[$column] ?? null)
            ->filter(fn ($value) => filled($value))
            ->map(fn ($value) => trim((string) $value))
            ->values();

        return $values->isEmpty() ? null : $values->implode("\n");
    }

    protected function looksLikeHeader(array $raw): bool
    {
        $joined = Str::lower(implode(' ', $raw));
        $headerHits = 0;

        foreach (['stt', 'model', 'ma hang', 'mã hàng', 'ten hang', 'tên hàng', 'don gia', 'đơn giá', 'gia niem yet', 'giá niêm yết', 'hinh anh', 'hình ảnh'] as $needle) {
            if (str_contains($joined, Str::lower($needle))) {
                $headerHits++;
            }
        }

        return $headerHits >= 2;
    }

    protected function guessCode(array $raw): ?string
    {
        foreach ($raw as $value) {
            $value = trim((string) $value);

            if (preg_match('/\b[A-Z0-9]{2,}(?:[-_\/.][A-Z0-9]{1,}){1,}\b/i', $value, $matches)) {
                return strtoupper($matches[0]);
            }
        }

        foreach ($raw as $value) {
            $value = trim((string) $value);

            if (preg_match('/^[A-Z0-9][A-Z0-9._\-\/]{3,60}$/i', $value) && preg_match('/\d/', $value)) {
                return strtoupper($value);
            }
        }

        return null;
    }

    protected function guessName(array $raw, ?string $code): ?string
    {
        $candidates = collect($raw)
            ->map(fn ($value) => trim((string) $value))
            ->reject(fn ($value) => $value === '' || $value === $code)
            ->reject(fn ($value) => str_starts_with(trim($value), '*'))
            ->reject(fn ($value) => mb_strlen($value) > 180)
            ->reject(fn ($value) => $this->priceParser->parse($value) !== null)
            ->reject(fn ($value) => preg_match('/^[A-Z0-9][A-Z0-9._\-\/]{3,60}$/i', $value) === 1)
            ->values();

        return $candidates->first();
    }

    protected function guessPrice(array $raw): ?int
    {
        $prices = collect($raw)
            ->map(fn ($value) => $this->priceParser->parse($value))
            ->filter(fn ($value) => $value !== null && $value >= 1000)
            ->values();

        return $prices->isEmpty() ? null : (int) $prices->last();
    }
}
