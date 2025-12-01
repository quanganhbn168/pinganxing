<?php

namespace App\Traits;

use App\Models\Image;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasImages
{
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable')->orderBy('position');
    }

    public function mainImage(): ?Image
    {
        // First() sẽ trả về model hoặc null, chuẩn rồi.
        return $this->images()->where('role', 'main')->first();
    }
    
    public function bannerImage(): ?Image
    {
        return $this->images()->where('role', 'banner')->first();
    }
    
    public function gallery(): MorphMany
    {
        return $this->images()->where('role', 'gallery')->orderBy('position');
    }

    /**
     * Đặt hoặc thay thế ảnh đại diện.
     * An toàn và kích hoạt event.
     */
    public function setMainImage(array $payload): Image
    {
        // 1. Lấy model ảnh cũ (nếu có)
        // Dùng ->get() để đảm bảo ta có Collection các Model
        $oldImages = $this->images()->where('role', 'main')->get();

        // 2. Tạo ảnh mới TRƯỚC
        // Nếu lệnh này thất bại, ảnh cũ vẫn còn, code dừng lại -> an toàn.
        $newImage = $this->images()->create(array_merge($payload, ['role' => 'main']));

        // 3. Xóa ảnh cũ SAU (nếu tạo mới thành công)
        // Vòng lặp và gọi ->delete() trên từng model sẽ kích hoạt event 'deleted'
        foreach ($oldImages as $oldImage) {
            $oldImage->delete(); // <-- Kích hoạt event ở Bước 2
        }

        return $newImage;
    }

    /**
     * Đặt hoặc thay thế ảnh banner.
     * (Hàm này bạn bị thiếu, logic y hệt setMainImage)
     */
    public function setBannerImage(array $payload): Image
    {
        $oldImages = $this->images()->where('role', 'banner')->get();

        $newImage = $this->images()->create(array_merge($payload, ['role' => 'banner']));

        foreach ($oldImages as $oldImage) {
            $oldImage->delete();
        }

        return $newImage;
    }

    public function addGalleryImage(array $payload, int $position = 0): Image
    {
        // Hàm này của bạn đã chuẩn rồi
        return $this->images()->create(array_merge($payload, [
            'role'     => 'gallery',
            'position' => $position,
        ]));
    }
}