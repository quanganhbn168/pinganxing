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
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('position')->nullable(); // ví dụ: Giảng viên tiếng Trung
            $table->unsignedBigInteger('image_id')->nullable();
            $table->string('level')->nullable(); // HSK 6, HSK 5,...
            $table->integer('experience')->nullable(); // số năm kinh nghiệm
            $table->text('bio')->nullable(); // mô tả chi tiết về giảng viên
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
