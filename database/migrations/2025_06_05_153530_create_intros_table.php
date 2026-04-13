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
        Schema::create('intros', function (Blueprint $table) {
            $table->id();
            $table->string('title'); 
            $table->text('description')->nullable(); 
            $table->longText('content')->nullable(); 
            $table->unsignedBigInteger('image_id')->nullable(); 
            $table->unsignedBigInteger('banner_id')->nullable(); 
            $table->boolean('status')->default(1); 
            $table->boolean('is_home')->default(1); 
            $table->boolean('is_main')->default(1); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intros');
    }
};
