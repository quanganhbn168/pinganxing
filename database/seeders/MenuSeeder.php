<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\MenuItem;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // Xóa menu cũ
        MenuItem::truncate();
        Menu::truncate();

        // Header Menu
        $header = Menu::firstOrCreate(
            ['location' => 'header'],
            ['name' => 'Menu chính', 'is_active' => true]
        );

        $headerItems = [
            ['title' => 'Trang chủ', 'type' => 'system_route', 'url' => 'home'],
            ['title' => 'Tour du lịch', 'type' => 'system_route', 'url' => 'frontend.tours.index'],
            ['title' => 'Dịch vụ', 'type' => 'system_route', 'url' => 'frontend.services.index'],
            ['title' => 'Tin tức', 'type' => 'system_route', 'url' => 'frontend.posts.index'],
            ['title' => 'Về chúng tôi', 'type' => 'system_route', 'url' => 'frontend.intro.index'],
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

        // Footer Menu
        $footer = Menu::firstOrCreate(
            ['location' => 'footer'],
            ['name' => 'Menu Footer', 'is_active' => true]
        );

        $footerItems = [
            ['title' => 'Trang chủ', 'type' => 'system_route', 'url' => 'home'],
            ['title' => 'Tour du lịch', 'type' => 'system_route', 'url' => 'frontend.tours.index'],
            ['title' => 'Về chúng tôi', 'type' => 'system_route', 'url' => 'frontend.intro.index'],
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
