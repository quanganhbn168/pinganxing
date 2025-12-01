<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasImages;
use App\Traits\HasSlug;
class Field extends Model
{
    use HasFactory, HasImages, HasSlug;

    /**
     * Các thuộc tính có thể được gán hàng loạt.
     *
     * @var array
     */
    protected $fillable = [
        'field_category_id',
        'name',
        'slug',
        'summary',
        'content',
        'status',
        'is_featured',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    /**
     * Chuyển đổi kiểu dữ liệu cho các thuộc tính.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'boolean',
        'is_featured' => 'boolean',
    ];

    /**
     * Lấy danh mục của lĩnh vực này.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(FieldCategory::class, 'field_category_id');
    }

    /**
     * Scope a query to only include active fields.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope a query to only include featured fields.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Lấy khóa route cho model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }
    
}