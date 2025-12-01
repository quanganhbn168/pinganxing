<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;

if (!function_exists('is_active_menu')) {
    function is_active_menu($route)
    {
        return Route::is($route) || Route::is($route . '.*') ? 'active' : '';
    }
}

if (!function_exists('is_open_menu')) {
    function is_open_menu(array $submenu): string
    {
        foreach ($submenu as $item) {
            // Kiểm tra submenu lồng nhau
            if (!empty($item['submenu']) && is_open_menu($item['submenu'])) {
                return 'menu-open';
            }

            // Kiểm tra route con khớp
            if (!empty($item['route']) && Route::is($item['route'] . '*')) {
                return 'menu-open';
            }
        }

        return '';
    }
}

if (!function_exists('get_category_options_flatten')) {
    /**
     * Hàm lấy danh sách danh mục dạng phẳng (Flat) để hiển thị trong Select Box.
     * Tự động Disable các danh mục Cha.
     *
     * @param \Illuminate\Support\Collection $categories (Lấy từ Category::all())
     * @param int|null $parentId (Đệ quy, không cần truyền)
     * @param string $prefix (Tiền tố hiển thị cấp độ)
     * @return array
     */
    function get_category_options_flatten($categories, $parentId = null, $prefix = '')
    {
        $options = [];
        
        // Lọc lấy các con của parentId hiện tại
        // Dùng where của Collection (trên RAM) nên rất nhanh, ko query lại DB
        $children = $categories->where('parent_id', $parentId);

        foreach ($children as $category) {
            // Kiểm tra xem thằng này có con không? (để biết là Cha hay Lá)
            // Nếu có con -> $hasChild = true -> Sẽ bị Disable
            $hasChild = $categories->where('parent_id', $category->id)->isNotEmpty();

            $options[] = (object) [
                'id' => $category->id,
                'name' => $prefix . $category->name,
                'disabled' => $hasChild, // TRUE nếu là cha
                'style' => $hasChild ? 'font-weight: bold; background-color: #eee; color: #333;' : '' // Style nhẹ cho dễ nhìn
            ];

            // Nếu nó là cha (có con), thì tiếp tục đào sâu (đệ quy)
            if ($hasChild) {
                $subOptions = get_category_options_flatten($categories, $category->id, $prefix . '-- ');
                $options = array_merge($options, $subOptions);
            }
        }

        return $options;
    }
}