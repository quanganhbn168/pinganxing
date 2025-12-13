<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('returned_items', function (Blueprint $table) {
            // Nhà cung cấp đang giữ thiết bị
            $table->foreignId('supplier_id')->nullable()->after('condition_note')
                  ->constrained('suppliers')->nullOnDelete();
            
            // Tình trạng: pending, sent_to_supplier, returned, closed
            $table->string('status')->default('pending')->after('supplier_id');
            
            // Người mang về (Admin ID)
            $table->foreignId('returned_by')->nullable()->after('status')
                  ->constrained('admins')->nullOnDelete();
            
            // Thời điểm mang về
            $table->timestamp('returned_at')->nullable()->after('returned_by');
            
            // Ghi chú thêm
            $table->text('notes')->nullable()->after('returned_at');
        });
    }

    public function down(): void
    {
        Schema::table('returned_items', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropForeign(['returned_by']);
            $table->dropColumn(['supplier_id', 'status', 'returned_by', 'returned_at', 'notes']);
        });
    }
};
