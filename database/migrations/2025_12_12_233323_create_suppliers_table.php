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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');                        // Tên nhà cung cấp
            $table->string('code')->nullable()->unique();  // Mã NCC
            $table->foreignId('type_tag_id')->nullable()   // Loại: đơn vị sửa chữa, bảo hành
                  ->constrained('tags')->nullOnDelete();
            $table->string('contact_name')->nullable();    // Tên người liên hệ
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('tax_code')->nullable();        // MST
            $table->string('bank_account')->nullable();
            $table->string('bank_name')->nullable();
            $table->text('note')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
