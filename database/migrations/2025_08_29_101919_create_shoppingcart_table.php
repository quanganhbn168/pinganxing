<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('cart.database.table'), function (Blueprint $table) {
            $table->string('identifier'); // ID của user
            $table->string('instance');   // Tên instance giỏ hàng (mặc định là 'default')
            $table->longText('content');    // Nội dung giỏ hàng (đã serialize)
            $table->timestamps();

            $table->primary(['identifier', 'instance']);
        });
    }

    public function down(): void
    {
        Schema::drop(config('cart.database.table'));
    }
};