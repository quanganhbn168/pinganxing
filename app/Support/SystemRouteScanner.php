<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

/**
 * Tự động quét và trả về danh sách các route frontend (GET, không có tham số)
 * để dùng làm nguồn dữ liệu cho dropdown "Trang hệ thống" trong Menu Builder.
 */
class SystemRouteScanner
{
    /**
     * Map tên route → label tiếng Việt tùy chỉnh.
     * Route nào không có trong đây sẽ tự sinh label từ URI.
     */
    protected static array $customLabels = [
        'home' => 'Trang chủ',
        'products.index' => 'Sản phẩm',
        'frontend.intro.index' => 'Về chúng tôi',
        'frontend.services.index' => 'Dịch vụ',
        'frontend.fields.index' => 'Lĩnh vực',
        'frontend.projects.index' => 'Dự án',
        'frontend.posts.index' => 'Tin tức',
        'frontend.careers.index' => 'Tuyển dụng',
        'contact.show' => 'Liên hệ',
        'consulting.index' => 'Tư vấn triển khai',
        'agency.index' => 'Đại lý',
        'cart.page' => 'Giỏ hàng',
        'frontend.search' => 'Tìm kiếm',
    ];

    /**
     * Các prefix cần loại bỏ (admin, api, livewire, auth, ...).
     */
    protected static array $excludePrefixes = [
        'admin', 'api', 'livewire', 'filament', '_debugbar',
        'sanctum', 'login', 'register', 'logout', 'password',
        'user/', 'cart/', 'checkout', 'thanh-toan',
    ];

    /**
     * Trả về mảng [route_name => label] cho dropdown.
     */
    public static function getOptions(): array
    {
        $routes = collect(Route::getRoutes()->getRoutes())
            ->filter(function ($route) {
                // Chỉ lấy GET request
                if (!in_array('GET', $route->methods())) {
                    return false;
                }

                // Phải có tên
                $name = $route->getName();
                if (empty($name)) {
                    return false;
                }

                // Bỏ route có tham số (VD: /san-pham/{slug})
                if (preg_match('/\{/', $route->uri())) {
                    return false;
                }

                // Bỏ các prefix hệ thống
                $uri = $route->uri();
                foreach (static::$excludePrefixes as $prefix) {
                    if (str_starts_with($uri, $prefix)) {
                        return false;
                    }
                }

                return true;
            })
            ->mapWithKeys(function ($route) {
                $name = $route->getName();
                $uri = '/' . ltrim($route->uri(), '/');

                // Ưu tiên label tùy chỉnh, nếu không thì tự sinh từ URI
                $label = static::$customLabels[$name]
                    ?? static::generateLabel($uri);

                return [$name => "{$label} ({$uri})"];
            })
            ->sortBy(function ($label, $key) {
                // Sắp xếp: home lên đầu, còn lại theo alphabet
                return $key === 'home' ? '0' : $label;
            })
            ->toArray();

        return $routes;
    }

    /**
     * Tự sinh label từ URI: /ve-chung-toi → "Ve Chung Toi"
     */
    protected static function generateLabel(string $uri): string
    {
        $path = trim($uri, '/');
        if ($path === '' || $path === '/') {
            return 'Trang chủ';
        }

        return ucwords(str_replace(['-', '_', '/'], ' ', $path));
    }
}
