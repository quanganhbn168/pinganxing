<?php

namespace Database\Seeders;

use App\Models\HomepageSection;
use Illuminate\Database\Seeder;

class HomepageSectionSeeder extends Seeder
{
    /**
     * Seed các sections mặc định cho trang chủ
     */
    public function run(): void
    {
        $sections = [
            [
                'key' => 'hero',
                'name' => 'Hero Slider',
                'title' => null,
                'subtitle' => null,
                'description' => null,
                'is_active' => true,
                'order' => 0,
                'settings' => null,
            ],
            [
                'key' => 'intro',
                'name' => 'Giới thiệu công ty',
                'title' => null, // Sẽ lấy từ $setting->name
                'subtitle' => null,
                'description' => null,
                'is_active' => true,
                'order' => 1,
                'settings' => [
                    'button_text' => 'Download Profile',
                    'button_link' => '/storage/profile.pdf',
                    'button_2_text' => 'Xem chi tiết',
                    'button_2_link' => '/gioi-thieu',
                ],
            ],
            [
                'key' => 'fields',
                'name' => 'Lĩnh vực hoạt động',
                'title' => 'Lĩnh vực hoạt động',
                'subtitle' => null,
                'description' => null,
                'background_image' => 'images/setting/contractors-bg-1.png',
                'is_active' => true,
                'order' => 2,
                'settings' => null,
            ],
            [
                'key' => 'projects',
                'name' => 'Dự án nổi bật',
                'title' => 'Dự án nổi bật',
                'subtitle' => null,
                'description' => null,
                'is_active' => true,
                'order' => 3,
                'settings' => null,
            ],
            [
                'key' => 'partners',
                'name' => 'Đối tác & Khách hàng',
                'title' => 'Đối tác & khách hàng',
                'subtitle' => null,
                'description' => null,
                'is_active' => true,
                'order' => 4,
                'settings' => [
                    'quote_text' => 'Chúng tôi cam kết đem đến cho khách hàng những sản phẩm chất lượng cao và dịch vụ tốt nhất!',
                    'quote_author' => 'Lê Sỹ Ngà',
                    'quote_position' => 'Giám đốc',
                    'quote_image' => 'images/setting/bat-tay.png',
                ],
            ],
            [
                'key' => 'core_values',
                'name' => 'Banner quảng cáo',
                'title' => null,
                'subtitle' => null,
                'description' => null,
                'is_active' => true,
                'order' => 5,
                'settings' => null,
            ],
            [
                'key' => 'news',
                'name' => 'Tin tức - Sự kiện',
                'title' => 'Tin tức - sự kiện',
                'subtitle' => null,
                'description' => null,
                'is_active' => true,
                'order' => 6,
                'settings' => [
                    'video_title' => 'Video giới thiệu',
                ],
            ],
            [
                'key' => 'careers',
                'name' => 'Tuyển dụng & Đại lý',
                'title' => null,
                'subtitle' => null,
                'description' => null,
                'is_active' => true,
                'order' => 7,
                'settings' => [
                    'card_1_title' => 'Tuyển dụng',
                    'card_1_link' => '/tuyen-dung',
                    'card_1_button' => 'Ứng tuyển',
                    'card_2_title' => 'Hệ thống đại lý',
                    'card_2_link' => '/dai-ly',
                    'card_2_button' => 'Hợp tác ngay',
                    'card_3_title' => 'Tư vấn triển khai',
                    'card_3_link' => '/tu-van',
                    'card_3_button' => 'Gửi yêu cầu',
                ],
            ],
            [
                'key' => 'testimonials',
                'name' => 'Đánh giá từ khách hàng',
                'title' => 'Đánh giá từ khách hàng',
                'subtitle' => null,
                'description' => null,
                'is_active' => true,
                'order' => 8,
                'settings' => null,
            ],
            [
                'key' => 'contact_form',
                'name' => 'Form liên hệ',
                'title' => 'Vui lòng để lại thông tin, Cnet sẽ liên hệ trong thời gian sớm nhất!',
                'subtitle' => null,
                'description' => null,
                'background_image' => 'images/setting/lien-he-bg.jpg',
                'is_active' => true,
                'order' => 9,
                'settings' => [
                    'feature_1_icon' => 'fa-solid fa-gears',
                    'feature_1_text' => 'Quy trình nhanh chóng',
                    'feature_2_icon' => 'fa-solid fa-headset',
                    'feature_2_text' => 'Đội ngũ tư vấn nhiệt tình',
                    'feature_3_icon' => 'fa-solid fa-tags',
                    'feature_3_text' => 'Giá cả phù hợp nhất',
                ],
            ],
        ];

        foreach ($sections as $section) {
            HomepageSection::updateOrCreate(
                ['key' => $section['key']],
                $section
            );
        }
    }
}
