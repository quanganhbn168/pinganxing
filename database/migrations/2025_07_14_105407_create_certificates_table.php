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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('image_id')->nullable();        
            $table->string('issued_by')->nullable();    
            $table->date('issued_at')->nullable();      
            $table->date('expired_at')->nullable();     
            $table->text('description')->nullable();
            $table->boolean('status')->default(1);      
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
