<?php

namespace App\Traits;

use App\Models\Slug;
use Illuminate\Support\Facades\Schema;
use App\Models\Product;
use App\Models\Project;
use App\Models\Service;
use App\Models\Post;
use App\Models\Field;
use App\Models\Intro;
use App\Models\Career;
use App\Models\Category;
use App\Models\ProjectCategory;
use App\Models\ServiceCategory;
use App\Models\PostCategory;
use App\Models\FieldCategory;

trait HasSlug
{
    /**
     * Map tập trung: Model class → URL prefix.
     * Dùng morph type từ bảng slugs, không cần thêm method vào từng model.
     */
    public static function slugPrefixMap(): array
    {
        return [
            Product::class => 'san-pham',
            Category::class => 'san-pham',
            Project::class => 'du-an',
            ProjectCategory::class => 'du-an',
            Service::class => 'dich-vu',
            ServiceCategory::class => 'dich-vu',
            Post::class => 'tin-tuc',
            PostCategory::class => 'tin-tuc',
            Field::class => 'linh-vuc',
            FieldCategory::class => 'linh-vuc',
            Intro::class => 'gioi-thieu',
            Career::class => 'tuyen-dung',
        ];
    }

    /**
     * Định nghĩa quan hệ với bảng Slugs
     * Đặt tên là slugData để không trùng với cột 'slug' trong bảng chính (nếu có)
     */
    public function slugData()
    {
        return $this->morphOne(Slug::class, 'sluggable');
    }

    /**
     * Alias cho slugData() để tương thích với SlugService
     */
    public function slug()
    {
        return $this->slugData();
    }

    /**
     * Lấy prefix URL của model hiện tại.
     * Gọi: $item->slug_prefix  →  'san-pham', 'du-an', ...
     */
    public function getSlugPrefixAttribute(): ?string
    {
        return static::slugPrefixMap()[static::class] ?? null;
    }

    /**
     * Accessor: Lấy URL chuẩn SEO (có prefix).
     * Gọi: $item->slug_url  →  https://domain.com/san-pham/may-bom-abc
     */
    public function getSlugUrlAttribute()
    {
        $slugString = $this->slugValue;
        $prefix = $this->slug_prefix;

        if ($slugString && $prefix) {
            return url("{$prefix}/{$slugString}");
        }

        // Fallback: URL không prefix (cho dữ liệu chưa map)
        if ($slugString) {
            return url($slugString);
        }

        return url('#');
    }

    /**
     * Helper: Lấy slug string (không phải url).
     * Gọi: $item->slugValue hoặc $item->slug_value
     */
    public function getSlugValueAttribute()
    {
        // 1. Ưu tiên bảng slugs (relation)
        if ($this->relationLoaded('slugData')) {
            return $this->slugData?->slug;
        }

        if ($this->slugData) {
            return $this->slugData->slug;
        }

        // 2. Fallback: cột slug trên bảng chính (dữ liệu cũ)
        return $this->attributes['slug'] ?? null;
    }
}