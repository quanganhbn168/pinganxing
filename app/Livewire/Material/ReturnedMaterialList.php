<?php

namespace App\Livewire\Material;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ReturnedItem;
use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;

class ReturnedMaterialList extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $filterReason = '';
    public $filterStatus = '';
    public $filterFrom = '';
    public $filterTo = '';

    // Modal state
    public $editingItemId = null;
    public $editSupplierId = null;
    public $editNotes = '';

    public function mount()
    {
        // Default: tháng hiện tại
        $this->filterFrom = now()->startOfMonth()->format('Y-m-d');
        $this->filterTo = now()->format('Y-m-d');
    }

    public function resetFilters()
    {
        $this->reset(['search', 'filterReason', 'filterStatus']);
        $this->filterFrom = now()->startOfMonth()->format('Y-m-d');
        $this->filterTo = now()->format('Y-m-d');
    }

    /**
     * Cập nhật status nhanh
     */
    public function updateStatus($itemId, $newStatus)
    {
        $item = ReturnedItem::find($itemId);
        if (!$item) return;

        $item->status = $newStatus;
        
        // Nếu đánh dấu "Đã mang về", ghi nhận người và thời gian
        if ($newStatus === ReturnedItem::STATUS_RETURNED) {
            $item->returned_by = Auth::guard('admin')->id();
            $item->returned_at = now();
        }
        
        $item->save();
        $this->dispatch('notify', type: 'success', message: 'Đã cập nhật trạng thái!');
    }

    /**
     * Mở modal để chỉnh sửa thông tin chi tiết
     */
    public function openEditModal($itemId)
    {
        $item = ReturnedItem::find($itemId);
        if (!$item) return;

        $this->editingItemId = $itemId;
        $this->editSupplierId = $item->supplier_id;
        $this->editNotes = $item->notes ?? '';
        
        $this->dispatch('open-edit-modal');
    }

    /**
     * Lưu thông tin chi tiết (supplier, notes)
     */
    public function saveDetails()
    {
        $item = ReturnedItem::find($this->editingItemId);
        if (!$item) return;

        $item->supplier_id = $this->editSupplierId;
        $item->notes = $this->editNotes;
        
        // Nếu có gán NCC và status còn pending, tự động chuyển sang sent_to_supplier
        if ($this->editSupplierId && $item->status === ReturnedItem::STATUS_PENDING) {
            $item->status = ReturnedItem::STATUS_SENT_TO_SUPPLIER;
        }
        
        $item->save();
        
        $this->reset(['editingItemId', 'editSupplierId', 'editNotes']);
        $this->dispatch('close-edit-modal');
        $this->dispatch('notify', type: 'success', message: 'Đã lưu thông tin!');
    }

    public function render()
    {
        $query = ReturnedItem::with([
            'report.task.workOrder:id,code,title',
            'report.task:id,work_order_id,title',
            'report:id,task_id,created_at',
            'supplier:id,name',
            'returnedByAdmin:id,name'
        ]);

        // Search by item name or serial
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('item_name', 'like', '%' . $this->search . '%')
                  ->orWhere('serial_number', 'like', '%' . $this->search . '%');
            });
        }

        // Filter by reason
        if ($this->filterReason) {
            $query->where('reason', $this->filterReason);
        }

        // Filter by status
        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        // Filter by date range (via report created_at)
        if ($this->filterFrom) {
            $query->whereHas('report', function ($q) {
                $q->whereDate('created_at', '>=', $this->filterFrom);
            });
        }
        if ($this->filterTo) {
            $query->whereHas('report', function ($q) {
                $q->whereDate('created_at', '<=', $this->filterTo);
            });
        }

        $items = $query->orderByDesc('id')->paginate(20);

        // Stats
        $statsQuery = ReturnedItem::query();
        if ($this->filterFrom) {
            $statsQuery->whereHas('report', fn($q) => $q->whereDate('created_at', '>=', $this->filterFrom));
        }
        if ($this->filterTo) {
            $statsQuery->whereHas('report', fn($q) => $q->whereDate('created_at', '<=', $this->filterTo));
        }

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'pending' => (clone $statsQuery)->where('status', ReturnedItem::STATUS_PENDING)->count(),
            'sent_to_supplier' => (clone $statsQuery)->where('status', ReturnedItem::STATUS_SENT_TO_SUPPLIER)->count(),
            'returned' => (clone $statsQuery)->where('status', ReturnedItem::STATUS_RETURNED)->count(),
            'closed' => (clone $statsQuery)->where('status', ReturnedItem::STATUS_CLOSED)->count(),
        ];

        $reasons = [
            'warranty' => 'Bảo hành',
            'replace' => 'Đổi model',
            'defective' => 'Lỗi nhà SX',
            'upgrade' => 'Nâng cấp',
        ];

        return view('livewire.material.returned-material-list', [
            'items' => $items,
            'stats' => $stats,
            'reasons' => $reasons,
            'statuses' => ReturnedItem::getStatusOptions(),
            'suppliers' => Supplier::active()->orderBy('name')->get(['id', 'name']),
        ])->layout('layouts.admin');
    }
}
