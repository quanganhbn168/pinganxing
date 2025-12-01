<?php

namespace App\Services;

use App\Models\ProjectCategory;
use App\Contracts\MediaServiceContract;
use Illuminate\Support\Arr;

class ProjectCategoryService
{
    protected MediaServiceContract $mediaService;

    /**
     * Cấu hình cho ảnh đại diện category.
     */
    private const CATEGORY_IMAGE_CONFIG = [
        'main' => ['width' => 800],
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
        return ProjectCategory::with('parent')->latest()->paginate(20);
    }

    public function create(array $data): ProjectCategory
    {
        $categoryData = Arr::except($data, ['image_original_path', 'banner_original_path']);
        $category = ProjectCategory::create($categoryData);

        // Xử lý ảnh đại diện
        $this->mediaService->updateMedia(
            $category,
            $data['image_original_path'] ?? null,
            'project_categories', 
            self::CATEGORY_IMAGE_CONFIG,
            fn($imgData) => $category->setMainImage($imgData), 
            null, 
            'ảnh danh mục dự án' 
        );

        // Xử lý banner
        $this->mediaService->updateMedia(
            $category,
            $data['banner_original_path'] ?? null,
            'project_categories/banner', 
            self::BANNER_IMAGE_CONFIG,
            fn($imgData) => $category->setBannerImage($imgData), 
            null, 
            'banner danh mục dự án' 
        );

        return $category->load(['parent', 'images']);
    }

    public function update(ProjectCategory $projectCategory, array $data): ProjectCategory
    {
        $categoryData = Arr::except($data, ['image_original_path', 'banner_original_path']);
        $projectCategory->update($categoryData);

        // Xử lý ảnh đại diện
        $this->mediaService->updateMedia(
            $projectCategory,
            $data['image_original_path'] ?? null,
            'project_categories',
            self::CATEGORY_IMAGE_CONFIG,
            fn($imgData) => $projectCategory->setMainImage($imgData),
            fn() => $projectCategory->mainImage(), 
            'ảnh danh mục dự án'
        );

        // Xử lý banner
        $this->mediaService->updateMedia(
            $projectCategory,
            $data['banner_original_path'] ?? null,
            'project_categories/banner',
            self::BANNER_IMAGE_CONFIG,
            fn($imgData) => $projectCategory->setBannerImage($imgData),
            fn() => $projectCategory->bannerImage(), 
            'banner danh mục dự án'
        );

        return $projectCategory->load(['parent', 'images']);
    }

    public function delete(ProjectCategory $projectCategory): void
    {
        $images = $projectCategory->images()->get(); 
        foreach ($images as $image) {
            $this->mediaService->deleteProcessedImages($image);
            $image->delete(); 
        }

        $projectCategory->delete();
    }
}