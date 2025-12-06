<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkOrder extends Model
{
    protected $table = 'work_orders'; 

    protected $fillable = [
        'customer_id',
        'created_by',
        'code',
        'title',
        'description',
        'status',
        'priority',
        'site_address',
        'contact_person',
        'contact_phone',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'status' => \App\Enums\WorkOrderStatus::class,
        'priority' => \App\Enums\WorkOrderPriority::class, // Auto cast to Enum
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    // Nhân viên được gán
    public function assignees(): BelongsToMany
    {
        return $this->belongsToMany(Admin::class, 'work_order_assignees', 'work_order_id', 'admin_id');
    }

    // Các task
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
    
    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(Admin::class, 'approved_by');
    }

    // Lấy TẤT CẢ vật tư trong phiếu việc này (Đi xuyên qua bảng tasks)
    // Rất tiện để làm Bảo Hành Dịch Vụ
    public function allItems()
    {
        return $this->hasManyThrough(TaskItem::class, Task::class);
    }

    public function warrantyService()
    {
        return $this->hasOne(WarrantyService::class);
    }

    public function warrantyDevice()
    {
        return $this->hasOne(WarrantyDevice::class);
    }

    

    // --- SCOPES (Lọc nhanh) ---

    // Lấy các phiếu đang chờ Admin duyệt
    public function scopePendingApproval($query)
    {
        return $query->where('status', 'pending_approval');
    }

    // Lấy các phiếu đã hoàn thành (để sinh bảo hành)
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
