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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_category_id');
            $table->string('name');
            $table->unsignedBigInteger('image_id')->nullable();
            $table->json('gallery')->nullable();
            $table->unsignedBigInteger('banner_id')->nullable();
            $table->text('description')->nullable();
            $table->text('content')->nullable();
            $table->boolean('status')->default(1);
            $table->boolean('is_home')->default(1);
            $table->boolean('is_menu')->default(1);
            $table->boolean('is_footer')->default(1);
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->unsignedInteger('price')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();   
            $table->unsignedBigInteger('meta_image_id')->nullable();   
            $table->timestamps();

            $table->foreign('service_category_id')
            ->references('id')
            ->on('service_categories')
            ->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->nullOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
