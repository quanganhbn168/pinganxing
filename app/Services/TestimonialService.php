<?php

namespace App\Services;

use App\Contracts\MediaServiceContract;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class TestimonialService
{
    /** Cấu hình xử lý ảnh testimonial 500 */
    private const IMAGE_CONFIG = [
        'main'     => ['width' => 600, 'height' => 600], // Bỏ fit: true để tránh cắt mất hình
        'variants' => [
            // Thumb gọn để hiển thị danh sách
            'thumbnail' => ['width' => 150, 'height' => 150], // Fit true ở đây ok vì thumb thường bé
        ],
        'quality'  => 85,
    ];

    public function __construct(
        protected MediaServiceContract $mediaService
    ) {}

    // App/Services/TestimonialService.php
    public function list(Request $request): array
    {
        $perPage = (int) $request->integer('per_page', 20);

        $testimonials = Testimonial::query()
            ->when($request->filled('keyword'), function ($q) use ($request) {
                $kw = trim((string) $request->get('keyword'));
                $q->where(function ($qq) use ($kw) {
                    $qq->where('name', 'LIKE', "%{$kw}%")
                       ->orWhere('content', 'LIKE', "%{$kw}%");
                });
            })
            ->when($request->filled('status') || $request->get('status') === '0', function ($q) use ($request) {
                $q->where('status', (int) $request->get('status'));
            })
            ->orderBy('position')
            ->orderByDesc('id')
            ->paginate($perPage);

        return [$testimonials];
    }


    /**
     * Tạo testimonial mới.
     * $data nhận các field thường + 'image_original_path' (từ media-input).
     */
    public function create(array $data): Testimonial
    {
        // Map input paths to model columns
        $data['image'] = $data['image_original_path'] ?? null;

        $testimonialData = Arr::except($data, ['image_original_path']);

        $testimonial = Testimonial::create($testimonialData);

        return $testimonial;
    }

    /**
     * Cập nhật testimonial.
     * Không thay ảnh nếu không truyền image_original_path.
     */
    public function update(Testimonial $testimonial, array $data): Testimonial
    {
        // Map input paths to model columns
        if (array_key_exists('image_original_path', $data)) {
            $data['image'] = $data['image_original_path'];
        }

        $testimonialData = Arr::except($data, ['image_original_path']);

        $testimonial->update($testimonialData);

        return $testimonial;
    }

    /**
     * Xoá testimonial + toàn bộ ảnh liên quan.
     */
    public function delete(Testimonial $testimonial): void
    {
        // Xoá file vật lý của mọi ảnh liên kết
        foreach ($testimonial->images as $image) {
            $this->mediaService->deleteProcessedImages($image);
        }

        $testimonial->images()->delete();
        $testimonial->delete();
    }
}
