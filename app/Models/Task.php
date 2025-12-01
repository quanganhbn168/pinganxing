<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    // Đổi job_id thành work_order_id
    protected $fillable = ['work_order_id', 'performer_id', 'report_content', 'collected_amount', 'is_paid'];

    public function items(): HasMany
    {
        return $this->hasMany(TaskItem::class);
    }

    // Quan hệ ngược về WorkOrder
    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'performer_id');
    }
}