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
        Schema::create('post_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->default(0);
            $table->string('name');
            $table->unsignedBigInteger('image_id')->nullable();
            $table->unsignedBigInteger('banner_id')->nullable();
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->boolean('status')->default(1);
            $table->boolean('is_home')->default(1);
            $table->boolean('is_menu')->default(1);
            $table->boolean('is_footer')->default(1);
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
        Schema::dropIfExists('post_categories');
    }
};
