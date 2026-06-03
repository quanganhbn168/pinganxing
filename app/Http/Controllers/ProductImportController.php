<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Awcodes\Curator\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;
use App\Models\ProductImportError;
use Illuminate\Database\QueryException;
use Throwable;
class ProductImportController extends Controller
{
    public function index()
    {
        return view('product-import');
    }

    public function preview(Request $request)
    {
        $request->validate([
            'brand' => ['required', 'string', 'max:100'],
            'file' => ['required', 'file', 'mimes:zip'],
        ]);

        $brand = Str::slug($request->input('brand'));
        $sessionId = (string) Str::uuid();

        $basePath = storage_path("app/imports/tmp/{$sessionId}");
        File::ensureDirectoryExists($basePath);

        $request->file('file')->move($basePath, 'upload.zip');

        $zipPath = $basePath . '/upload.zip';

        $zip = new ZipArchive();

        if ($zip->open($zipPath) !== true) {
            return response()->json([
                'message' => 'Không mở được file ZIP.',
            ], 422);
        }

        $zip->extractTo($basePath);
        $zip->close();

        $jsonPath = $this->findProductsJson($basePath);

        if (! $jsonPath) {
            return response()->json([
                'message' => 'Không tìm thấy products.json trong file ZIP.',
            ], 422);
        }

        $payload = json_decode(File::get($jsonPath), true);

        if (! is_array($payload)) {
            return response()->json([
                'message' => 'products.json không hợp lệ.',
            ], 422);
        }

        /*
         * Hỗ trợ 2 kiểu JSON:
         *
         * Kiểu 1:
         * [
         *   { product... }
         * ]
         *
         * Kiểu 2:
         * {
         *   "metadata": {...},
         *   "products": [
         *      { product... }
         *   ]
         * }
         */
        $rawProducts = $payload['products'] ?? $payload;
        $metadata = $payload['metadata'] ?? [];

        if (! is_array($rawProducts)) {
            return response()->json([
                'message' => 'Không tìm thấy danh sách products trong products.json.',
            ], 422);
        }

        $products = collect($rawProducts)
            ->filter(fn ($item) => is_array($item))
            ->map(function ($item) use ($sessionId, $brand) {
                return $this->normalizeProductForPreview($item, $sessionId, $brand);
            })
            ->values();

        $summary = [
            'brand' => $brand,
            'source_file' => $metadata['source_file'] ?? null,
            'total_products' => $products->count(),
            'products_with_images' => $products->filter(fn ($item) => count($item['images'] ?? []) > 0)->count(),
            'products_without_images' => $products->filter(fn ($item) => count($item['images'] ?? []) === 0)->count(),
            'total_images' => $products->sum(fn ($item) => count($item['images'] ?? [])),
            'products_with_specs' => $products->filter(fn ($item) => filled($item['specifications'] ?? null))->count(),
            'products_without_specs' => $products->filter(fn ($item) => blank($item['specifications'] ?? null))->count(),
        ];

        File::put($basePath . '/preview.json', json_encode([
            'brand' => $brand,
            'metadata' => $metadata,
            'summary' => $summary,
            'products' => $products,
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        return response()->json([
            'session_id' => $sessionId,
            'summary' => $summary,
            'products' => $products->take(100)->values(),
        ]);
    }

    public function confirm(Request $request)
    {
        $request->validate([
            'session_id' => ['required', 'string'],
            'brand' => ['required', 'string', 'max:100'],
            'only_has_image' => ['nullable'],
            'only_has_specs' => ['nullable'],
        ]);

        $sessionId = $request->input('session_id');
        $brand = Str::slug($request->input('brand'));

        $onlyHasImage = filter_var($request->input('only_has_image'), FILTER_VALIDATE_BOOLEAN);
        $onlyHasSpecs = filter_var($request->input('only_has_specs'), FILTER_VALIDATE_BOOLEAN);

        $basePath = storage_path("app/imports/tmp/{$sessionId}");
        $previewPath = $basePath . '/preview.json';

        if (! File::exists($previewPath)) {
            return response()->json([
                'message' => 'Không tìm thấy phiên preview. Anh đọc dữ liệu lại nhé.',
            ], 404);
        }

        $payload = json_decode(File::get($previewPath), true);

        if (! is_array($payload)) {
            return response()->json([
                'message' => 'File preview.json không hợp lệ.',
            ], 422);
        }

        $products = $payload['products'] ?? [];

        $imported = 0;
        $updated = 0;
        $created = 0;
        $skipped = 0;
        $missingImages = 0;
        $createdMedia = 0;
        $errors = 0;

        foreach ($products as $index => $item) {
            try {
                $name = trim((string) ($item['name'] ?? ''));

                if ($name === '') {
                    $skipped++;

                    $this->saveImportError(
                        sessionId: $sessionId,
                        brand: $brand,
                        item: $item,
                        errorType: 'missing_name',
                        errorMessage: 'Sản phẩm thiếu tên.'
                    );

                    $errors++;
                    continue;
                }

                $images = $item['images'] ?? [];
                $specifications = $this->cleanValue($item['specifications'] ?? null);

                if ($onlyHasImage && count($images) === 0) {
                    $skipped++;
                    continue;
                }

                if ($onlyHasSpecs && blank($specifications)) {
                    $skipped++;
                    continue;
                }

                $code = $this->cleanValue($item['code'] ?? null);

                $mediaResult = $this->importImagesToCurator(
                    $images,
                    $basePath,
                    $brand,
                    $name
                );

                $mediaIds = $mediaResult['media_ids'];
                $missingImages += $mediaResult['missing_images'];
                $createdMedia += $mediaResult['created_media'];

                $mainImageId = $mediaIds[0] ?? null;

                $priceData = $this->normalizeProductPrices(
                    $item['price'] ?? null,
                    $item['retail_price'] ?? null
                );

                $lookup = $code
                ? ['code' => $code, 'name' => $name]
                : ['name' => $name];

                $exists = Product::where($lookup)->exists();

                $productData = [
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

                    'image_id' => $mainImageId,
                    'gallery' => $mediaIds,

                    'stock' => 0,
                    'status' => true,
                    'has_variants' => false,
                    'is_featured' => false,
                    'is_home' => false,
                    'is_on_sale' => filled($priceData['price_discount']),

                    'discount_type' => null,
                    'discount_value' => null,

            /*
             * Chỗ này anh chỉnh đúng enum trong DB.
             * Nếu DB cho simple thì để simple.
             */
            'product_type' => 'physical',
        ];

        $productData = $this->filterColumns(Product::class, $productData);

        Product::updateOrCreate($lookup, $productData);

        $imported++;

        if ($exists) {
            $updated++;
        } else {
            $created++;
        }
    } catch (QueryException $e) {
        $errors++;
        $skipped++;

        $this->saveImportError(
            sessionId: $sessionId,
            brand: $brand,
            item: $item,
            errorType: 'query_exception',
            errorMessage: $e->getMessage()
        );

        continue;
    } catch (Throwable $e) {
        $errors++;
        $skipped++;

        $this->saveImportError(
            sessionId: $sessionId,
            brand: $brand,
            item: $item,
            errorType: get_class($e),
            errorMessage: $e->getMessage()
        );

        continue;
    }
}

        return response()->json([
    'imported' => $imported,
    'created' => $created,
    'updated' => $updated,
    'skipped' => $skipped,
    'missing_images' => $missingImages,
    'created_media' => $createdMedia,
    'errors' => $errors,
]);
    }

    public function previewImage(Request $request, string $sessionId)
    {
        $path = $request->query('path');

        if (! $path) {
            abort(404);
        }

        $safePath = $this->sanitizeRelativePath($path);
        $fullPath = storage_path("app/imports/tmp/{$sessionId}/{$safePath}");

        if (! File::exists($fullPath)) {
            abort(404);
        }

        return response()->file($fullPath);
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

            $storagePath = $this->makeProductImageStoragePath(
                oldRelativePath: $safeOldRelativePath,
                brand: $brand
            );

            $media = $this->saveImageToCurator(
                sourcePath: $sourcePath,
                storagePath: $storagePath,
                alt: $alt
            );

            if (! $media) {
                continue;
            }

            $mediaIds[] = $media->id;

            if ($media->wasRecentlyCreated) {
                $createdMedia++;
            }
        }

        $mediaIds = collect($mediaIds)
            ->filter()
            ->unique()
            ->values()
            ->all();

        return [
            'media_ids' => $mediaIds,
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

        /*
         * Lưu file thật vào storage/app/public/...
         * Nhớ có public/storage trỏ về storage/app/public.
         */
        Storage::disk($disk)->put($storagePath, File::get($sourcePath));

        $directory = dirname($storagePath);
        $directory = $directory === '.' ? null : $directory;

        $fileName = basename($storagePath);
        $name = pathinfo($fileName, PATHINFO_FILENAME);
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $mimeType = File::mimeType($sourcePath);
        $size = File::size($sourcePath);

        [$width, $height] = @getimagesize($sourcePath) ?: [null, null];

        $mediaData = [
            'disk' => $disk,
            'directory' => $directory,
            'visibility' => 'public',
            'name' => $name,
            'path' => $storagePath,
            'width' => $width,
            'height' => $height,
            'size' => $size,
            'type' => $mimeType,
            'ext' => $extension,
            'alt' => $alt,
            'title' => $alt,
            'description' => null,
            'caption' => null,
        ];

        $mediaData = $this->filterColumns(Media::class, $mediaData);

        /*
         * Tránh tạo trùng record Curator khi import lại cùng file.
         */
        $lookup = $this->filterColumns(Media::class, [
            'disk' => $disk,
            'path' => $storagePath,
        ]);

        if (empty($lookup)) {
            return Media::create($mediaData);
        }

        return Media::updateOrCreate($lookup, $mediaData);
    }

    private function makeProductImageStoragePath(string $oldRelativePath, string $brand): string
    {
        /*
         * Input có thể là:
         * images/dahua/camera-ip/abc.png
         * images/camera-ip/abc.png
         * dahua/camera-ip/abc.png
         * camera-ip/abc.png
         */

        $path = $this->sanitizeRelativePath($oldRelativePath);

        /*
         * Bỏ prefix images/.
         */
        $path = preg_replace('/^images\//', '', $path);

        /*
         * Nếu path đã bắt đầu bằng brand rồi thì bỏ đi để tránh:
         * products/dahua/dahua/camera-ip/abc.png
         */
        $path = preg_replace('/^' . preg_quote($brand, '/') . '\//', '', $path);

        $storagePath = 'products/' . $brand . '/' . $path;

        return $this->sanitizeRelativePath($storagePath);
    }

    private function normalizeProductPrices(mixed $price, mixed $retailPrice): array
    {
        $dealerPrice = $this->normalizeNumber($price);
        $sellingPrice = $this->normalizeNumber($retailPrice);

        /*
         * Ý nghĩa:
         * - price: giá bán chính trên web.
         * - price_discount: giá giảm nếu có.
         *
         * Nếu có cả giá bán lẻ và giá đại lý:
         * - Giá bán lẻ lớn hơn giá đại lý => price = bán lẻ, price_discount = đại lý.
         * - Ngược lại thì không set discount để tránh sai logic.
         */
        if ($sellingPrice && $dealerPrice && $dealerPrice < $sellingPrice) {
            return [
                'price' => $sellingPrice,
                'price_discount' => $dealerPrice,
            ];
        }

        return [
            'price' => $sellingPrice ?: $dealerPrice,
            'price_discount' => null,
        ];
    }

    private function normalizeProductForPreview(array $item, string $sessionId, string $brand): array
    {
        $images = collect($item['images'] ?? [])
            ->filter(fn ($image) => is_array($image))
            ->map(function ($image) use ($sessionId) {
                $file = $image['file'] ?? null;

                if ($file) {
                    $image['preview_url'] = route('product-import.preview-image', [
                        'sessionId' => $sessionId,
                        'path' => $file,
                    ]);
                }

                return $image;
            })
            ->values()
            ->all();

        $code = $this->firstFilled([
            $item['code'] ?? null,
            data_get($item, 'data.code'),
            data_get($item, 'raw.Mã sản phẩm'),
            data_get($item, 'raw.Ma san pham'),
            data_get($item, 'raw.Mã SP'),
            data_get($item, 'raw.Code'),
        ]);

        $name = $this->firstFilled([
            $item['name'] ?? null,
            data_get($item, 'data.name'),
            data_get($item, 'raw.Tên sản phẩm'),
            data_get($item, 'raw.Ten san pham'),
            data_get($item, 'raw.Tên SP'),
            data_get($item, 'raw.Name'),
        ]);

        $specifications = $this->firstFilled([
            $item['specifications'] ?? null,
            data_get($item, 'data.specifications'),
            data_get($item, 'data.thong_so_ky_thuat'),
            data_get($item, 'raw.Thông số kỹ thuật'),
            data_get($item, 'raw.Thong so ky thuat'),
            data_get($item, 'raw.Thông số'),
            data_get($item, 'raw.Specifications'),
            data_get($item, 'raw.Specification'),
        ]);

        /*
         * Một số file như HIKVISION không có tên sản phẩm riêng.
         * Khi đó lấy dòng đầu của thông số kỹ thuật làm name.
         */
        if (blank($name) && filled($specifications)) {
            $name = $this->makeNameFromSpecifications($specifications);
        }

        $price = $this->firstFilled([
            $item['price'] ?? null,
            data_get($item, 'data.price'),
            data_get($item, 'data.don_gia_dai_ly'),
            data_get($item, 'data.gia_dai_ly'),
            data_get($item, 'raw.Giá đại lý'),
            data_get($item, 'raw.Gia dai ly'),
            data_get($item, 'raw.Đơn giá đại lý'),
        ]);

        $retailPrice = $this->firstFilled([
            $item['retail_price'] ?? null,
            data_get($item, 'data.retail_price'),
            data_get($item, 'data.don_gia_ban_le'),
            data_get($item, 'data.gia_ban_le'),
            data_get($item, 'data.gia_ban'),
            data_get($item, 'raw.Giá bán lẻ'),
            data_get($item, 'raw.Gia ban le'),
            data_get($item, 'raw.Đơn giá bán lẻ'),
            data_get($item, 'raw.Giá bán'),
        ]);

        $warranty = $this->firstFilled([
            $item['warranty'] ?? null,
            data_get($item, 'data.warranty'),
            data_get($item, 'data.bao_hanh'),
            data_get($item, 'data.bh'),
            data_get($item, 'raw.Bảo hành'),
            data_get($item, 'raw.Bao hanh'),
            data_get($item, 'raw.BH'),
        ]);

        $status = $this->firstFilled([
            $item['status'] ?? null,
            data_get($item, 'data.status'),
            data_get($item, 'data.tinh_trang'),
            data_get($item, 'raw.Tình trạng'),
            data_get($item, 'raw.Tinh trang'),
            data_get($item, 'raw.Status'),
        ]);

        $category = $this->firstFilled([
            $item['category'] ?? null,
            data_get($item, 'data.category'),
            data_get($item, 'data.system'),
            data_get($item, 'raw.Danh mục'),
            data_get($item, 'raw.Danh muc'),
            data_get($item, 'raw.Category'),
            data_get($item, 'raw.Hệ thống'),
        ]);

        return [
            'code' => $this->cleanValue($code),
            'name' => $this->cleanValue($name),
            'brand' => $item['brand'] ?? $brand,
            'sheet' => $this->cleanValue($item['sheet'] ?? null),
            'category' => $this->cleanValue($category),

            'price' => $this->normalizeNumber($price),
            'retail_price' => $this->normalizeNumber($retailPrice),

            'warranty' => $this->cleanValue($warranty),
            'status' => $this->cleanValue($status),
            'specifications' => $this->cleanValue($specifications),

            'images' => $images,
            'raw' => $item,
        ];
    }

    private function findProductsJson(string $basePath): ?string
    {
        foreach (File::allFiles($basePath) as $file) {
            if ($file->getFilename() === 'products.json') {
                return $file->getRealPath();
            }
        }

        return null;
    }

    private function firstFilled(array $values): mixed
    {
        foreach ($values as $value) {
            if (is_string($value)) {
                $value = trim($value);
            }

            if ($value !== null && $value !== '') {
                return $value;
            }
        }

        return null;
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

    private function normalizeNumber(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return (int) round((float) $value);
        }

        $value = (string) $value;

        /*
         * Xử lý dạng:
         * 1.200.000 đ
         * 1,200,000
         * 1200000
         */
        $number = preg_replace('/[^\d]/', '', $value);

        if ($number === '') {
            return null;
        }

        return (int) $number;
    }

    private function makeNameFromSpecifications(string $specifications): ?string
    {
        $lines = preg_split('/\r\n|\r|\n/', $specifications);

        foreach ($lines as $line) {
            $line = trim($line);
            $line = ltrim($line, "*-• \t");

            if ($line !== '') {
                return Str::limit($line, 180, '');
            }
        }

        return null;
    }

    private function sanitizeRelativePath(string $path): string
    {
        $path = str_replace('\\', '/', $path);
        $path = str_replace(['../', '..\\'], '', $path);
        $path = ltrim($path, '/');

        return $path;
    }

    private function makeShortDescription(array $item, string $brand): ?string
    {
        $name = $item['name'] ?? null;
        $code = $item['code'] ?? null;
        $warranty = $item['warranty'] ?? null;

        if (! $name) {
            return null;
        }

        $parts = [];

        if ($code) {
            $parts[] = "Sản phẩm {$code}";
        }

        $parts[] = $name;

        if ($warranty) {
            $parts[] = "Bảo hành {$warranty}";
        }

        return implode('. ', $parts) . '.';
    }

    private function makeDescription(array $item, string $brand): ?string
    {
        $name = $item['name'] ?? null;
        $code = $item['code'] ?? null;
        $category = $item['category'] ?? null;
        $warranty = $item['warranty'] ?? null;
        $specifications = $item['specifications'] ?? null;

        if (! $name) {
            return null;
        }

        $html = [];

        $html[] = '<p><strong>' . e($name) . '</strong>'
            . ' là sản phẩm thuộc thương hiệu <strong>' . e(Str::headline($brand)) . '</strong>'
            . ($category ? ', nhóm <strong>' . e($category) . '</strong>' : '')
            . '.</p>';

        if ($code) {
            $html[] = '<p><strong>Mã sản phẩm:</strong> ' . e($code) . '</p>';
        }

        if ($warranty) {
            $html[] = '<p><strong>Bảo hành:</strong> ' . e($warranty) . '</p>';
        }

        if ($specifications) {
            $html[] = '<h3>Thông số kỹ thuật</h3>';
            $html[] = '<div style="white-space: pre-line;">' . e($specifications) . '</div>';
        }

        return implode("\n", $html);
    }

    private function filterColumns(string $modelClass, array $data): array
    {
        $model = new $modelClass();
        $table = $model->getTable();

        return collect($data)
            ->filter(function ($value, $column) use ($table) {
                return Schema::hasColumn($table, $column);
            })
            ->all();
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
            'item' => $item,
        ]);
    }
}
}