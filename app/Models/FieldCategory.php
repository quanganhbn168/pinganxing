<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasImages;
use App\Traits\HasSlug;
class FieldCategory extends Model
{
    use HasFactory, HasImages, HasSlug;

    /**
     * Các thuộc tính có thể được gán hàng loạt.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'description',
        'content',
        'status',
        'position',
    ];

    /**
     * Chuyển đổi kiểu dữ liệu cho các thuộc tính.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Lấy danh mục cha.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(FieldCategory::class, 'parent_id');
    }

    /**
     * Lấy các danh mục con.
     */
    public function children(): HasMany
    {
        return $this->hasMany(FieldCategory::class, 'parent_id');
    }

    /**
     * Lấy tất cả các lĩnh vực thuộc về danh mục này.
     */
    public function fields(): HasMany
    {
        return $this->hasMany(Field::class);
    }

    /**
     * Scope a query to only include active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true)->orderBy('order', 'asc');
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