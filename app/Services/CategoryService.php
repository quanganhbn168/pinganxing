<?php

namespace App\Services;

use App\Models\Category;
use App\Contracts\MediaServiceContract;
use Illuminate\Support\Arr;

class CategoryService
{
    protected MediaServiceContract $mediaService;

    /**
     * Cấu hình cho ảnh đại diện category.
     */
    private const CATEGORY_IMAGE_CONFIG = [
        'main' => ['width' => 400],
        'variants' => ['thumbnail' => ['width' => 150, 'height' => 150, 'fit' => true]],
        'quality' => 85,
        'format' => 'webp'
    ];

    /**
     * Cấu hình cho banner category.
     */
    private const BANNER_IMAGE_CONFIG = [
        'main' => ['width' => 1920, 'height' => 500, 'fit' => true],
        'variants' => ['thumbnail' => ['width' => 300, 'height' => 100, 'fit' => true]],
        'quality' => 85,
        'format' => 'webp'
    ];

    public function __construct(MediaServiceContract $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    public function getAll()
    {
        return Category::with('parent')->latest()->paginate(20);
    }

    public function create(array $data): Category
    {
        $categoryData = Arr::except($data, ['image_original_path', 'banner_original_path']);
        $category = Category::create($categoryData);

        // Xử lý ảnh đại diện
        $this->mediaService->updateMedia(
            $category,
            $data['image_original_path'] ?? null,
            'categories', 
            self::CATEGORY_IMAGE_CONFIG,
            fn($imgData) => $category->setMainImage($imgData), 
            null, 
            'ảnh danh mục' 
        );

        // Xử lý banner
        $this->mediaService->updateMedia(
            $category,
            $data['banner_original_path'] ?? null,
            'categories/banner', 
            self::BANNER_IMAGE_CONFIG,
            fn($imgData) => $category->setBannerImage($imgData), 
            null, 
            'banner danh mục' 
        );

        return $category->load(['parent', 'images']);
    }

    public function update(Category $category, array $data): Category
    {
        $categoryData = Arr::except($data, ['image_original_path', 'banner_original_path']);
        $category->update($categoryData);

        // Xử lý ảnh đại diện
        $this->mediaService->updateMedia(
            $category,
            $data['image_original_path'] ?? null,
            'categories',
            self::CATEGORY_IMAGE_CONFIG,
            fn($imgData) => $category->setMainImage($imgData),
            fn() => $category->mainImage(), 
            'ảnh danh mục'
        );

        // Xử lý banner
        $this->mediaService->updateMedia(
            $category,
            $data['banner_original_path'] ?? null,
            'categories/banner',
            self::BANNER_IMAGE_CONFIG,
            fn($imgData) => $category->setBannerImage($imgData),
            fn() => $category->bannerImage(), 
            'banner danh mục'
        );

        return $category->load(['parent', 'images']);
    }

    public function delete(Category $category): void
    {
        $images = $category->images()->get(); 
        foreach ($images as $image) {
            $this->mediaService->deleteProcessedImages($image);
            $image->delete(); 
        }

        $category->delete();
    }
}