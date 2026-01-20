<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('homepage_sections', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();           // VD: 'hero', 'intro', 'fields', 'projects'
            $table->string('name');                    // Tên hiển thị: "Hero Slider", "Giới thiệu"
            $table->string('title')->nullable();       // Tiêu đề section (hiển thị trên frontend)
            $table->string('subtitle')->nullable();    // Subtitle
            $table->text('description')->nullable();   // Mô tả ngắn
            $table->string('image')->nullable();       // Ảnh minh họa
            $table->string('background_image')->nullable(); // Ảnh nền
            $table->json('settings')->nullable();      // Các cấu hình bổ sung dạng JSON
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homepage_sections');
    }
};
