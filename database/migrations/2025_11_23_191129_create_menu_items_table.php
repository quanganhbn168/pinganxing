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
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            
            // Loại menu: 'page', 'category', 'product', 'custom'
            $table->string('type')->default('custom'); 
            
            // ID của bài viết/danh mục (nếu type != custom)
            $table->unsignedBigInteger('reference_id')->nullable(); 
            
            // Link cứng (chỉ dùng khi type = custom)
            $table->string('url')->nullable(); 
            
            $table->unsignedBigInteger('parent_id')->nullable()->default(0);
            $table->integer('position')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
