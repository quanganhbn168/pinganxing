<?php

namespace App\Services;

use App\Models\Field;
use App\Models\FieldCategory;
use App\Contracts\MediaServiceContract;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;

class FieldService
{
    protected MediaServiceContract $mediaService;

    /**
     * Cấu hình cho ảnh đại diện field.
     */
    private const FIELD_IMAGE_CONFIG = [
        'main' => ['width' => 800],
        'variants' => ['thumbnail' => ['width' => 300, 'height' => 200, 'fit' => true]],
        'quality' => 85,
        'format' => 'webp'
    ];

    public function __construct(MediaServiceContract $mediaService)
    {
        $this->mediaService = $mediaService;
    }
    /**
     * Danh sách bài viết có lọc + phân trang.
     * Trả về [LengthAwarePaginator $posts, array $filterCategories]
     */
    public function list(Request $request): array
    {
        $perPage = (int) $request->integer('per_page', 20);

        $fields = Field::with('category')
            ->when($request->filled('keyword'), function ($q) use ($request) {
                $kw = trim((string) $request->get('keyword'));
                $q->where(function ($qq) use ($kw) {
                    $qq->where('title', 'LIKE', "%{$kw}%")
                       ->orWhere('slug', 'LIKE', "%{$kw}%");
                });
            })
            ->when($request->filled('category_id'), fn($q) =>
                $q->where('field_category_id', (int) $request->get('category_id')))
            ->when($request->filled('status') || $request->get('status') === '0', fn($q) =>
                $q->where('status', (int) $request->get('status')))
            ->when($request->filled('is_home') || $request->get('is_home') === '0', fn($q) =>
                $q->where('is_home', (int) $request->get('is_home')))
            ->latest('id')
            ->paginate($perPage);

        $filterCategories = $this->getFilterCategories();

        return [$fields, $filterCategories];
    }

    /** Dùng cho filter dropdown ở index */
    public function getFilterCategories(): array
    {
        return FieldCategory::orderBy('name')->pluck('name', 'id')->toArray();
    }


    public function getAll()
    {
        return Field::with('category')->latest()->paginate(20);
    }

    public function create(array $data): Field
    {
        $fieldData = Arr::except($data, ['image_original_path']);
        $field = Field::create($fieldData);

        // Xử lý ảnh đại diện
        $this->mediaService->updateMedia(
            $field,
            $data['image_original_path'] ?? null,
            'fields', 
            self::FIELD_IMAGE_CONFIG,
            fn($imgData) => $field->setMainImage($imgData), 
            null, 
            'ảnh đại diện lĩnh vực' 
        );

        return $field->load(['category', 'images']);
    }

    public function update(Field $field, array $data): Field
    {
        $fieldData = Arr::except($data, ['image_original_path']);
        $field->update($fieldData);

        // Xử lý ảnh đại diện
        $this->mediaService->updateMedia(
            $field,
            $data['image_original_path'] ?? null,
            'fields',
            self::FIELD_IMAGE_CONFIG,
            fn($imgData) => $field->setMainImage($imgData),
            fn() => $field->mainImage(), 
            'ảnh đại diện lĩnh vực'
        );

        return $field->load(['category', 'images']);
    }

    public function delete(Field $field): void
    {
        $images = $field->images()->get(); 
        foreach ($images as $image) {
            $this->mediaService->deleteProcessedImages($image);
            $image->delete(); 
        }

        // Xóa slug nếu có
        if ($field->slug) {
            $field->slug->delete();
        }

        $field->delete();
    }
}