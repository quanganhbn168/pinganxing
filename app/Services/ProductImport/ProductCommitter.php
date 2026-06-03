<?php

namespace App\Services\ProductImport;

use App\Models\Product;
use App\Models\ProductImportBatch;
use App\Models\ProductImportRow;
use App\Support\SlugGenerator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class ProductCommitter
{
    public function __construct(protected SlugGenerator $slugGenerator)
    {
    }

    public function commit(ProductImportBatch $batch, bool $includeNeedsReview = false): ProductImportBatch
    {
        $batch->update([
            'status' => ProductImportBatch::STATUS_COMMITTING,
            'started_at' => $batch->started_at ?: now(),
            'finished_at' => null,
        ]);

        $statuses = [ProductImportRow::STATUS_READY];

        if ($includeNeedsReview) {
            $statuses[] = ProductImportRow::STATUS_NEEDS_REVIEW;
        }

        $imported = 0;
        $failed = 0;

        $batch->rows()
            ->whereIn('status', $statuses)
            ->orderBy('id')
            ->chunkById(100, function ($rows) use (&$imported, &$failed): void {
                foreach ($rows as $row) {
                    try {
                        DB::transaction(function () use ($row): void {
                            $this->commitRow($row);
                        });

                        $imported++;
                    } catch (Throwable $e) {
                        $failed++;

                        $row->update([
                            'status' => ProductImportRow::STATUS_FAILED,
                            'errors' => [[
                                'message' => $e->getMessage(),
                                'file' => $e->getFile(),
                                'line' => $e->getLine(),
                            ]],
                        ]);
                    }
                }
            });

        $batch->update([
            'status' => $failed > 0 ? ProductImportBatch::STATUS_READY : ProductImportBatch::STATUS_COMMITTED,
            'imported_rows' => $batch->rows()->where('status', ProductImportRow::STATUS_IMPORTED)->count(),
            'failed_rows' => $batch->rows()->where('status', ProductImportRow::STATUS_FAILED)->count(),
            'finished_at' => now(),
        ]);

        return $batch->refresh();
    }

    protected function commitRow(ProductImportRow $row): Product
    {
        if (! filled($row->name)) {
            throw new \RuntimeException('Import row has no product name.');
        }

        $code = filled($row->code)
            ? trim((string) $row->code)
            : $this->makeCode((string) $row->name, $row->id);

        $payload = [
            'type' => 'simple',
            'category_id' => $row->category_id,
            'name' => $row->name,
            'code' => $code,
            'image_id' => $row->image_id,
            'gallery' => $row->gallery ?: null,
            'description' => $row->description,
            'content' => $row->content,
            'specifications' => $row->specifications,
            'price' => $row->price,
            'price_discount' => null,
            'stock' => (int) config('product_import.commit.default_stock', 0),
            'status' => (bool) config('product_import.commit.default_status', true),
            'has_variants' => false,
            'is_featured' => false,
            'is_home' => false,
            'is_on_sale' => false,
            'product_type' => (string) config('product_import.commit.default_product_type', 'physical'),
            'meta_title' => $row->name,
            'meta_description' => $row->description ? Str::limit(strip_tags($row->description), 160) : null,
            'meta_image_id' => $row->image_id,
        ];

        $product = Product::query()->where('code', $code)->first();

        if ($product && (bool) config('product_import.commit.update_existing_by_code', true)) {
            $product->fill($payload)->save();
        } else {
            $product = Product::create($payload);
        }

        $this->slugGenerator->syncModel($product, $product->name, $product->slugData?->id);

        $row->update([
            'product_id' => $product->id,
            'code' => $code,
            'status' => ProductImportRow::STATUS_IMPORTED,
            'errors' => null,
        ]);

        return $product;
    }

    protected function makeCode(string $name, int $rowId): string
    {
        $base = Str::upper(Str::slug($name, ''));
        $base = $base !== '' ? Str::limit($base, 32, '') : 'IMPORT';
        $candidate = $base.'-'.$rowId;
        $counter = 1;

        while (Product::query()->where('code', $candidate)->exists()) {
            $candidate = $base.'-'.$rowId.'-'.$counter++;
        }

        return $candidate;
    }
}
