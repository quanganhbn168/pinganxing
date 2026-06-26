<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TourCategory;
use Awcodes\Curator\Models\Media;
use Illuminate\Support\Str;

class TourCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Hạ Long', 'desc' => 'Vịnh Hạ Long - Kỳ quan thiên nhiên thế giới', 'img' => 'https://picsum.photos/800/450?random=11'],
            ['name' => 'Đà Nẵng', 'desc' => 'Đà Nẵng - Thành phố đáng sống', 'img' => 'https://picsum.photos/800/450?random=12'],
            ['name' => 'Nha Trang', 'desc' => 'Nha Trang - Hòn ngọc của biển Đông', 'img' => 'https://picsum.photos/800/450?random=13'],
            ['name' => 'Phú Quốc', 'desc' => 'Đảo ngọc Phú Quốc', 'img' => 'https://picsum.photos/800/450?random=14'],
            ['name' => 'Đà Lạt', 'desc' => 'Đà Lạt - Thành phố mộng mơ', 'img' => 'https://picsum.photos/800/450?random=15'],
            ['name' => 'Sapa', 'desc' => 'Sapa - Thị trấn sương mù', 'img' => 'https://picsum.photos/800/450?random=16'],
        ];

        foreach ($categories as $cat) {
            $slug = Str::slug($cat['name']);
            
            $media = Media::create([
                'disk' => 'public',
                'directory' => 'media/tours',
                'visibility' => 'public',
                'name' => 'cat-' . $slug,
                'path' => $cat['img'],
                'type' => 'image/jpeg',
                'ext' => 'jpg',
                'alt' => $cat['name'],
            ]);

            TourCategory::create([
                'name' => $cat['name'],
                'slug' => $slug,
                'image_id' => $media->id,
                'description' => $cat['desc'],
                'status' => true,
                'is_home' => true,
            ]);
        }
    }
}
