<?php

namespace App\Livewire\Material;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ReturnedItem;

class ReturnedMaterialList extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $filterReason = '';
    public $filterFrom = '';
    public $filterTo = '';

    public function mount()
    {
        // Default: tháng hiện tại
        $this->filterFrom = now()->startOfMonth()->format('Y-m-d');
        $this->filterTo = now()->format('Y-m-d');
    }

    public function resetFilters()
    {
        $this->reset(['search', 'filterReason']);
        $this->filterFrom = now()->startOfMonth()->format('Y-m-d');
        $this->filterTo = now()->format('Y-m-d');
    }

    public function render()
    {
        $query = ReturnedItem::with([
            'report.task.workOrder:id,code,title',
            'report.task:id,work_order_id,title',
            'report:id,task_id,created_at'
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
            'warranty' => (clone $statsQuery)->where('reason', 'warranty')->count(),
            'replace' => (clone $statsQuery)->where('reason', 'replace')->count(),
            'defective' => (clone $statsQuery)->where('reason', 'defective')->count(),
            'upgrade' => (clone $statsQuery)->where('reason', 'upgrade')->count(),
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
        ])->layout('layouts.admin');
    }
}
