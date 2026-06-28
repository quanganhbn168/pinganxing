<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TourCategory;
use Illuminate\Support\Str;

class TourCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Hạ Long', 'desc' => 'Vịnh Hạ Long - Kỳ quan thiên nhiên thế giới'],
            ['name' => 'Đà Nẵng', 'desc' => 'Đà Nẵng - Thành phố đáng sống'],
            ['name' => 'Nha Trang', 'desc' => 'Nha Trang - Hòn ngọc của biển Đông'],
            ['name' => 'Phú Quốc', 'desc' => 'Đảo ngọc Phú Quốc'],
            ['name' => 'Đà Lạt', 'desc' => 'Đà Lạt - Thành phố mộng mơ'],
            ['name' => 'Sapa', 'desc' => 'Sapa - Thị trấn sương mù'],
        ];

        foreach ($categories as $cat) {
            $slug = Str::slug($cat['name']);
            
            TourCategory::create([
                'name' => $cat['name'],
                'slug' => $slug,
                'description' => $cat['desc'],
                'status' => true,
                'is_home' => true,
            ]);
        }
    }
}
