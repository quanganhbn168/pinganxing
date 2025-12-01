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
            $table->string('slug'); 
            $table->text('description')->nullable(); 
            $table->longText('content')->nullable(); 
            $table->string('image')->nullable(); 
            $table->string('banner')->nullable(); 
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
