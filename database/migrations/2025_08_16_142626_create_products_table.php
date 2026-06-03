<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // --- Cột cấu trúc chính ---
            $table->string('type')->default('simple')->comment('Loại sản phẩm: simple hoặc variable');
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained()->onDelete('set null');

            // --- Cột thông tin cơ bản ---
            $table->string('name');
            $table->string('code')->unique();
            $table->unsignedBigInteger('image_id')->nullable();
            $table->json('gallery')->nullable();
            $table->unsignedBigInteger('banner_id')->nullable();
            $table->longText('description')->nullable();
            $table->longText('content')->nullable();
            $table->longText('specifications')->nullable();

            // --- Cột cho sản phẩm "simple" ---
            $table->decimal('price', 12, 2)->nullable()->comment('Giá cho sản phẩm simple');
            $table->decimal('price_discount', 12, 2)->nullable();
            $table->unsignedInteger('stock')->nullable()->comment('Tồn kho cho sản phẩm simple');

            // --- Cột trạng thái ---
            $table->boolean('status')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_home')->default(false);
            $table->boolean('is_on_sale')->default(false);
            $table->string('discount_type')->nullable();
            $table->decimal('discount_value', 12, 2)->nullable();
            $table->boolean('has_variants')->default(false);
            $table->enum('product_type', ['physical', 'service']);
            // --- CÁC CỘT META CHO SEO ---
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->unsignedBigInteger('meta_image_id')->nullable();
            $table->text('meta_keywords')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};


