<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

trait UploadImageTrait
{
    /**
     * Upload ảnh (hỗ trợ raster & SVG) với resize, WebP, watermark — tương thích Intervention Image v3.
     *
     * @param UploadedFile $file
     * @param string       $folder         Thư mục lưu (trong disk 'public')
     * @param int|null     $resizeWidth    Chiều rộng max/đích
     * @param int|null     $resizeHeight   Chiều cao max/đích
     * @param bool         $convertToWebp  Convert sang WebP
     * @param string       $watermarkPath  Đường dẫn public_path() tới watermark (tùy chọn)
     * @param bool         $keepRatio      true: scaleDown giữ tỉ lệ, false: resize ép khuôn (khi có đủ w,h)
     *
     * @return string      Public path dạng 'storage/...'
     */
    public function uploadImage(
        UploadedFile $file,
        string $folder = 'uploads/images',
        ?int $resizeWidth = 1920,
        ?int $resizeHeight = null,
        bool $convertToWebp = true,
        string $watermarkPath = '',
        bool $keepRatio = true
    ): string {
        $disk = 'public';
        $ext  = strtolower($file->getClientOriginalExtension() ?: '');
        $mime = strtolower($file->getClientMimeType() ?: '');
        $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $slug = Str::slug($name) ?: 'image';
        $uniq = uniqid();

        // ===== SVG: lưu nguyên file, không xử lý =====
        if ($ext === 'svg' || str_contains($mime, 'svg')) {
            $filename = "{$uniq}-{$slug}.svg";
            $path     = "{$folder}/{$filename}";
            $file->storeAs($folder, $filename, $disk);
            return "storage/{$path}";
        }

        // ===== Raster: xử lý v3 =====
        $manager = new ImageManager(new Driver());
        $image   = $manager->read($file->getPathname())->orient();

        // Resize
        if ($resizeWidth || $resizeHeight) {
            if ($keepRatio) {
                // scaleDown giữ tỉ lệ, không phóng vượt kích thước đưa vào
                $image->scaleDown(
                    width:  $resizeWidth  ?? null,
                    height: $resizeHeight ?? null
                );
            } else {
                // Ép đúng kích thước khi có đủ 2 chiều
                if ($resizeWidth && $resizeHeight) {
                    $image->resize($resizeWidth, $resizeHeight);
                }
            }
        }

        // Watermark (tuỳ chọn)
        if ($watermarkPath && is_file(public_path($watermarkPath))) {
            $wm = $manager->read(public_path($watermarkPath));
            // bottom-right, offset (10,10)
            $image->place($wm, 'bottom-right', 10, 10);
        }

        // Encode & tên file cuối
        if ($convertToWebp) {
            $filename = "{$uniq}-{$slug}.webp";
            $binary   = $image->toWebp(85);
        } else {
            // Giữ/ext hợp lệ khi không convert
            $finalExt = in_array($ext, ['jpg','jpeg','png','webp']) ? ($ext === 'jpeg' ? 'jpg' : $ext) : 'jpg';
            $filename = "{$uniq}-{$slug}.{$finalExt}";
            switch ($finalExt) {
    case 'png':
        // PNG không có quality, chỉ interlace (bool)
        $binary = $image->toPng(); // hoặc ->toPng(interlaced: true)
        break;

    case 'webp':
        $binary = $image->toWebp(quality: 85);
        break;

    default: // jpg
        $binary = $image->toJpeg(quality: 85);
        break;
}

        }

        $path = "{$folder}/{$filename}";
        Storage::disk($disk)->put($path, (string) $binary);

        return "storage/{$path}";
    }

    /**
     * Xóa ảnh đã lưu (public path).
     */
    public function deleteImage(?string $publicPath): void
    {
        if (!$publicPath || !Str::startsWith($publicPath, 'storage/')) {
            return;
        }
        $rel = Str::replaceFirst('storage/', '', $publicPath);
        if (Storage::disk('public')->exists($rel)) {
            Storage::disk('public')->delete($rel);
        }
    }
}
