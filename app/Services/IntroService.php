<?php

namespace App\Services;

use App\Contracts\MediaServiceContract;
use App\Models\Intro;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class IntroService
{
    /** Ảnh đại diện */
    private const MAIN_IMAGE_CONFIG = [
        'main'     => ['width' => 1024],
        'variants' => [
            'thumbnail' => ['width' => 150, 'height' => 150, 'fit' => true],
        ],
        'quality'  => 85,
        'format'   => 'webp',
    ];

    /** Ảnh banner 1920x700 */
    private const BANNER_IMAGE_CONFIG = [
        'main'     => ['width' => 1920, 'height' => 700, 'fit' => true],
        'variants' => [
            'thumbnail' => ['width' => 150, 'height' => 150, 'fit' => true],
        ],
        'quality'  => 85,
        'format'   => 'webp',
    ];

    public function __construct(
        protected MediaServiceContract $mediaService
    ) {}

    /**
     * Danh sách + filter keyword/status + phân trang
     * Trả về mảng 1 phần tử: [$intros] để khớp Controller.
     */
    public function list(Request $request): array
    {
        $perPage = (int) $request->integer('per_page', 20);

        $intros = Intro::query()
            ->when($request->filled('keyword'), function ($q) use ($request) {
                $kw = trim((string) $request->get('keyword'));
                $q->where(function ($qq) use ($kw) {
                    $qq->where('title', 'LIKE', "%{$kw}%")
                       ->orWhere('slug',  'LIKE', "%{$kw}%");
                });
            })
            ->when($request->filled('status') || $request->get('status') === '0', function ($q) use ($request) {
                $q->where('status', (int) $request->get('status'));
            })
            ->latest('id')
            ->paginate($perPage);

        return [$intros];
    }

    /**
     * Tạo Intro + media (image/banner).
     * $data gồm field thường + image_original_path, banner_original_path (từ media-input).
     */
    public function create(array $data): Intro
    {
        $introData = Arr::except($data, ['image_original_path', 'banner_original_path']);

        $intro = Intro::create($introData);

        // Ảnh đại diện
        $this->mediaService->updateMedia(
            $intro,
            $data['image_original_path'] ?? null,
            'intros', // thư mục
            self::MAIN_IMAGE_CONFIG,
            fn ($img) => $intro->setMainImage($img),
            null,
            'Ảnh đại diện'
        );

        // Ảnh banner
        $this->mediaService->updateMedia(
            $intro,
            $data['banner_original_path'] ?? null,
            'intros/banner',
            self::BANNER_IMAGE_CONFIG,
            fn ($img) => $intro->setBannerImage($img),
            null,
            'Ảnh banner'
        );

        return $intro;
    }

    /**
     * Cập nhật Intro + media (nếu có path mới).
     */
    public function update(Intro $intro, array $data): Intro
    {
        $introData = Arr::except($data, ['image_original_path', 'banner_original_path']);

        $intro->update($introData);

        // Ảnh đại diện
        $this->mediaService->updateMedia(
            $intro,
            $data['image_original_path'] ?? null,
            'intros',
            self::MAIN_IMAGE_CONFIG,
            fn ($img) => $intro->setMainImage($img),
            fn () => $intro->mainImage(), // trả về ảnh hiện tại để service biết xoá/thay
            'Ảnh đại diện'
        );

        // Ảnh banner
        $this->mediaService->updateMedia(
            $intro,
            $data['banner_original_path'] ?? null,
            'intros/banner',
            self::BANNER_IMAGE_CONFIG,
            fn ($img) => $intro->setBannerImage($img),
            fn () => $intro->bannerImage(),
            'Ảnh banner'
        );

        return $intro;
    }

    /**
     * Xoá Intro + toàn bộ media liên quan.
     */
    public function delete(Intro $intro): void
    {
        foreach ($intro->images as $image) {
            $this->mediaService->deleteProcessedImages($image);
        }

        $intro->images()->delete();
        $intro->delete();
    }
}
