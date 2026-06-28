<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('slides')) {
            DB::table('slides')->where('link', '/tours')->update(['link' => '/tour']);
            DB::table('slides')->where('link', '/services')->update(['link' => '/dich-vu']);
        }

        if (Schema::hasTable('testimonials')) {
            $sampleTestimonials = [
                'Nguyễn Minh Anh' => 'Lịch trình gọn gàng, xe đón đúng giờ và tư vấn rất nhiệt tình. Gia đình tôi có một chuyến đi Hạ Long nhẹ nhàng, vui và đáng nhớ.',
                'Trần Quốc Bảo' => 'Đội ngũ hỗ trợ nhanh, kịch bản team building hợp với văn hóa công ty. Mọi phát sinh đều được xử lý bình tĩnh và chuyên nghiệp.',
                'Lê Thu Hà' => 'Giá tốt, thông tin rõ ràng và phòng đúng như tư vấn. Tôi đặc biệt thích cách nhân viên nhắc lịch, gửi voucher và hỗ trợ check-in.',
                'Phạm Hoàng Nam' => 'Hồ sơ được hướng dẫn kỹ từng bước nên tôi yên tâm hơn rất nhiều. Thời gian phản hồi nhanh và kết quả đúng hẹn.',
            ];

            foreach ($sampleTestimonials as $name => $content) {
                DB::table('testimonials')
                    ->where('name', $name)
                    ->where('content', $content)
                    ->update(['status' => false]);
            }
        }
    }

    public function down(): void
    {
        // Không khôi phục lại các đường dẫn cũ đã sai.
    }
};
