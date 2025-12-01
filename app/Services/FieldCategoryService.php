<?php

namespace App\Services;

use App\Models\FieldCategory;
use App\Contracts\MediaServiceContract;
use Illuminate\Support\Arr;

class FieldCategoryService
{
    protected MediaServiceContract $mediaService;

    private const CATEGORY_IMAGE_CONFIG = [
        'main' => ['width' => 400],
        'variants' => ['thumbnail' => ['width' => 150, 'height' => 150, 'fit' => true]],
        'quality' => 85,
        'format' => 'webp'
    ];

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
        return FieldCategory::with('parent')->latest()->paginate(20);
    }

    public function create(array $data): FieldCategory
    {
        $categoryData = Arr::except($data, ['image_original_path', 'banner_original_path']);
        $category = FieldCategory::create($categoryData);

        // Xử lý ảnh đại diện
        $this->mediaService->updateMedia(
            $category,
            $data['image_original_path'] ?? null,
            'field_categories', 
            self::CATEGORY_IMAGE_CONFIG,
            fn($imgData) => $category->setMainImage($imgData), 
            null, 
            'ảnh danh mục lĩnh vực' 
        );

        // Xử lý banner
        $this->mediaService->updateMedia(
            $category,
            $data['banner_original_path'] ?? null,
            'field_categories/banner', 
            self::BANNER_IMAGE_CONFIG,
            fn($imgData) => $category->setBannerImage($imgData), 
            null, 
            'banner danh mục lĩnh vực' 
        );

        return $category->load(['parent', 'images']);
    }

    public function update(FieldCategory $fieldCategory, array $data): FieldCategory
    {
        $categoryData = Arr::except($data, ['image_original_path', 'banner_original_path']);
        $fieldCategory->update($categoryData);

        // Xử lý ảnh đại diện
        $this->mediaService->updateMedia(
            $fieldCategory,
            $data['image_original_path'] ?? null,
            'field_categories',
            self::CATEGORY_IMAGE_CONFIG,
            fn($imgData) => $fieldCategory->setMainImage($imgData),
            fn() => $fieldCategory->mainImage(), 
            'ảnh danh mục lĩnh vực'
        );

        // Xử lý banner
        $this->mediaService->updateMedia(
            $fieldCategory,
            $data['banner_original_path'] ?? null,
            'field_categories/banner',
            self::BANNER_IMAGE_CONFIG,
            fn($imgData) => $fieldCategory->setBannerImage($imgData),
            fn() => $fieldCategory->bannerImage(), 
            'banner danh mục lĩnh vực'
        );

        return $fieldCategory->load(['parent', 'images']);
    }

    public function delete(FieldCategory $fieldCategory): void
    {
        $images = $fieldCategory->images()->get(); 
        foreach ($images as $image) {
            $this->mediaService->deleteProcessedImages($image);
            $image->delete(); 
        }

        $fieldCategory->delete();
    }
}