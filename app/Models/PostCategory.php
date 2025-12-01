<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Traits\HasImages;
use App\Traits\HasSlug;
use Illuminate\Support\Facades\Cache;

class PostCategory extends Model
{
    /** @use HasFactory<\Database\Factories\PostCategoryFactory> */
    use HasFactory, HasImages, HasSlug;
    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'image',
        'banner',
        'status',
        'is_home',
    ];

    protected $casts = [
        'status' => 'boolean',
        'is_home' => 'boolean',
        'parent_id' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name) . '-' . Str::random(5);
            }
        });
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }
    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
    
    public function descendantIds(): array
    {
        $ids = [];
        foreach ($this->children as $child) {
            $ids[] = $child->id;
            // nối mảng con cháu
            $ids = array_merge($ids, $child->descendantIds());
        }
        return $ids;
    }

    /**
     * Lấy mảng ID của danh mục hiện tại và tất cả danh mục con cháu
     * Có sử dụng Cache để tối ưu hiệu năng
     */
    public static function getTreeIds($rootId)
    {
        // Tạo Cache Key duy nhất cho mỗi ID
        $cacheKey = "post_category_tree_{$rootId}";

        // Cache trong 1 ngày (86400 giây) hoặc lâu hơn tùy bạn
        return Cache::remember($cacheKey, 86400, function () use ($rootId) {
            
            // Logic lấy dữ liệu (giữ nguyên code cũ của bạn)
            $rootCategory = self::with('childrenRecursive')->find($rootId);

            if (!$rootCategory) {
                return [];
            }

            $ids = [$rootCategory->id];

            $traverse = function ($categories) use (&$ids, &$traverse) {
                foreach ($categories as $category) {
                    $ids[] = $category->id;
                    if ($category->childrenRecursive->isNotEmpty()) {
                        $traverse($category->childrenRecursive);
                    }
                }
            };

            $traverse($rootCategory->childrenRecursive);

            return $ids;
        });
    }
}
