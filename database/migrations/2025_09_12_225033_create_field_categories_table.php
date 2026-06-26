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
        Schema::create('field_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('position')->default(0);
            $table->unsignedBigInteger('parent_id')->nullable()->index();
            $table->string('description')->nullable();
            $table->string('content')->nullable();
            $table->text('solution_overview')->nullable();
            $table->json('business_challenges')->nullable();
            $table->json('brand_solutions')->nullable();
            $table->json('key_features')->nullable();
            $table->json('impact_stats')->nullable();
            $table->json('implementation_steps')->nullable();
            $table->json('related_product_ids')->nullable();
            $table->json('related_project_ids')->nullable();
            $table->unsignedBigInteger('image_id')->nullable();
            $table->unsignedBigInteger('banner_id')->nullable();
            $table->boolean('status')->default(1);
            $table->boolean('is_home')->default(false);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('field_categories');
    }
};
