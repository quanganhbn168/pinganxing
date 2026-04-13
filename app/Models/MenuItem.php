<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;

class MenuItem extends Model
{
    protected $guarded = [];

    protected $casts = [
        'position' => 'integer',
        'parent_id' => 'integer',
        'menu_id' => 'integer',
        'reference_id' => 'integer',
    ];

    // ==================== RELATIONS ====================

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent()
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('position');
    }

    /**
     * Đệ quy eager-load toàn bộ cây con (dùng cho render menu đa cấp).
     */
    public function childs()
    {
        return $this->children()->with('childs');
    }

    // ==================== REFERENCE RELATIONS ====================

    public function page()
    {
        return $this->belongsTo(Page::class, 'reference_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'reference_id');
    }

    public function fieldCategory()
    {
        return $this->belongsTo(FieldCategory::class, 'reference_id');
    }

    public function projectCategory()
    {
        return $this->belongsTo(ProjectCategory::class, 'reference_id');
    }

    public function postCategory()
    {
        return $this->belongsTo(PostCategory::class, 'reference_id');
    }

    // ==================== ACCESSORS ====================

    /**
     * Tự động resolve URL dựa trên type của menu item.
     */
    public function getLinkAttribute(): string
    {
        return match ($this->type) {
            'system_route' => Route::has($this->url) ? route($this->url) : url('/'),
            'custom' => $this->url ?? '#',
            'page' => $this->page ? url($this->page->slug) : '#',
            'category' => $this->category ? url($this->category->slugValue) : '#',
            'field_category' => $this->fieldCategory ? url($this->fieldCategory->slugValue) : '#',
            'project_category' => $this->projectCategory ? url($this->projectCategory->slugValue) : '#',
            'post_category' => $this->postCategory ? url($this->postCategory->slugValue) : '#',
            default => '#',
        };
    }

    /**
     * Kiểm tra Menu Item này có đang active (khớp URL hiện tại) hay không.
     */
    public function getIsActiveRouteAttribute(): bool
    {
        $linkPath = trim(parse_url($this->link, PHP_URL_PATH) ?? '', '/');

        return request()->url() == url($this->link)
            || ($linkPath !== '' && request()->is($linkPath . '*'));
    }

    /**
     * Lấy target attribute cho thẻ <a> (mở tab mới hay không).
     */
    public function getLinkTargetAttribute(): string
    {
        return $this->target ?? '_self';
    }
}