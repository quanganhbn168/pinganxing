<?php

namespace App\Http\View\Composers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

// 4 model động
use App\Models\FieldCategory;   // Lĩnh vực
use App\Models\ProjectCategory; // Dự án
use App\Models\Category;        // Sản phẩm
use App\Models\PostCategory;    // Tin tức

class MenuComposer
{
    public function compose(View $view): void
        {
            // Static
            $homeUrl    = Route::has('home')    ? route('home')    : url('/');
            $aboutUrl   = Route::has('frontend.intro.index')   ? route('frontend.intro.index')   : url('/');
            $contactUrl = Route::has('contact.show') ? route('contact.show') : url('/lien-he');

            // Dynamic (NHẤN MẠNH: luôn có URL index thay vì "#")
            $linhVuc = $this->wrapGroup(
                'LĨNH VỰC',
                $this->tree(FieldCategory::class),
                preferRoute: 'fields.index',
                fallbackPath: '/linh-vuc'
            );

            $duAn = $this->wrapGroup(
                'DỰ ÁN',
                $this->tree(ProjectCategory::class),
                preferRoute: 'projects.index',
                fallbackPath: '/du-an'
            );

            $sanPham = $this->wrapGroup(
                'SẢN PHẨM',
                $this->tree(Category::class),
                preferRoute: 'products.index',
                fallbackPath: '/san-pham'
            );

            $tinTuc = $this->wrapGroup(
                'TIN TỨC',
                $this->tree(PostCategory::class),
                preferRoute: 'posts.index',
                fallbackPath: '/tin-tuc'
            );

            $headerMenu = [
                $this->item('TRANG CHỦ', $homeUrl),
                $this->item('VỀ CHÚNG TÔI', $aboutUrl),
                $linhVuc,
                $duAn,       
                $sanPham,
                $tinTuc,
                $this->item('LIÊN HỆ & TƯ VẤN', $contactUrl),
            ];

            $view->with('headerMenu', $headerMenu);
        }

    /* ----------------- CORE ----------------- */

    protected function tree(string $modelClass, int $maxDepth = 3): array
{
    $table    = (new $modelClass)->getTable();
    $cacheKey = "header_menu_structure:{$table}";
    $ttl      = now()->addMinutes(15);

    // 1) Lấy thô từ cache (không dùng remember trước, để tự xử lý type)
    $raw = Cache::get($cacheKey);

    // 2) Nếu cache cũ là Collection → convert sang array + ghi đè lại
    if ($raw instanceof \Illuminate\Support\Collection) {
        $arr = $this->toArrayRecursive($raw, 1, $maxDepth);
        Cache::put($cacheKey, $arr, $ttl);
        return $arr;
    }

    // 3) Nếu đã là array hợp lệ → trả về luôn
    if (is_array($raw)) {
        return $raw;
    }

    // 4) Xây mới rồi lưu cache (đảm bảo trả về array)
    $q = $modelClass::query()
        ->whereNull('parent_id')
        ->where('status', 1);

    if (\Illuminate\Support\Facades\Schema::hasColumn($table, 'is_menu')) {
        $q->where('is_menu', 1);
    }

    $q->with(['children' => function ($child) use ($table) {
        $child->where('status', 1);
        if (\Illuminate\Support\Facades\Schema::hasColumn($table, 'is_menu')) {
            $child->where('is_menu', 1);
        }
        $this->applyOrder($table, $child);
        $child->with(['children' => function ($grand) use ($table) {
            $grand->where('status', 1);
            if (\Illuminate\Support\Facades\Schema::hasColumn($table, 'is_menu')) {
                $grand->where('is_menu', 1);
            }
            $this->applyOrder($table, $grand);
        }]);
    }]);

    $this->applyOrder($table, $q);

    $roots = $q->get(['id','name','slug','parent_id','position','status']);
    $arr   = $this->toArrayRecursive($roots, 1, $maxDepth); // <-- array

    Cache::put($cacheKey, $arr, $ttl);
    return $arr;
}


    /**
     * Map collection -> mảng 'title','url','children' đệ quy đến $maxDepth.
     */
    protected function toArrayRecursive($collection, int $depth, int $maxDepth): array
    {
        return $collection->map(function ($node) use ($depth, $maxDepth) {
            $children = [];
            if ($depth < $maxDepth && $node->relationLoaded('children') && $node->children->isNotEmpty()) {
                $children = $this->toArrayRecursive($node->children, $depth + 1, $maxDepth);
            }
            return [
                'title'    => $node->name,
                'url'      => route('frontend.slug.handle', $node->slug), // anh đang dùng route slug.handle
                'children' => $children,
            ];
        })->toArray();
    }

    /**
     * Áp dụng sắp xếp: position > id, fallback name nếu không có position.
     */
    protected function applyOrder(string $table, $query): void
    {
        if (Schema::hasColumn($table, 'position')) {
            $query->orderBy('position')->orderBy('id');
        } elseif (Schema::hasColumn($table, 'name')) {
            $query->orderBy('name');
        } else {
            $query->orderBy('id');
        }
    }

    /**
     * Tạo 1 item chuẩn cho header.
     */
    protected function item(string $title, string $url = '#', array $children = []): array
    {
        return ['title' => $title, 'url' => $url, 'children' => $children];
    }

    protected function wrapGroup(string $title, array $children, ?string $preferRoute = null, ?string $fallbackPath = null): array
    {
        $url = '#';
        if ($preferRoute && Route::has($preferRoute)) {
            $url = route($preferRoute);
        } elseif ($fallbackPath) {
            $url = url($fallbackPath);
        }
        return $this->item($title, $url, $children);
    }
}
