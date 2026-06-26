<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            [
                'name' => 'Nguyễn Minh Anh',
                'position' => 'Khách tour Hạ Long',
                'content' => 'Lịch trình gọn gàng, xe đón đúng giờ và tư vấn rất nhiệt tình. Gia đình tôi có một chuyến đi Hạ Long nhẹ nhàng, vui và đáng nhớ.',
            ],
            [
                'name' => 'Trần Quốc Bảo',
                'position' => 'Doanh nghiệp tổ chức team building',
                'content' => 'Đội ngũ hỗ trợ nhanh, kịch bản team building hợp với văn hóa công ty. Mọi phát sinh đều được xử lý bình tĩnh và chuyên nghiệp.',
            ],
            [
                'name' => 'Lê Thu Hà',
                'position' => 'Khách đặt combo khách sạn',
                'content' => 'Giá tốt, thông tin rõ ràng và phòng đúng như tư vấn. Tôi đặc biệt thích cách nhân viên nhắc lịch, gửi voucher và hỗ trợ check-in.',
            ],
            [
                'name' => 'Phạm Hoàng Nam',
                'position' => 'Khách dịch vụ visa',
                'content' => 'Hồ sơ được hướng dẫn kỹ từng bước nên tôi yên tâm hơn rất nhiều. Thời gian phản hồi nhanh và kết quả đúng hẹn.',
            ],
        ];

        foreach ($testimonials as $testimonial) {
            Testimonial::updateOrCreate(
                ['name' => $testimonial['name']],
                $testimonial + ['status' => true]
            );
        }
    }
}
