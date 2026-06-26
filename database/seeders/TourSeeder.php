<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tour;
use App\Models\TourCategory;
use Awcodes\Curator\Models\Media;
use Illuminate\Support\Str;

class TourSeeder extends Seeder
{
    public function run(): void
    {
        $tours = [
            ['name' => 'Tour Hạ Long 2 Ngày 1 Đêm trên Du thuyền', 'category' => 'Hạ Long', 'price' => 2500000],
            ['name' => 'Tour Hạ Long - Cát Bà 3 Ngày 2 Đêm', 'category' => 'Hạ Long', 'price' => 3500000],
            ['name' => 'Tour Đà Nẵng - Hội An - Bà Nà Hills', 'category' => 'Đà Nẵng', 'price' => 4500000],
            ['name' => 'Tour Đà Nẵng - Cù Lao Chàm 3 Ngày 2 Đêm', 'category' => 'Đà Nẵng', 'price' => 3200000],
            ['name' => 'Tour Nha Trang - Vinpearl Land 4 Ngày 3 Đêm', 'category' => 'Nha Trang', 'price' => 5500000],
            ['name' => 'Tour Đảo ngọc Phú Quốc 3 Ngày 2 Đêm', 'category' => 'Phú Quốc', 'price' => 4800000],
            ['name' => 'Tour Đà Lạt Mộng mơ 3 Ngày 2 Đêm', 'category' => 'Đà Lạt', 'price' => 3000000],
            ['name' => 'Tour Sapa - Fansipan 2 Ngày 1 Đêm', 'category' => 'Sapa', 'price' => 2200000],
        ];

        foreach ($tours as $index => $tourData) {
            $cat = TourCategory::where('name', $tourData['category'])->first();
            if ($cat) {
                $media = Media::firstOrCreate(
                    ['name' => 'tour-image-' . $index],
                    [
                        'disk' => 'public',
                        'directory' => 'media',
                        'visibility' => 'public',
                        'path' => 'https://picsum.photos/1280/720?random=' . ($index + 20),
                        'type' => 'image/jpeg',
                        'ext' => 'jpg',
                        'alt' => $tourData['name'],
                    ]
                );

                Tour::create([
                    'tour_category_id' => $cat->id,
                    'name' => $tourData['name'],
                    'slug' => Str::slug($tourData['name']),
                    'image_id' => $media->id,
                    'price' => $tourData['price'],
                    'price_discount' => $tourData['price'] * 0.9,
                    'rating' => 5,
                    'is_home' => true,
                    'status' => true,
                    'description' => 'Khám phá ' . $tourData['name'] . ' cùng Ping An Xing với mức giá ưu đãi hấp dẫn.',
                    'content' => 'Nội dung chi tiết chương trình tour sẽ được cập nhật. Vui lòng liên hệ để nhận lịch trình chi tiết và tư vấn miễn phí.'
                ]);
            }
        }
    }
}
