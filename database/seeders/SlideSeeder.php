<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Slide;
use Awcodes\Curator\Models\Media;

class SlideSeeder extends Seeder
{
    public function run(): void
    {
        // Xóa slide cũ
        Slide::truncate();

        // Hình banner 1
        $media1 = Media::firstOrCreate(
            ['name' => 'banner-1'],
            [
                'disk' => 'public',
                'directory' => 'media',
                'visibility' => 'public',
                'path' => 'https://picsum.photos/1920/1080?random=1',
                'type' => 'image/jpeg',
                'ext' => 'jpg',
                'alt' => 'Trải nghiệm du lịch tuyệt vời',
            ]
        );

        Slide::create([
            'image_id' => $media1->id,
            'title' => 'Khám phá thế giới cùng Ping An Xing',
            'subtitle' => 'Trải nghiệm du lịch tuyệt vời',
            'button_text' => 'Xem các tour',
            'link' => '/tours',
            'position' => 1,
            'status' => true,
        ]);

        // Hình banner 2
        $media2 = Media::firstOrCreate(
            ['name' => 'banner-2'],
            [
                'disk' => 'public',
                'directory' => 'media',
                'visibility' => 'public',
                'path' => 'https://picsum.photos/1920/1080?random=2',
                'type' => 'image/jpeg',
                'ext' => 'jpg',
                'alt' => 'Dịch vụ chất lượng cao',
            ]
        );

        Slide::create([
            'image_id' => $media2->id,
            'title' => 'Dịch vụ chất lượng cao',
            'subtitle' => 'Hỗ trợ tận tâm, uy tín hàng đầu',
            'button_text' => 'Khám phá dịch vụ',
            'link' => '/services',
            'position' => 2,
            'status' => true,
        ]);
    }
}
