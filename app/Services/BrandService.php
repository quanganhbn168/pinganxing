<?php

namespace App\Services;

use App\Models\Brand;
use App\Contracts\MediaServiceContract;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;

class BrandService
{
    protected MediaServiceContract $mediaService;

    /**
     * Cấu hình cho ảnh brand (logo).
     */
    private const BRAND_IMAGE_CONFIG = [
        'main' => ['width' => 250, 'height' => 250, 'fit' => true],
        'variants' => ['thumbnail' => ['width' => 100, 'height' => 100, 'fit' => true]],
        'quality' => 85,
        'format' => 'webp'
    ];

    public function __construct(MediaServiceContract $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    public function getAll()
    {
        return Brand::latest()->paginate(20);
    }

    public function create(array $data): Brand
    {
        $brandData = Arr::except($data, ['image_original_path']);
        
        // Tạo slug nếu chưa có
        if (empty($brandData['slug']) && !empty($brandData['name'])) {
            $brandData['slug'] = $this->generateUniqueSlug($brandData['name']);
        }

        $brand = Brand::create($brandData);

        // Xử lý ảnh brand
        $this->mediaService->updateMedia(
            $brand,
            $data['image_original_path'] ?? null,
            'brands', 
            self::BRAND_IMAGE_CONFIG,
            fn($imgData) => $brand->setMainImage($imgData), 
            null, 
            'logo thương hiệu' 
        );

        return $brand->load('images');
    }

    public function update(Brand $brand, array $data): bool
    {
        $brandData = Arr::except($data, ['image_original_path']);
        
        // Tạo slug mới nếu name thay đổi
        if (isset($data['name']) && $brand->name !== $data['name'] && empty($data['slug'])) {
            $brandData['slug'] = $this->generateUniqueSlug($data['name'], $brand->id);
        }

        $updateResult = $brand->update($brandData);

        // Xử lý ảnh brand
        $this->mediaService->updateMedia(
            $brand,
            $data['image_original_path'] ?? null,
            'brands',
            self::BRAND_IMAGE_CONFIG,
            fn($imgData) => $brand->setMainImage($imgData),
            fn() => $brand->mainImage(), 
            'logo thương hiệu'
        );

        return $updateResult;
    }

    public function delete(Brand $brand): ?bool
    {
        // Xóa ảnh liên quan
        $images = $brand->images()->get(); 
        foreach ($images as $image) {
            $this->mediaService->deleteProcessedImages($image);
            $image->delete(); 
        }

        return $brand->delete();
    }

    /**
     * Tạo slug duy nhất.
     */
    private function generateUniqueSlug(string $name, ?int $exceptId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while (Brand::where('slug', $slug)
            ->when($exceptId, function ($query) use ($exceptId) {
                return $query->where('id', '!=', $exceptId);
            })
            ->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }
}