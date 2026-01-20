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
        // Map media inputs to columns
        $data['image'] = $data['image_original_path'] ?? null;
        $data['banner'] = $data['banner_original_path'] ?? null;

        $categoryData = Arr::except($data, ['image_original_path', 'banner_original_path']);
        $category = FieldCategory::create($categoryData);

        return $category->load(['parent', 'images']);
    }

    public function update(FieldCategory $fieldCategory, array $data): FieldCategory
    {
        // Map media inputs to columns
        if (array_key_exists('image_original_path', $data)) {
            $data['image'] = $data['image_original_path'];
        }
        if (array_key_exists('banner_original_path', $data)) {
            $data['banner'] = $data['banner_original_path'];
        }

        $categoryData = Arr::except($data, ['image_original_path', 'banner_original_path']);
        $fieldCategory->update($categoryData);

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