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
    ];

    /** Ảnh banner 1920x700 */
    private const BANNER_IMAGE_CONFIG = [
        'main'     => ['width' => 1920, 'height' => 700, 'fit' => true],
        'variants' => [
            'thumbnail' => ['width' => 150, 'height' => 150, 'fit' => true],
        ],
        'quality'  => 85,
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
        // Map input paths to model columns
        $data['image'] = $data['image_original_path'] ?? null;
        $data['banner'] = $data['banner_original_path'] ?? null;

        $introData = Arr::except($data, ['image_original_path', 'banner_original_path']);

        $intro = Intro::create($introData);

        return $intro;
    }

    /**
     * Cập nhật Intro + media (nếu có path mới).
     */
    public function update(Intro $intro, array $data): Intro
    {
        // Map input paths to model columns
        if (array_key_exists('image_original_path', $data)) {
            $data['image'] = $data['image_original_path'];
        }
        if (array_key_exists('banner_original_path', $data)) {
            $data['banner'] = $data['banner_original_path'];
        }

        $introData = Arr::except($data, ['image_original_path', 'banner_original_path']);

        $intro->update($introData);

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
