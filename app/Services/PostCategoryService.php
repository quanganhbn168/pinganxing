<?php

namespace App\Services;

use App\Models\PostCategory;
use App\Contracts\MediaServiceContract;
use Illuminate\Support\Arr;

class PostCategoryService
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
        return PostCategory::with('parent')->latest()->paginate(20);
    }

    public function create(array $data): PostCategory
    {
        $categoryData = Arr::except($data, ['image_original_path', 'banner_original_path']);
        $category = PostCategory::create($categoryData);

        // Xử lý ảnh đại diện
        $this->mediaService->updateMedia(
            $category,
            $data['image_original_path'] ?? null,
            'post_categories', 
            self::CATEGORY_IMAGE_CONFIG,
            fn($imgData) => $category->setMainImage($imgData), 
            null, 
            'ảnh danh mục bài viết' 
        );

        // Xử lý banner
        $this->mediaService->updateMedia(
            $category,
            $data['banner_original_path'] ?? null,
            'post_categories/banner', 
            self::BANNER_IMAGE_CONFIG,
            fn($imgData) => $category->setBannerImage($imgData), 
            null, 
            'banner danh mục bài viết' 
        );

        return $category->load(['parent', 'images']);
    }

    public function update(PostCategory $postCategory, array $data): PostCategory
    {
        $categoryData = Arr::except($data, ['image_original_path', 'banner_original_path']);
        $postCategory->update($categoryData);

        // Xử lý ảnh đại diện
        $this->mediaService->updateMedia(
            $postCategory,
            $data['image_original_path'] ?? null,
            'post_categories',
            self::CATEGORY_IMAGE_CONFIG,
            fn($imgData) => $postCategory->setMainImage($imgData),
            fn() => $postCategory->mainImage(), 
            'ảnh danh mục bài viết'
        );

        // Xử lý banner
        $this->mediaService->updateMedia(
            $postCategory,
            $data['banner_original_path'] ?? null,
            'post_categories/banner',
            self::BANNER_IMAGE_CONFIG,
            fn($imgData) => $postCategory->setBannerImage($imgData),
            fn() => $postCategory->bannerImage(), 
            'banner danh mục bài viết'
        );

        return $postCategory->load(['parent', 'images']);
    }

    public function delete(PostCategory $postCategory): void
    {
        $images = $postCategory->images()->get(); 
        foreach ($images as $image) {
            $this->mediaService->deleteProcessedImages($image);
            $image->delete(); 
        }

        $postCategory->delete();
    }
}