<?php

namespace App\Services\ProductImport;

use App\Models\Product;
use App\Models\ProductImportError;
use Awcodes\Curator\Models\Media;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class ZipProductImporter
{
    public function import(
        string $sessionId,
        string $brand,
        bool $onlyHasImage = false,
        bool $onlyHasSpecs = false,
    ): array {
        $brand = Str::slug($brand);
        $basePath = storage_path("app/imports/tmp/{$sessionId}");
        $previewPath = $basePath . '/preview.json';

        if (! File::exists($previewPath)) {
            throw new \RuntimeException('Không tìm thấy phiên preview. Anh đọc dữ liệu lại nhé.');
        }

        $payload = json_decode(File::get($previewPath), true);

        if (! is_array($payload)) {
            throw new \RuntimeException('File preview.json không hợp lệ.');
        }

        $products = $payload['products'] ?? [];
        $total = count($products);

        $result = [
            'imported' => 0,
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'missing_images' => 0,
            'created_media' => 0,
            'errors' => 0,
        ];

        $this->putStatus($sessionId, [
            'state' => 'running',
            'message' => 'Đang import sản phẩm...',
            'processed' => 0,
            'total' => $total,
            'result' => $result,
        ]);

        foreach ($products as $index => $item) {
            try {
                $name = trim((string) ($item['name'] ?? ''));

                if ($name === '') {
                    $result['skipped']++;
                    $result['errors']++;

                    $this->saveImportError($sessionId, $brand, $item, 'missing_name', 'Sản phẩm thiếu tên.');
                    $this->tick($sessionId, $index + 1, $total, $result, 'Bỏ qua sản phẩm thiếu tên.');
                    continue;
                }

                $images = $item['images'] ?? [];
                $specifications = $this->cleanValue($item['specifications'] ?? null);

                if ($onlyHasImage && count($images) === 0) {
                    $result['skipped']++;
                    $this->tick($sessionId, $index + 1, $total, $result, "Bỏ qua {$name} vì thiếu ảnh.");
                    continue;
                }

                if ($onlyHasSpecs && blank($specifications)) {
                    $result['skipped']++;
                    $this->tick($sessionId, $index + 1, $total, $result, "Bỏ qua {$name} vì thiếu thông số.");
                    continue;
                }

                $code = $this->cleanValue($item['code'] ?? null);

                $mediaResult = $this->importImagesToCurator($images, $basePath, $brand, $name);
                $mediaIds = $mediaResult['media_ids'];

                $result['missing_images'] += $mediaResult['missing_images'];
                $result['created_media'] += $mediaResult['created_media'];

                $priceData = $this->normalizeProductPrices(
                    $item['price'] ?? null,
                    $item['retail_price'] ?? null
                );

                $lookup = $code
                    ? ['code' => $code, 'name' => $name]
                    : ['name' => $name];

                $exists = Product::where($lookup)->exists();

                $productData = $this->filterColumns(Product::class, [
                    'type' => 'simple',
                    'brand_id' => null,
                    'category_id' => null,
                    'name' => $name,
                    'code' => $code,
                    'description' => $this->makeShortDescription($item, $brand),
                    'content' => $this->makeDescription($item, $brand),
                    'specifications' => $specifications,
                    'price' => $priceData['price'],
                    'price_discount' => $priceData['price_discount'],
                    'image_id' => $mediaIds[0] ?? null,
                    'gallery' => $mediaIds,
                    'stock' => 0,
                    'status' => true,
                    'has_variants' => false,
                    'is_featured' => false,
                    'is_home' => false,
                    'is_on_sale' => filled($priceData['price_discount']),
                    'discount_type' => null,
                    'discount_value' => null,
                    'product_type' => 'physical',
                ]);

                Product::updateOrCreate($lookup, $productData);

                $result['imported']++;
                $result[$exists ? 'updated' : 'created']++;

                $this->tick($sessionId, $index + 1, $total, $result, "Đã import {$name}.");
            } catch (QueryException $e) {
                $result['errors']++;
                $result['skipped']++;
                $this->saveImportError($sessionId, $brand, $item, 'query_exception', $e->getMessage());
                $this->tick($sessionId, $index + 1, $total, $result, 'Có lỗi database, đã ghi log lỗi.');
            } catch (Throwable $e) {
                $result['errors']++;
                $result['skipped']++;
                $this->saveImportError($sessionId, $brand, $item, get_class($e), $e->getMessage());
                $this->tick($sessionId, $index + 1, $total, $result, 'Có lỗi import, đã ghi log lỗi.');
            }
        }

        $this->putStatus($sessionId, [
            'state' => 'finished',
            'message' => 'Import hoàn tất.',
            'processed' => $total,
            'total' => $total,
            'result' => $result,
        ]);

        return $result;
    }

    public static function statusKey(string $sessionId): string
    {
        return "product-import:zip:{$sessionId}";
    }

    public function putStatus(string $sessionId, array $status): void
    {
        Cache::put(
            self::statusKey($sessionId),
            array_merge(['updated_at' => now()->toISOString()], $status),
            (int) config('product_import.queue.status_ttl', 3600)
        );
    }

    private function tick(string $sessionId, int $processed, int $total, array $result, string $message): void
    {
        $this->putStatus($sessionId, [
            'state' => 'running',
            'message' => $message,
            'processed' => $processed,
            'total' => $total,
            'result' => $result,
        ]);
    }

    private function importImagesToCurator(array $images, string $basePath, string $brand, ?string $alt = null): array
    {
        $mediaIds = [];
        $missingImages = 0;
        $createdMedia = 0;

        foreach ($images as $image) {
            if (! is_array($image)) {
                continue;
            }

            $oldRelativePath = $image['file'] ?? null;

            if (! $oldRelativePath) {
                continue;
            }

            $safeOldRelativePath = $this->sanitizeRelativePath($oldRelativePath);
            $sourcePath = $basePath . '/' . $safeOldRelativePath;

            if (! File::exists($sourcePath)) {
                $missingImages++;
                continue;
            }

            $media = $this->saveImageToCurator(
                sourcePath: $sourcePath,
                storagePath: $this->makeProductImageStoragePath($safeOldRelativePath, $brand),
                alt: $alt
            );

            if ($media) {
                $mediaIds[] = $media->id;
                $createdMedia += $media->wasRecentlyCreated ? 1 : 0;
            }
        }

        return [
            'media_ids' => collect($mediaIds)->filter()->unique()->values()->all(),
            'missing_images' => $missingImages,
            'created_media' => $createdMedia,
        ];
    }

    private function saveImageToCurator(string $sourcePath, string $storagePath, ?string $alt = null): ?Media
    {
        if (! File::exists($sourcePath)) {
            return null;
        }

        $disk = 'public';
        Storage::disk($disk)->put($storagePath, File::get($sourcePath));

        $fileName = basename($storagePath);
        $directory = dirname($storagePath);
        $directory = $directory === '.' ? null : $directory;
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        [$width, $height] = @getimagesize($sourcePath) ?: [null, null];

        $mediaData = $this->filterColumns(Media::class, [
            'disk' => $disk,
            'directory' => $directory,
            'visibility' => 'public',
            'name' => pathinfo($fileName, PATHINFO_FILENAME),
            'path' => $storagePath,
            'width' => $width,
            'height' => $height,
            'size' => File::size($sourcePath),
            'type' => File::mimeType($sourcePath),
            'ext' => $extension,
            'alt' => $alt,
            'title' => $alt,
            'description' => null,
            'caption' => null,
        ]);

        $lookup = $this->filterColumns(Media::class, [
            'disk' => $disk,
            'path' => $storagePath,
        ]);

        return empty($lookup)
            ? Media::create($mediaData)
            : Media::updateOrCreate($lookup, $mediaData);
    }

    private function makeProductImageStoragePath(string $oldRelativePath, string $brand): string
    {
        $path = preg_replace('/^images\//', '', $this->sanitizeRelativePath($oldRelativePath));
        $path = preg_replace('/^' . preg_quote($brand, '/') . '\//', '', $path);

        return $this->sanitizeRelativePath('products/' . $brand . '/' . $path);
    }

    private function normalizeProductPrices(mixed $price, mixed $retailPrice): array
    {
        $dealerPrice = $this->normalizeNumber($price);
        $sellingPrice = $this->normalizeNumber($retailPrice);

        if ($sellingPrice && $dealerPrice && $dealerPrice < $sellingPrice) {
            return ['price' => $sellingPrice, 'price_discount' => $dealerPrice];
        }

        return ['price' => $sellingPrice ?: $dealerPrice, 'price_discount' => null];
    }

    private function normalizeNumber(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return (int) round((float) $value);
        }

        $number = preg_replace('/[^\d]/', '', (string) $value);

        return $number === '' ? null : (int) $number;
    }

    private function cleanValue(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_array($value) || is_object($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function makeShortDescription(array $item, string $brand): ?string
    {
        $name = $item['name'] ?? null;

        if (! $name) {
            return null;
        }

        $parts = [];

        if (! empty($item['code'])) {
            $parts[] = "Sản phẩm {$item['code']}";
        }

        $parts[] = $name;

        if (! empty($item['warranty'])) {
            $parts[] = "Bảo hành {$item['warranty']}";
        }

        return implode('. ', $parts) . '.';
    }

    private function makeDescription(array $item, string $brand): ?string
    {
        $name = $item['name'] ?? null;

        if (! $name) {
            return null;
        }

        $html = [];
        $html[] = '<p><strong>' . e($name) . '</strong>'
            . ' là sản phẩm thuộc thương hiệu <strong>' . e(Str::headline($brand)) . '</strong>'
            . (! empty($item['category']) ? ', nhóm <strong>' . e($item['category']) . '</strong>' : '')
            . '.</p>';

        if (! empty($item['code'])) {
            $html[] = '<p><strong>Mã sản phẩm:</strong> ' . e($item['code']) . '</p>';
        }

        if (! empty($item['warranty'])) {
            $html[] = '<p><strong>Bảo hành:</strong> ' . e($item['warranty']) . '</p>';
        }

        if (! empty($item['specifications'])) {
            $html[] = '<h3>Thông số kỹ thuật</h3>';
            $html[] = '<div style="white-space: pre-line;">' . e($item['specifications']) . '</div>';
        }

        return implode("\n", $html);
    }

    private function filterColumns(string $modelClass, array $data): array
    {
        $table = (new $modelClass())->getTable();

        return collect($data)
            ->filter(fn ($value, $column): bool => Schema::hasColumn($table, $column))
            ->all();
    }

    private function sanitizeRelativePath(string $path): string
    {
        $path = str_replace('\\', '/', $path);
        $path = str_replace(['../', '..\\'], '', $path);

        return ltrim($path, '/');
    }

    private function saveImportError(
        string $sessionId,
        string $brand,
        array $item,
        string $errorType,
        string $errorMessage
    ): void {
        try {
            ProductImportError::create([
                'session_id' => Str::limit($sessionId, 100, ''),
                'brand' => Str::limit($brand, 100, ''),
                'code' => $this->safeShortText($item['code'] ?? null, 190),
                'name' => $this->safeShortText($item['name'] ?? null, 250),
                'error_type' => Str::limit($errorType, 100, ''),
                'error_message' => Str::limit($errorMessage, 2000, ''),
                'raw_product' => $item,
            ]);
        } catch (Throwable $e) {
            logger()->error('Không lưu được product import error', [
                'session_id' => $sessionId,
                'brand' => $brand,
                'error_type' => $errorType,
                'error_message' => $errorMessage,
                'save_error' => $e->getMessage(),
            ]);
        }
    }

    private function safeShortText(mixed $value, int $limit): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_array($value) || is_object($value)) {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        return Str::limit(trim((string) $value), $limit, '');
    }
}
