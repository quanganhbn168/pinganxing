<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('sku')->unique()->nullable();
            $table->decimal('price', 12, 2)->comment('Giá bán cuối cùng');
            $table->decimal('compare_at_price', 12, 2)->nullable()->comment('Giá gốc để gạch đi (phải lớn hơn giá bán)');
            $table->boolean('is_default')->default(1);
            $table->unsignedInteger('stock');
            $table->json('options')->nullable()->comment('JSON lưu các lựa chọn biến thể');
            $table->unsignedBigInteger('image_id')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('product_variants');
    }
};