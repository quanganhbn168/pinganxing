<?php

namespace App\Services;

use App\Models\PostCategory;
use App\Contracts\MediaServiceContract;
use Illuminate\Support\Arr;
use App\Services\SlugService;

class PostCategoryService
{
    protected MediaServiceContract $mediaService;

    /**
     * Cấu hình cho ảnh đại diện category.
     */
    private const CATEGORY_IMAGE_CONFIG = [
        'main' => ['width' => 800, 'height' => 450, 'fit' => true],
        'variants' => ['thumbnail' => ['width' => 320, 'height' => 180, 'fit' => true]],
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

    protected SlugService $slugService;

    public function __construct(MediaServiceContract $mediaService, SlugService $slugService)
    {
        $this->mediaService = $mediaService;
        $this->slugService = $slugService;
    }

    public function getAll()
    {
        return PostCategory::with('parent')->latest()->paginate(20);
    }

    public function create(array $data): PostCategory
    {
        // Map media inputs to columns
        $data['image'] = $data['image_original_path'] ?? null;
        $data['banner'] = $data['banner_original_path'] ?? null;

        $categoryData = Arr::except($data, ['image_original_path', 'banner_original_path']);
        $category = PostCategory::create($categoryData);

        // Sync Morph Slug
        $this->slugService->upsert($category, $category->name);

        return $category->load(['parent', 'images']);
    }

    public function update(PostCategory $postCategory, array $data): PostCategory
    {
        // Map media inputs to columns
        if (array_key_exists('image_original_path', $data)) {
            $data['image'] = $data['image_original_path'];
        }
        if (array_key_exists('banner_original_path', $data)) {
            $data['banner'] = $data['banner_original_path'];
        }

        $categoryData = Arr::except($data, ['image_original_path', 'banner_original_path']);
        $postCategory->update($categoryData);

        // Sync Morph Slug
        $this->slugService->upsert($postCategory, $postCategory->name);

        return $postCategory->load(['parent', 'images']);
    }

    public function delete(PostCategory $postCategory): void
    {
        $images = $postCategory->images()->get(); 
        foreach ($images as $image) {
            $this->mediaService->deleteProcessedImages($image);
            $image->delete(); 
        }

        // Delete Morph Slug
        $postCategory->slugData()->delete();

        $postCategory->delete();
    }

    /**
     * Xóa hàng loạt có dọn dẹp ảnh.
     */
    public function bulkDelete(array $ids): void
    {
        $categories = PostCategory::whereIn('id', $ids)->get();
        foreach ($categories as $category) {
            $this->delete($category);
        }
    }
}