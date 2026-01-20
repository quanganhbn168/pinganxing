<?php
namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use App\Contracts\MediaServiceContract;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class ProductService
{
    private const MAIN_IMAGE_CONFIG = [
        'main' => ['width' => 1024],
        'variants' => [
            'thumbnail' => ['width' => 150, 'height' => 150, 'fit' => true]
        ],
        'quality' => 90,
    ];

    private const GALLERY_IMAGE_CONFIG = [
        'main' => ['width' => 1024],
        'variants' => [
            'thumbnail' => ['width' => 150, 'height' => 150, 'fit' => true]
        ],
        'quality' => 90,
    ];

    public function __construct(
        protected MediaServiceContract $mediaService
    ) {}

    public function getAll()
    {
        return Product::with('category')->latest()->paginate(20);
    }

    public function getCategoryOptions()
    {
        return Category::pluck('name', 'id')->toArray();
    }

    /**
     * Tạo sản phẩm mới + xử lý ảnh
     */
    public function create(array $data): Product
    {
        // Map input paths to model columns
        $data['image'] = $data['image_original_path'] ?? null;

        $productData = Arr::except($data, [
            'image_original_path',
            'gallery_original_paths'
        ]);

        $productData['slug'] = Str::slug($data['name']);
        $product = Product::create($productData);

        // Gallery
        $this->updateGallery($product, $data['gallery_original_paths'] ?? null);

        return $product;
    }

    /**
     * Cập nhật sản phẩm + xử lý ảnh
     */
    public function update(Product $product, array $data): Product
    {
        // Map input paths to model columns
        if (array_key_exists('image_original_path', $data)) {
            $data['image'] = $data['image_original_path'];
        }

        $productData = Arr::except($data, [
            'image_original_path',
            'gallery_original_paths'
        ]);

        $product->update($productData);

        // Cập nhật gallery
        $this->updateGallery($product, $data['gallery_original_paths'] ?? null);

        return $product;
    }

public function list(Request $request): array
{
    $perPage = (int) $request->integer('per_page', 20);

    $products = Product::with('category')
        ->when($request->filled('keyword'), function ($q) use ($request) {
            $kw = trim($request->get('keyword'));
            $q->where(function ($qq) use ($kw) {
                $qq->where('name', 'LIKE', "%{$kw}%")
                   ->orWhere('slug', 'LIKE', "%{$kw}%")
                   ->orWhere('code', 'LIKE', "%{$kw}%"); // nếu có code
            });
        })
        ->when($request->filled('category_id'), fn($q) => $q->where('category_id', $request->get('category_id')))
        ->when($request->filled('status') || $request->get('status') === '0', fn($q) => $q->where('status', (int)$request->get('status')))
        ->when($request->filled('is_home') || $request->get('is_home') === '0', fn($q) => $q->where('is_home', (int)$request->get('is_home')))
        ->latest('id')
        ->paginate($perPage);

    $filterCategories = $this->getFilterCategories();

    return [$products, $filterCategories];
}

public function getFilterCategories(): array
{
    return Category::orderBy('name')->pluck('name', 'id')->toArray();
}

    /**
     * Đồng bộ gallery (xoá cũ → thêm mới)
     */
    private function updateGallery(Product $product, ?string $rawJson): void
    {
        if (!$rawJson) {
            return;
        }

        $paths = json_decode($rawJson, true) ?? [];

        // Xoá ảnh cũ
        foreach ($product->gallery as $img) {
            $this->mediaService->deleteProcessedImages($img);
            $img->delete();
        }

        // Tạo mới
        foreach ($paths as $index => $path) {
            $imageData = $this->mediaService->processAndPrepareData(
                $path,
                'products/gallery',
                self::GALLERY_IMAGE_CONFIG
            );

            if ($imageData) {
                $product->addGalleryImage($imageData, $index);
            }
        }
    }

    /**
     * Xoá sản phẩm + media liên quan
     */
    public function delete(Product $product): void
    {
        foreach ($product->images as $img) {
            $this->mediaService->deleteProcessedImages($img);
        }

        $product->images()->delete();
        $product->delete();
    }
}
