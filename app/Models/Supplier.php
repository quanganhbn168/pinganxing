<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',           // Mã nhà cung cấp
        'type_tag_id',    // Liên kết Tag (loại: đơn vị sửa chữa, bảo hành, v.v.)
        'contact_name',   // Tên người liên hệ
        'phone',
        'email',
        'address',
        'tax_code',       // Mã số thuế
        'bank_account',   // Số tài khoản
        'bank_name',      // Tên ngân hàng
        'note',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    // --- Relationships ---

    /**
     * Liên kết với Tag (loại nhà cung cấp)
     */
    public function typeTag(): BelongsTo
    {
        return $this->belongsTo(Tag::class, 'type_tag_id');
    }

    // --- Scopes ---

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeSearch($query, $term)
    {
        if (!$term) return $query;
        
        return $query->where(function($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('code', 'like', "%{$term}%")
              ->orWhere('phone', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%");
        });
    }
}
