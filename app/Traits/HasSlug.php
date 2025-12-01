<?php

namespace App\Traits;

use App\Models\Slug;
use Illuminate\Support\Facades\Schema;

trait HasSlug
{
    /**
     * Định nghĩa quan hệ với bảng Slugs
     * Đặt tên là slugData để không trùng với cột 'slug' trong bảng chính (nếu có)
     */
    public function slugData()
    {
        return $this->morphOne(Slug::class, 'sluggable');
    }

    /**
     * Accessor: Lấy URL chuẩn
     * Gọi: $item->slug_url
     */
    public function getSlugUrlAttribute()
    {
        // 1. Ưu tiên lấy từ bảng 'slugs' (Quan hệ slugData)
        // Check relationLoaded để tận dụng Eager Loading nếu có
        if ($this->relationLoaded('slugData')) {
            if ($this->slugData) {
                return url($this->slugData->slug);
            }
        } else {
            // Nếu chưa load thì query thử (chấp nhận query thêm 1 cái nếu chưa eager load)
            if ($this->slugData()->exists()) {
                return url($this->slugData->slug);
            }
        }

        // 2. Fallback: Nếu bảng 'slugs' chưa có, thử lấy cột 'slug' trong chính bảng đó (cho dữ liệu cũ)
        // Check xem trong attributes có key 'slug' không
        if (array_key_exists('slug', $this->attributes) && !empty($this->attributes['slug'])) {
            return url($this->attributes['slug']);
        }

        // 3. Đường cùng
        return url('#');
    }
    
    /**
     * Helper: Lấy slug string (không phải url)
     * Gọi: $item->slug_value
     */
    public function getSlugValueAttribute()
    {
        return $this->slugData ? $this->slugData->slug : ($this->attributes['slug'] ?? null);
    }
}