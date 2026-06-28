<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Slide;

class SlideSeeder extends Seeder
{
    public function run(): void
    {
        // Xóa slide cũ
        Slide::truncate();

        Slide::create([
            'title' => 'Khám phá thế giới cùng Ping An Xing',
            'subtitle' => 'Trải nghiệm du lịch tuyệt vời',
            'button_text' => 'Xem các tour',
            'link' => '/tour',
            'position' => 1,
            'status' => true,
        ]);

        Slide::create([
            'title' => 'Dịch vụ chất lượng cao',
            'subtitle' => 'Hỗ trợ tận tâm, uy tín hàng đầu',
            'button_text' => 'Khám phá dịch vụ',
            'link' => '/dich-vu',
            'position' => 2,
            'status' => true,
        ]);
    }
}
