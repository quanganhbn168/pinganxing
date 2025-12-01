<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;
use App\Traits\HasImages;
use App\Traits\HasSlug;
use App\Models\Product;
use App\Models\Attribute;
use App\Models\Slug;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory, HasImages, HasSlug;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'status',
        'is_home',
        'is_menu',
        'is_footer',
        'position',
        'meta_description',
        'meta_keywords',
        'meta_image'
    ];

    protected $casts = [
        'status'     => 'boolean',
        'is_home'    => 'boolean',
        'is_menu'    => 'boolean',
        'is_footer'  => 'boolean',
        'parent_id'  => 'integer',
        'position'   => 'integer',
    ];

    const TYPE_PHYSICS = 'physics';
    const TYPE_SERVICE = 'services';

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->position) || $model->position === 0) {
                $model->position = static::max('position') + 1;
            }
        });

        // clear cached tree when category changes
        static::saved(function () {
            Cache::forget('category_tree_map');
        });

        static::deleted(function () {
            Cache::forget('category_tree_map');
        });
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class, 'category_attribute');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * Lấy tất cả ID của các danh mục con (cháu, chắt...) một cách đệ quy.
     * Dùng khi đã eager-load children để tránh N+1.
     *
     * @return array
     */
    public function getAllDescendantIds(): array
    {
        $descendantIds = [];
        foreach ($this->children as $child) {
            $descendantIds[] = $child->id;
            $descendantIds = array_merge($descendantIds, $child->getAllDescendantIds());
        }
        return $descendantIds;
    }

    public function latestProducts()
    {
        return $this->hasMany(Product::class)->where('status', 1)->latest()->limit(10);
    }

    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    /**
     * Alternative recursive collector (same as getAllDescendantIds)
     *
     * @return array
     */
    public function descendantIds(): array
    {
        $ids = [];
        foreach ($this->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $child->descendantIds());
        }
        return $ids;
    }

    // -------------------------
    // Static helpers with cache
    // -------------------------

    /**
     * Build children map (parent_id => [childId,...]) and cache it.
     * Cache key should be cleared when admin changes categories (we clear on save/delete above).
     *
     * @return array
     */
    public static function buildChildrenMap(): array
    {
        return Cache::remember('category_tree_map', 3600, function () {
            $map = [];
            $all = self::where('status', 1)->orderBy('name')->get(['id', 'parent_id']);
            foreach ($all as $c) {
                $parent = $c->parent_id ?? 0;
                $map[$parent][] = $c->id;
            }
            return $map;
        });
    }

    /**
     * Lấy danh sách id descendants (include self) dựa trên childrenMap
     *
     * @param int $startId
     * @return array
     */
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
}
