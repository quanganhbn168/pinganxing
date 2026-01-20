<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

trait UploadImageTrait
{
    /**
     * Xử lý input ảnh từ Form:
     * - Nếu là file upload (legacy): Upload và trả về path mới.
     * - Nếu là string (LFM): Trả về nguyên path.
     * - Xử lý xóa ảnh cũ nếu có thay đổi.
     * 
     * @param Request $request
     * @param string $field Tên field input
     * @param string|null $currentPath Path ảnh hiện tại (để xóa nếu thay đổi)
     * @param string $folder Folder upload (nếu là file mới)
     * @return string|null Path cuối cùng
     */
    public function processImageInput(Request $request, string $field, ?string $currentPath = null, string $folder = 'uploads/images', bool $convertToWebp = true): ?string
    {
        // 1. Nếu có file upload (ưu tiên cao nhất)
        if ($request->hasFile($field)) {
            // Delete old file if it exists and is a physical file
            if ($currentPath && $currentPath !== $request->input($field)) {
                $this->deleteImage($currentPath);
            }
            // Pass convertToWebp (defaults: width=1920, height=null)
            return $this->uploadImage($request->file($field), $folder, 1920, null, $convertToWebp);
        }

        // 2. Nếu là string path (LFM gửi về)
        if ($request->filled($field)) {
            $newValue = $request->input($field);
            
            // Nếu input là JSON string (Gallery)
            if ($this->isJson($newValue)) {
                 return $newValue;
            }

            // Nếu input là path ảnh đơn
            // Rule: Don't delete logic for now when switching LFM paths to avoid accidental data loss.
            return $newValue;
        }

        // 3. Giữ nguyên giá trị cũ
        return $currentPath;
    }

    private function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

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
