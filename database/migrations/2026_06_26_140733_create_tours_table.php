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
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tour_category_id')->nullable()->index();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('code')->nullable();
            $table->unsignedBigInteger('image_id')->nullable();
            $table->json('gallery')->nullable();
            $table->unsignedBigInteger('banner_id')->nullable();
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            
            // Tour specifics
            $table->string('duration')->nullable(); // e.g. 3N2Đ
            $table->string('transport')->nullable(); // e.g. Bay Vietnam Airlines
            $table->string('departure')->nullable(); // e.g. Khởi hành hằng tuần
            $table->json('features')->nullable(); // e.g. ["Khách sạn 4 sao", "Buffet sáng"]
            
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('price_discount', 15, 2)->default(0);
            
            $table->decimal('rating', 3, 1)->default(5.0);
            $table->integer('review_count')->default(0);

            $table->boolean('status')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_hot')->default(false);
            $table->boolean('is_sale')->default(false);
            $table->boolean('is_home')->default(false);

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->unsignedBigInteger('meta_image_id')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tours');
    }
};
