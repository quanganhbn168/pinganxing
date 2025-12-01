<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Bảng Tin tuyển dụng
        Schema::create('careers', function (Blueprint $table) {
            $table->id();
            $table->string('name');                 // Tên vị trí (vd: Nhân viên kinh doanh)
            $table->string('slug')->index();        // Slug SEO
            $table->string('image')->nullable();    // Ảnh đại diện tin
            
            // Các thông tin chi tiết
            $table->string('salary')->nullable();   // Mức lương (text để nhập: 10-15tr hoặc Thỏa thuận)
            $table->integer('quantity')->default(1);// Số lượng cần tuyển
            $table->string('education')->nullable();// Bằng cấp (Đại học, CĐ,...)
            $table->string('location')->nullable(); // Địa điểm làm việc (Hà Nội, HCM...)
            $table->string('type')->default('Full-time'); // Loại hình (Full-time, Part-time...)
            $table->date('deadline')->nullable();   // Hạn nộp hồ sơ
            
            // Nội dung chi tiết (HTML)
            $table->text('description')->nullable();// Mô tả công việc
            $table->text('requirement')->nullable();// Yêu cầu ứng viên
            $table->text('benefit')->nullable();    // Quyền lợi/Đãi ngộ

            $table->boolean('status')->default(1);  // 1: Đang tuyển, 0: Dừng tuyển
            $table->boolean('is_home')->default(0); // Hiển thị trang chủ
            $table->integer('position')->default(0);// Sắp xếp
            
            $table->timestamps();
        });

        // 2. Bảng Hồ sơ ứng tuyển (Lưu form người dùng gửi)
        Schema::create('career_applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('career_id'); // Ứng tuyển cho tin nào
            
            $table->string('name');                  // Tên ứng viên
            $table->string('email')->nullable();
            $table->string('phone');
            $table->string('cv_path');               // Đường dẫn file PDF CV
            $table->text('message')->nullable();     // Lời nhắn
            
            $table->string('status')->default('pending'); // pending, reviewed, interviewed, rejected
            
            $table->timestamps();

            // Khóa ngoại
            $table->foreign('career_id')->references('id')->on('careers')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('career_applications');
        Schema::dropIfExists('careers');
    }
};