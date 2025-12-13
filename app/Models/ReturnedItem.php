<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnedItem extends Model
{
    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_SENT_TO_SUPPLIER = 'sent_to_supplier';
    const STATUS_RETURNED = 'returned';
    const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'task_report_id',
        'item_name',
        'serial_number',
        'reason',
        'condition_note',
        'supplier_id',
        'status',
        'returned_by',
        'returned_at',
        'notes',
    ];

    protected $casts = [
        'returned_at' => 'datetime',
    ];

    // --- RELATIONSHIPS ---

    public function report(): BelongsTo
    {
        return $this->belongsTo(TaskReport::class, 'task_report_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function returnedByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'returned_by');
    }

    // --- ACCESSORS ---

    /**
     * Lý do thu hồi với label tiếng Việt
     */
    public function getReasonLabelAttribute(): string
    {
        return match($this->reason) {
            'warranty' => 'Bảo hành',
            'replace' => 'Đổi model',
            'defective' => 'Lỗi nhà SX',
            'upgrade' => 'Nâng cấp',
            default => $this->reason ?? 'Khác',
        };
    }

    /**
     * Status với label tiếng Việt
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Chờ xử lý',
            self::STATUS_SENT_TO_SUPPLIER => 'Đã gửi NCC',
            self::STATUS_RETURNED => 'Đã mang về',
            self::STATUS_CLOSED => 'Đã đóng',
            default => 'Không xác định',
        };
    }

    /**
     * Status badge color class
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_SENT_TO_SUPPLIER => 'info',
            self::STATUS_RETURNED => 'success',
            self::STATUS_CLOSED => 'secondary',
            default => 'light',
        };
    }

    /**
     * Danh sách tất cả status để hiển thị dropdown
     */
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'Chờ xử lý',
            self::STATUS_SENT_TO_SUPPLIER => 'Đã gửi NCC',
            self::STATUS_RETURNED => 'Đã mang về',
            self::STATUS_CLOSED => 'Đã đóng',
        ];
    }
}
