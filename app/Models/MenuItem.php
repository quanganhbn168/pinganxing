<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;

class MenuItem extends Model
{
    protected $guarded = [];

    // 1. Relation: Lấy con (Đệ quy)
    public function children() {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('position');
    }
    
    // Relation dùng để Eager Load cả cây
    public function childs() {
        return $this->children()->with('childs');
    }

    // 2. Relation: Liên kết tới các Model thật
    public function page() { return $this->belongsTo(Page::class, 'reference_id'); }
    public function category() { return $this->belongsTo(Category::class, 'reference_id'); }
    public function fieldCategory() { return $this->belongsTo(FieldCategory::class, 'reference_id'); }
    public function projectCategory() { return $this->belongsTo(ProjectCategory::class, 'reference_id'); }
    public function postCategory() { return $this->belongsTo(PostCategory::class, 'reference_id'); }
    // Thêm product, post nếu cần...

    // --- LOGIC LẤY LINK (Cập nhật switch case) ---
    public function getLinkAttribute()
    {
        switch ($this->type) {
            case 'system_route':
                return Route::has($this->url) ? route($this->url) : url('/');

            case 'custom':
                return $this->url ?? '#';

            case 'page':
                return $this->page ? url($this->page->slug) : '#';
            
            // Xử lý từng loại danh mục riêng biệt
            case 'category': // Sửa type khớp với Admin view
                return $this->category ? url($this->category->slugValue) : '#';
            
            case 'field_category':
                return $this->fieldCategory ? url($this->fieldCategory->slugValue) : '#';

            case 'project_category':
                return $this->projectCategory ? url($this->projectCategory->slugValue) : '#';

            case 'post_category':
                return $this->postCategory ? url($this->postCategory->slugValue) : '#';
            
            default:
                return '#';
        }
    }
}