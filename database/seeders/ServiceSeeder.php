<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\ServiceCategory;
use Awcodes\Curator\Models\Media;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['name' => 'Đặt vé máy bay nội địa', 'category' => 'Vé máy bay'],
            ['name' => 'Vé máy bay khứ hồi Hà Nội - Phú Quốc', 'category' => 'Vé máy bay'],
            ['name' => 'Vé máy bay khứ hồi Sài Gòn - Đà Lạt', 'category' => 'Vé máy bay'],
            ['name' => 'Combo Khách sạn 5 sao Nha Trang', 'category' => 'Khách sạn'],
            ['name' => 'Khách sạn Vinpearl Phú Quốc 3N2Đ', 'category' => 'Khách sạn'],
            ['name' => 'Dịch vụ làm Visa Hàn Quốc trọn gói', 'category' => 'Visa'],
            ['name' => 'Xin Visa Châu Âu Schengen tỷ lệ đậu cao', 'category' => 'Visa'],
            ['name' => 'Thuê xe ô tô 7 chỗ tự lái Hà Nội', 'category' => 'Thuê xe'],
            ['name' => 'Thuê xe Limousine 9 chỗ đón sân bay', 'category' => 'Thuê xe'],
            ['name' => 'Gói Tổ chức Team Building bãi biển', 'category' => 'Team Building'],
            ['name' => 'Kịch bản Gala Dinner & Team Building Doanh Nghiệp', 'category' => 'Team Building'],
        ];

        // Dùng 1 ảnh minh họa chung
        $media = Media::firstOrCreate(
            ['name' => 'dummy-service-image'],
            [
                'disk' => 'public',
                'directory' => 'media',
                'visibility' => 'public',
                'path' => 'https://picsum.photos/1280/720',
                'type' => 'image/jpeg',
                'ext' => 'jpg',
                'alt' => 'Service image',
            ]
        );

        foreach ($services as $srvData) {
            $cat = ServiceCategory::where('name', $srvData['category'])->first();
            if ($cat) {
                $slug = Str::slug($srvData['name']);
                $service = Service::create([
                    'service_category_id' => $cat->id,
                    'name' => $srvData['name'],
                    'image_id' => $media->id,
                    'is_home' => true,
                    'status' => true,
                    'description' => 'Mô tả ngắn gọn cho dịch vụ ' . $srvData['name'],
                    'content' => 'Nội dung chi tiết dịch vụ sẽ được cập nhật sau. Quý khách có thể liên hệ trực tiếp để được tư vấn.'
                ]);
                $service->slugData()->create(['slug' => $slug]);
            }
        }
    }
}
