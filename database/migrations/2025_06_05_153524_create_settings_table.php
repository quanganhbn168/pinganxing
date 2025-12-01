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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('logo')->nullable();
            $table->string('banner')->nullable();
            $table->string('favicon')->nullable();

            $table->string('email')->nullable();
            $table->string('phone')->nullable();      

            $table->string('address')->nullable();    
            $table->string('zalo')->nullable();    
            $table->string('mess')->nullable();    
            $table->string('tiktok')->nullable();    
            $table->string('youtube')->nullable();    

            $table->text('map')->nullable();

            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();   
            $table->string('meta_image')->nullable();   

            $table->text('schema_script')->nullable(); 
            $table->text('head_script')->nullable();   
            $table->text('body_script')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
