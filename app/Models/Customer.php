<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Customer extends Model
{
    protected $fillable = ['name', 'notes'];

    // Lấy tất cả liên hệ
    public function contacts(): HasMany
    {
        return $this->hasMany(CustomerContact::class);
    }

    // Helper lấy danh sách SĐT
    public function phones()
    {
        return $this->contacts()->where('type', 'phone');
    }

    // Helper lấy danh sách địa chỉ
    public function addresses()
    {
        return $this->contacts()->where('type', 'address');
    }
    public function tasks(): HasManyThrough
    {
        return $this->hasManyThrough(
            Task::class,      // Model đích (Task)
            WorkOrder::class, // Model trung gian (WorkOrder)
            'customer_id',    // Khóa ngoại trên bảng trung gian (work_orders.customer_id)
            'work_order_id',  // Khóa ngoại trên bảng đích (tasks.work_order_id)
            'id',             // Khóa chính bảng hiện tại (customers.id)
            'id'              // Khóa chính bảng trung gian (work_orders.id)
        );
    }

    public function workOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class);
    }
}