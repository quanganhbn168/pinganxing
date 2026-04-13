<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\MenuItem;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // Header Menu
        $header = Menu::firstOrCreate(
            ['location' => 'header'],
            ['name' => 'Menu chính', 'is_active' => true]
        );

        if (MenuItem::where('menu_id', $header->id)->count() === 0) {
            $headerItems = [
                ['title' => 'Trang chủ', 'type' => 'system_route', 'url' => 'home'],
                ['title' => 'Về chúng tôi', 'type' => 'system_route', 'url' => 'frontend.intro.index'],
                ['title' => 'Dịch vụ', 'type' => 'system_route', 'url' => 'frontend.services.index'],
                ['title' => 'Lĩnh vực', 'type' => 'system_route', 'url' => 'frontend.fields.index'],
                ['title' => 'Dự án', 'type' => 'system_route', 'url' => 'frontend.projects.index'],
                ['title' => 'Sản phẩm', 'type' => 'system_route', 'url' => 'products.index'],
                ['title' => 'Tin tức', 'type' => 'system_route', 'url' => 'frontend.posts.index'],
                ['title' => 'Liên hệ', 'type' => 'system_route', 'url' => 'contact.show'],
            ];

            foreach ($headerItems as $i => $item) {
                MenuItem::create(array_merge($item, [
                    'menu_id' => $header->id,
                    'parent_id' => 0,
                    'position' => $i,
                    'target' => '_self',
                ]));
            }
        }

        // Footer Menu
        $footer = Menu::firstOrCreate(
            ['location' => 'footer', 'name' => 'Footer - Về công ty'],
            ['is_active' => true]
        );

        if (MenuItem::where('menu_id', $footer->id)->count() === 0) {
            $footerItems = [
                ['title' => 'Trang chủ', 'type' => 'system_route', 'url' => 'home'],
                ['title' => 'Về chúng tôi', 'type' => 'system_route', 'url' => 'frontend.intro.index'],
                ['title' => 'Dự án', 'type' => 'system_route', 'url' => 'frontend.projects.index'],
                ['title' => 'Tuyển dụng', 'type' => 'system_route', 'url' => 'frontend.careers.index'],
                ['title' => 'Liên hệ', 'type' => 'system_route', 'url' => 'contact.show'],
            ];

            foreach ($footerItems as $i => $item) {
                MenuItem::create(array_merge($item, [
                    'menu_id' => $footer->id,
                    'parent_id' => 0,
                    'position' => $i,
                    'target' => '_self',
                ]));
            }
        }
    }
}
