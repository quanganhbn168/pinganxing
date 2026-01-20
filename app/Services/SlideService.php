<?php

namespace App\Services;

use App\Contracts\MediaServiceContract;
use App\Models\Slide;
use App\Enums\SliderType; // <--- Import Enum
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class SlideService
{
    /** * Cấu hình xử lý ảnh slide.
     * Lưu ý: Nếu mỗi 'type' cần kích thước ảnh khác nhau, 
     * bạn cần viết logic động để chọn config thay vì const cố định này.
     */
    private const IMAGE_CONFIG = [
        'main'     => ['width' => 1920, 'height' => 600, 'fit' => true],
        'variants' => [
            'thumbnail' => ['width' => 300, 'height' => 94, 'fit' => true],
        ],
        'quality'  => 85,
    ];

    public function __construct(
        protected MediaServiceContract $mediaService
    ) {}

    /**
     * Danh sách slide + filter (keyword, status, type).
     */
    public function list(Request $request): array
    {
        $perPage = (int) $request->integer('per_page', 20);

        $slides = Slide::query()
            // Filter: Keyword
            ->when($request->filled('keyword'), function ($q) use ($request) {
                $kw = trim((string) $request->get('keyword'));
                $q->where(function ($qq) use ($kw) {
                    $qq->where('title', 'LIKE', "%{$kw}%")
                       ->orWhere('link',  'LIKE', "%{$kw}%");
                });
            })
            // Filter: Status
            ->when($request->filled('status') || $request->get('status') === '0', function ($q) use ($request) {
                $q->where('status', (int) $request->get('status'));
            })
            // Filter: Type (Bổ sung phần này)
            ->when($request->filled('type'), function ($q) use ($request) {
                $q->where('type', $request->get('type'));
            })
            ->orderBy('position') // Sắp xếp theo thứ tự hiển thị
            ->orderByDesc('id')
            ->paginate($perPage);

        return [$slides];
    }

    /**
     * Tạo slide mới.
     */
    public function create(array $data): Slide
    {
        // Map input paths to model columns
        $data['image'] = $data['image_original_path'] ?? null;

        // $data['type'] sẽ được tự động lưu nhờ $fillable và Casting trong Model
        $slideData = Arr::except($data, ['image_original_path']);

        $slide = Slide::create($slideData);

        return $slide;
    }

    /**
     * Cập nhật slide.
     */
    public function update(Slide $slide, array $data): Slide
    {
        // Map input paths to model columns
        if (array_key_exists('image_original_path', $data)) {
            $data['image'] = $data['image_original_path'];
        }

        $slideData = Arr::except($data, ['image_original_path']);

        $slide->update($slideData);

        return $slide;
    }

    /**
     * Xoá slide
     */
    public function delete(Slide $slide): void
    {
        foreach ($slide->images as $image) {
            $this->mediaService->deleteProcessedImages($image);
        }

        $slide->images()->delete();
        $slide->delete();
    }
}