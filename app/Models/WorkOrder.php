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
        'deadline', // Hạn hoàn thành
        'started_at', // Thời điểm bắt đầu công việc
        'site_address',
        'contact_person',
        'contact_phone',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'status' => \App\Enums\WorkOrderStatus::class,
        'priority' => \App\Enums\WorkOrderPriority::class,
        'deadline' => 'datetime',
        'started_at' => 'datetime',
    ];

    /**
     * Boot method - Auto-generate unique code on create
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($workOrder) {
            if (empty($workOrder->code)) {
                $workOrder->code = static::generateUniqueCode();
            }
        });
    }

    /**
     * Generate a unique work order code
     * Format: WO-YYMMDD-XXXX (date + 4 random alphanumeric)
     * 
     * @return string
     */
    public static function generateUniqueCode(): string
    {
        $prefix = 'PAX';
        $datePart = now()->format('ymd'); // 241209
        $maxAttempts = 10;

        for ($i = 0; $i < $maxAttempts; $i++) {
            // Generate 4 random uppercase alphanumeric characters
            $randomPart = strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));
            $code = "{$prefix}-{$datePart}-{$randomPart}";

            // Check if code exists
            if (!static::where('code', $code)->exists()) {
                return $code;
            }
        }

        // Fallback: Use timestamp + micro for guaranteed uniqueness
        $timestamp = now()->format('ymdHis');
        $micro = substr(microtime(), 2, 4);
        return "{$prefix}-{$timestamp}-{$micro}";
    }


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

    // Comments/Discussion
    public function comments(): HasMany
    {
        return $this->hasMany(WorkOrderComment::class);
    }

    // Activity Logs (Timeline)
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class)->orderBy('created_at', 'desc');
    }


    // File đính kèm (ảnh, tài liệu)
    public function attachments(): HasMany
    {
        return $this->hasMany(WorkOrderAttachment::class);
    }

    // Tags (Nhãn phân loại)
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'work_order_tag');
    }

    // Payments (Các khoản thanh toán)
    public function payments(): HasMany
    {
        return $this->hasMany(WorkOrderPayment::class);
    }

    // --- FINANCIAL COMPUTED ---

    /**
     * Tổng giá trị phiếu việc (tất cả payments đã verified)
     */
    public function getTotalValueAttribute(): int
    {
        return $this->payments()
            ->where('payment_type', '!=', \App\Enums\PaymentType::COLLECTION)
            ->sum('amount');
    }

    /**
     * Tổng tiền đã thu (các payments có is_collected = true)
     */
    public function getTotalCollectedAttribute(): int
    {
        return $this->payments()->collected()->sum('amount');
    }

    /**
     * Công nợ còn lại
     */
    public function getBalanceAttribute(): int
    {
        return $this->total_value - $this->total_collected;
    }

    // --- HELPER METHODS ---

    /**
     * Kiểm tra WorkOrder đã bị khóa (hoàn thành hoặc hủy)
     */
    public function isLocked(): bool
    {
        return $this->status === \App\Enums\WorkOrderStatus::COMPLETED 
            || $this->status === \App\Enums\WorkOrderStatus::CANCELLED;
    }

    /**
     * Kiểm tra có thể báo cáo được không
     */
    public function allowsReporting(): bool
    {
        return !$this->isLocked();
    }

    /**
     * Kiểm tra có thể tạo task phát sinh không
     */
    public function allowsAdditionalTasks(): bool
    {
        return $this->status === \App\Enums\WorkOrderStatus::PROCESSING
            || $this->status === \App\Enums\WorkOrderStatus::PENDING;
    }

    /**
     * Kiểm tra đã quá hạn chưa
     */
    public function isOverdue(): bool
    {
        if (!$this->deadline) return false;
        if ($this->isLocked()) return false; // Không tính quá hạn nếu đã đóng
        return $this->deadline->isPast();
    }

    /**
     * Lấy thông tin deadline dạng array để dùng trong view
     */
    public function getDeadlineStatusAttribute(): ?array
    {
        if (!$this->deadline) return null;

        $now = now();
        $diff = $now->diff($this->deadline);
        $isOverdue = $this->deadline->isPast();
        $isLocked = $this->isLocked();

        // Nếu đã đóng, không hiện cảnh báo
        if ($isLocked) {
            return [
                'days' => 0,
                'hours' => 0,
                'is_overdue' => false,
                'label' => 'Đã hoàn thành',
                'color' => 'success',
            ];
        }

        if ($isOverdue) {
            return [
                'days' => $diff->days,
                'hours' => $diff->h,
                'is_overdue' => true,
                'label' => "Quá hạn {$diff->days} ngày",
                'color' => 'danger',
            ];
        }

        return [
            'days' => $diff->days,
            'hours' => $diff->h,
            'is_overdue' => false,
            'label' => "Còn {$diff->days} ngày {$diff->h}h",
            'color' => $diff->days <= 1 ? 'warning' : 'info',
        ];
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

    // --- VIEW ACCESSORS (Tránh PHP trong Blade) ---

    /**
     * Tính phần trăm tiến độ
     */
    public function getProgressPercentAttribute(): int
    {
        $total = $this->tasks->count();
        if ($total === 0) return 0;
        $completed = $this->tasks->where('status', \App\Enums\TaskStatus::COMPLETED)->count();
        return (int) round(($completed / $total) * 100);
    }

    /**
     * Text hiển thị tiến độ: "2/5 việc (40%)"
     */
    public function getProgressTextAttribute(): string
    {
        $total = $this->tasks->count();
        $completed = $this->tasks->where('status', \App\Enums\TaskStatus::COMPLETED)->count();
        return "{$completed}/{$total} việc ({$this->progress_percent}%)";
    }

    /**
     * Màu progress bar
     */
    public function getProgressColorAttribute(): string
    {
        $percent = $this->progress_percent;
        if ($percent == 100) return 'bg-success';
        if ($percent > 50) return 'bg-primary';
        return 'bg-warning';
    }

    /**
     * HTML badge cho priority (dùng Blade: {!! $order->priority_badge !!})
     */
    public function getPriorityBadgeAttribute(): string
    {
        if (!$this->priority) {
            return '<span class="badge badge-secondary">N/A</span>';
        }
        return '<span class="badge badge-' . $this->priority->color() . '">' . $this->priority->label() . '</span>';
    }

    /**
     * Lấy SĐT khách hàng nhanh
     */
    public function getCustomerPhoneAttribute(): string
    {
        return $this->customer?->contacts?->where('type', 'phone')->first()?->value ?? '---';
    }

    /**
     * Scope: Chỉ lấy work order được gán cho user
     */
    public function scopeAssignedTo($query, $adminId)
    {
        return $query->whereHas('assignees', fn($q) => $q->where('admin_id', $adminId));
    }
}
