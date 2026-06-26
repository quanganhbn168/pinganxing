<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceCategory;
use Awcodes\Curator\Models\Media;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Tour du lịch', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#2b817e" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21.54 15H17a2 2 0 0 0-2 2v4.54"/><path d="M7 3.34V5a3 3 0 0 0 3 3v0a2 2 0 0 1 2 2v0c0 1.1.9 2 2 2v0a2 2 0 0 0 2-2v0c0-1.1.9-2 2-2h3.17"/><path d="M11 21.95V18a2 2 0 0 0-2-2v0a2 2 0 0 1-2-2v-1a2 2 0 0 0-2-2H2.05"/><circle cx="12" cy="12" r="10"/></svg>', 'desc' => 'Tour trong nước & quốc tế đa dạng'],
            ['name' => 'Vé máy bay', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#1f71c4" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.2-1.1.6L3 8l6 3.1L7 13.2 4 13l-2 2 4 2 2 4 2-2-.2-3 2.1-2 2.1 6c.4.2.7-.2.6-.7z"/></svg>', 'desc' => 'Đặt vé nhanh chóng giá tốt mỗi ngày'],
            ['name' => 'Khách sạn', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#7952b3" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10 22v-6.57"/><path d="M14 22v-6.57"/><path d="M3 22v-18a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v18"/><path d="M8 6h.01"/><path d="M16 6h.01"/><path d="M12 6h.01"/><path d="M12 10h.01"/><path d="M12 14h.01"/><path d="M16 10h.01"/><path d="M16 14h.01"/><path d="M8 10h.01"/><path d="M8 14h.01"/></svg>', 'desc' => 'Đa dạng lựa chọn ưu đãi hấp dẫn'],
            ['name' => 'Visa', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#1a8553" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="16" height="20" x="4" y="2" rx="2" ry="2"/><path d="M12 16h.01"/><path d="M12 12h.01"/><path d="M12 8h.01"/></svg>', 'desc' => 'Hỗ trợ xin visa nhanh chóng'],
            ['name' => 'Thuê xe', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#ff733b" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>', 'desc' => 'Xe đời mới, lái xe chuyên nghiệp'],
            ['name' => 'Team Building', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#c032a4" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>', 'desc' => 'Tổ chức sự kiện, team building'],
        ];

        foreach ($categories as $cat) {
            $slug = Str::slug($cat['name']);
            $svgPath = 'media/svg/' . $slug . '.svg';
            
            // Lưu file SVG
            Storage::disk('public')->put($svgPath, $cat['icon']);
            
            // Tạo Curator Media
            $media = Media::create([
                'disk' => 'public',
                'directory' => 'media/svg',
                'visibility' => 'public',
                'name' => $slug,
                'path' => $svgPath,
                'type' => 'image/svg+xml',
                'ext' => 'svg',
                'alt' => $cat['name'],
            ]);

            $serviceCategory = ServiceCategory::create([
                'name' => $cat['name'],
                'image_id' => $media->id,
                'description' => $cat['desc'],
                'status' => true,
                'is_home' => true,
            ]);
            $serviceCategory->slugData()->create(['slug' => $slug]);
        }
    }
}
