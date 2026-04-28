<?php

namespace App\Traits;

use App\Models\Career;
use App\Models\Category;
use App\Models\Field;
use App\Models\FieldCategory;
use App\Models\Intro;
use App\Models\Page;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Product;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Slug;

trait HasSlug
{
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
            Page::class => '',
        ];
    }

    public function slugData()
    {
        return $this->morphOne(Slug::class, 'sluggable');
    }

    public function slug()
    {
        return $this->slugData();
    }

    public function getSlugPrefixAttribute(): ?string
    {
        return static::slugPrefixMap()[static::class] ?? null;
    }

    public function getSlugUrlAttribute()
    {
        $slugString = $this->slug_value;
        $prefix = $this->slug_prefix;

        if ($slugString && $prefix !== null && $prefix !== '') {
            return url("{$prefix}/{$slugString}");
        }

        return $slugString ? url($slugString) : url('#');
    }

    public function getSlugValueAttribute()
    {
        if ($this->relationLoaded('slugData')) {
            return $this->slugData?->slug;
        }

        if ($this->slugData) {
            return $this->slugData->slug;
        }

        return $this->attributes['slug'] ?? null;
    }
}
