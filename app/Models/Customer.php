<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    
    public function workOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class);
    }
}