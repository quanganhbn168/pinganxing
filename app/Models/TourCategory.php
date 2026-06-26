<?php

namespace App\Models;

use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

use App\Traits\HasSlug;
use App\Traits\HasSeo;

class TourCategory extends Model
{
    use HasFactory, HasSlug, HasSeo, \App\Traits\HasCategoryTree;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'image_id',
        'banner_id',
        'description',
        'content',
        'status',
        'is_home',
        'position',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'meta_image_id',
    ];

    protected $casts = [
        'status'    => 'boolean',
        'is_home'   => 'boolean',
        'parent_id' => 'integer',
        'position'  => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->position) || $model->position === 0) {
                $model->position = static::max('position') + 1;
            }
        });

        static::saved(function () {
            Cache::forget('tour_category_tree_map');
        });

        static::deleted(function () {
            Cache::forget('tour_category_tree_map');
        });
    }

    public function tours(): HasMany
    {
        return $this->hasMany(Tour::class);
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'image_id');
    }

    public function banner(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'banner_id');
    }

    public static function buildChildrenMap(): array
    {
        return Cache::remember('tour_category_tree_map', 3600, function () {
            $map = [];
            $all = self::where('status', 1)->orderBy('name')->get(['id', 'parent_id']);
            foreach ($all as $c) {
                $parent = $c->parent_id ?? 0;
                $map[$parent][] = $c->id;
            }
            return $map;
        });
    }

    public static function getDescendantIds(int $startId): array
    {
        $childrenMap = self::buildChildrenMap();

        $collect = function ($id) use (&$collect, $childrenMap) {
            $ids = [$id];
            $children = $childrenMap[$id] ?? [];
            foreach ($children as $childId) {
                $ids = array_merge($ids, $collect($childId));
            }
            return $ids;
        };

        return $collect($startId);
    }

    public function getSlugUrlAttribute()
    {
        $slug = $this->slug_value;
        if (!$slug) return url('#');
        return route('frontend.tours.category', ['slug' => $slug]);
    }
}
