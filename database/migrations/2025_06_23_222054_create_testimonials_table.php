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
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('image_id')->nullable();
            $table->string('position')->nullable(); // công việc / ngành
            $table->text('content'); // nội dung cảm nhận
            $table->boolean('status')->default(true); // dùng để bật/tắt hiển thị
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
