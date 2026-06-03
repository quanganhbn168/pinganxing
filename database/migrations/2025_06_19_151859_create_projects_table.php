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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('project_category_id');
            $table->text('description')->nullable();
            $table->longText('project_overview')->nullable();
            $table->json('business_problems')->nullable();
            $table->json('implemented_solutions')->nullable();
            $table->json('implementation_process')->nullable();
            $table->json('achieved_results')->nullable();
            $table->text('content')->nullable();
            $table->string('investor')->nullable();
            $table->string('address')->nullable();
            $table->string('year')->nullable();
            $table->string('value')->nullable();
            $table->boolean('status')->default(0);
            $table->boolean('is_home')->default(0);
            $table->unsignedBigInteger('image_id')->nullable();
            $table->json('gallery')->nullable();
            $table->unsignedBigInteger('banner_id')->nullable();
            $table->foreign('project_category_id')->references('id')->on('project_categories')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};


