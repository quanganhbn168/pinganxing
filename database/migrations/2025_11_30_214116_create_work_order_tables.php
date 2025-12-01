<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Bảng Khách hàng (Giữ nguyên)
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 2. Bảng Liên hệ (Giữ nguyên)
        Schema::create('customer_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->string('type'); // 'phone', 'address'
            $table->string('value');
            $table->string('label')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        // 3. Bảng Work Orders (Thay cho bảng Jobs cũ)
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('admins'); // Người tạo phiếu
            
            $table->string('code')->unique(); // Mã phiếu (VD: WO-2024-001)
            $table->string('title'); // Yêu cầu: "Lắp camera nhà anh A"
            $table->text('description')->nullable(); 
            
            // status: pending, processing, completed, cancelled
            $table->string('status')->default('pending'); 
            
            $table->timestamps();
        });

        // 4. Bảng Phân công (Thay job_id bằng work_order_id)
        Schema::create('work_order_assignees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained('work_orders')->onDelete('cascade');
            $table->foreignId('admin_id')->constrained('admins')->onDelete('cascade');
            $table->timestamps();
        });

        // 5. Bảng Task (Thay job_id bằng work_order_id)
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            // Liên kết với Work Order thay vì Job
            $table->foreignId('work_order_id')->constrained('work_orders')->onDelete('cascade');
            $table->foreignId('performer_id')->constrained('admins'); 
            
            $table->text('report_content')->nullable();
            $table->decimal('collected_amount', 15, 0)->default(0);
            $table->boolean('is_paid')->default(false);
            
            $table->timestamps();
        });

        // 6. Bảng Vật tư (Giữ nguyên logic)
        Schema::create('task_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->onDelete('cascade');
            $table->string('item_name'); 
            $table->string('serial_number')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('price', 15, 0)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_items');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('work_order_assignees');
        Schema::dropIfExists('work_orders');
        Schema::dropIfExists('customer_contacts');
        Schema::dropIfExists('customers');
    }
};
