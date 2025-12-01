<?php

namespace App\Livewire\WorkOrder;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\WorkOrder;
use Livewire\Attributes\Layout;

class WorkOrderList extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    // Bộ lọc
    public $search = '';
    public $status = 'all'; // all, pending, processing, completed, cancelled

    #[Layout('layouts.admin')]
    public function render()
    {
        $query = WorkOrder::query()
            ->with(['customer', 'assignees', 'tasks']) // Eager load cho nhẹ
            ->orderBy('created_at', 'desc');

        // Lọc theo từ khóa (Mã job, Tên job, Tên khách)
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('code', 'like', '%' . $this->search . '%')
                  ->orWhere('title', 'like', '%' . $this->search . '%')
                  ->orWhereHas('customer', function($c) {
                      $c->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Lọc theo trạng thái
        if ($this->status != 'all') {
            $query->where('status', $this->status);
        }

        return view('livewire.work-order.work-order-list', [
            'orders' => $query->paginate(10)
        ]);
    }

    // --- HÀNH ĐỘNG CỦA QUẢN TRÒ ---
    
    // Đóng Job (Hoàn thành)
    public function markAsCompleted($id)
    {
        $order = WorkOrder::find($id);
        if ($order) {
            $order->update(['status' => 'completed']);
            session()->flash('success', "Đã đóng Job {$order->code} thành công!");
        }
    }

    // Hủy Job (Nếu khách boom hàng hoặc hủy kèo)
    public function markAsCancelled($id)
    {
        $order = WorkOrder::find($id);
        if ($order) {
            $order->update(['status' => 'cancelled']);
            session()->flash('success', "Đã hủy Job {$order->code}.");
        }
    }

    // Mở lại Job (Nếu nhầm)
    public function markAsProcessing($id)
    {
        $order = WorkOrder::find($id);
        if ($order) {
            $order->update(['status' => 'processing']);
            session()->flash('success', "Đã mở lại Job {$order->code}.");
        }
    }
}