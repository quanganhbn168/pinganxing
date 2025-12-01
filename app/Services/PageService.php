<?php

namespace App\Services;

use App\Models\Page;
use App\Contracts\MediaServiceContract;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class PageService
{
    protected MediaServiceContract $mediaService;

    // ... (Giữ nguyên các CONST cấu hình ảnh)
    private const MAIN_IMAGE_CONFIG = [
        'main' => ['width' => 1024],
        'variants' => ['thumbnail' => ['width' => 150, 'height' => 150, 'fit' => true]],
        'quality' => 85,
        'format' => 'webp'
    ];

    private const BANNER_IMAGE_CONFIG = [
        'main' => ['width' => 1920, 'height' => 700, 'fit' => true],
        'variants' => ['thumbnail' => ['width' => 150, 'height' => 150, 'fit' => true]],
        'quality' => 85,
        'format' => 'webp'
    ];

    public function __construct(MediaServiceContract $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    public function create(array $data): Page
    {
        // ... (Giữ nguyên hàm create)
        $pageData = Arr::except($data, ['image_original_path', 'banner_original_path']);
        $page = Page::create($pageData);

        $this->mediaService->updateMedia(
            $page,
            $data['image_original_path'] ?? null,
            'pages',
            self::MAIN_IMAGE_CONFIG,
            fn($imgData) => $page->setMainImage($imgData),
            null,
            'ảnh đại diện'
        );

        $this->mediaService->updateMedia(
            $page,
            $data['banner_original_path'] ?? null,
            'pages/banner',
            self::BANNER_IMAGE_CONFIG,
            fn($imgData) => $page->setBannerImage($imgData),
            null,
            'ảnh banner'
        );

        return $page;
    }

    /**
     * [FIX] Cập nhật Page từ mảng dữ liệu.
     * Đã sửa logic lấy ảnh để tránh lỗi Array to String
     */
    public function update(Page $page, array $data): Page
    {
        $pageId = $page->id;
        // 1. XỬ LÝ TITLE & DESCRIPTION
        // Kiểm tra xem input gửi lên là dạng mảng title[id] hay dạng đơn title
        $titles = $data['title'] ?? [];
        $title = is_array($titles) ? ($titles[$pageId] ?? $page->title) : ($titles ?: $page->title);

        $descs = $data['description'] ?? [];
        $description = is_array($descs) ? ($descs[$pageId] ?? $page->description) : ($descs ?: $page->description);

        // 2. XỬ LÝ ẢNH (QUAN TRỌNG: SỬA LỖI TẠI ĐÂY)
        // Lấy chính xác giá trị của pageId hiện tại, không fallback về cả mảng
        $rawImages = $data['image_original_path'] ?? [];
        $imagePath = is_array($rawImages) ? ($rawImages[$pageId] ?? null) : $rawImages;

        $rawBanners = $data['banner_original_path'] ?? [];
        $bannerPath = is_array($rawBanners) ? ($rawBanners[$pageId] ?? null) : $rawBanners;

        // 3. XỬ LÝ CONTENT
        $rawContent = $data['content'] ?? null;
        // Nếu content gửi dạng mảng content[id]
        if (is_array($rawContent) && isset($rawContent[$pageId])) {
            $contentRaw = $rawContent[$pageId];
        } else {
            $contentRaw = $rawContent; // Trường hợp gửi đơn
        }

        // Normalize content
        $contentToStore = null;
        if (is_string($contentRaw) && $this->looksLikeJson($contentRaw)) {
            $decoded = json_decode($contentRaw, true);
            $contentToStore = is_array($decoded) ? $decoded : ['html' => $contentRaw];
        } elseif (is_array($contentRaw)) {
            $contentToStore = $contentRaw;
        } else {
            $contentToStore = ['html' => (string)($contentRaw ?? '')];
        }

        // 4. XỬ LÝ FEATURES
        // Logic cũ của bạn dùng Arr::get khá rối, chuyển sang lấy trực tiếp cho an toàn
        $allFeatures = $data['features'] ?? [];
        // Nếu gửi dạng features[pageId] -> lấy mảng con, nếu không thì lấy chính nó
        $features = (isset($allFeatures[$pageId]) && is_array($allFeatures[$pageId]))
            ? $allFeatures[$pageId]
            : []; // Mặc định rỗng nếu không tìm thấy

        $normalizedFeatures = [];
        for ($i = 0; $i < 4; $i++) {
            $f = isset($features[$i]) && is_array($features[$i]) ? $features[$i] : [];

            $normalizedFeatures[$i] = [
                'icon' => (string)($f['icon'] ?? ''),
                'value' => (string)($f['value'] ?? ($f['number'] ?? '')),
                'title' => (string)($f['title'] ?? ''),
                'description' => (string)($f['description'] ?? ''),
            ];
        }

        // 5. CẬP NHẬT
        $updateData = [
            'title' => $title,
            'description' => $description,
            'content' => $contentToStore,
            'features' => $normalizedFeatures,
        ];

        $page->update($updateData);

        // Update Media
        // Lúc này $imagePath chắc chắn là String hoặc Null, không bao giờ là Array
        $this->mediaService->updateMedia(
            $page,
            $imagePath,
            'pages',
            self::MAIN_IMAGE_CONFIG,
            fn($imgData) => $page->setMainImage($imgData),
            fn() => $page->mainImage(), // Hàm callback lấy ảnh cũ để xóa nếu cần
            'ảnh đại diện'
        );

        $this->mediaService->updateMedia(
            $page,
            $bannerPath,
            'pages/banner',
            self::BANNER_IMAGE_CONFIG,
            fn($imgData) => $page->setBannerImage($imgData),
            fn() => $page->bannerImage(),
            'ảnh banner'
        );

        return $page;
    }

    // ... (Giữ nguyên hàm delete và looksLikeJson)
    public function delete(Page $page): void
    {
        $images = $page->images()->get();
        foreach ($images as $image) {
            $this->mediaService->deleteProcessedImages($image);
            $image->delete();
        }
        $page->delete();
    }

    protected function looksLikeJson($string): bool
    {
        if (!is_string($string)) return false;
        $string = trim($string);
        if ($string === '') return false;
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}