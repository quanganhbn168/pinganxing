<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkOrder extends Model
{
    protected $table = 'work_orders'; 

    protected $fillable = ['customer_id', 'created_by', 'code', 'title', 'description', 'status'];

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
}
