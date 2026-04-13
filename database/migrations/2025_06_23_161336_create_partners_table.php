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
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tên đối tác
            $table->unsignedBigInteger('image_id')->nullable(); // Đường dẫn ảnh logo
            $table->string('url')->nullable(); // Link tới website của đối tác
            $table->integer('sort_order')->default(0); // Thứ tự sắp xếp
            $table->boolean('status')->default(true); // Trạng thái hiển thị
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partners');
    }
};
