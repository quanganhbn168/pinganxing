<?php

namespace App\Livewire\WorkOrder;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\WorkOrder;
use App\Enums\WorkOrderStatus;
use App\Enums\WorkOrderPriority;
use Livewire\Attributes\Layout;

class WorkOrderList extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    // Bộ lọc
    public $search = '';
    public $status = 'all';

    /**
     * Base query - có filter theo role nếu là Staff
     */
    protected function getBaseQuery()
    {
        $user = auth('admin')->user();
        
        $query = WorkOrder::query()
            ->with(['customer.contacts', 'assignees', 'tasks', 'warrantyService', 'tags'])
            ->orderBy('created_at', 'desc');

        // STAFF CHỈ THẤY VIỆC CỦA MÌNH
        if ($user->hasRole('staff')) {
            $query->assignedTo($user->id);
        }

        return $query;
    }

    /**
     * Counts cho button tabs (Staff view)
     */
    public function getCountsProperty(): array
    {
        $base = $this->getBaseQuery();
        
        return [
            'all' => (clone $base)->count(),
            'pending' => (clone $base)->where('status', WorkOrderStatus::PENDING)->count(),
            'processing' => (clone $base)->where('status', WorkOrderStatus::PROCESSING)->count(),
            'urgent' => (clone $base)
                ->whereIn('priority', [WorkOrderPriority::URGENT->value, WorkOrderPriority::HIGH->value])
                ->whereNotIn('status', [WorkOrderStatus::COMPLETED, WorkOrderStatus::CANCELLED])
                ->count(),
            'completed' => (clone $base)->where('status', WorkOrderStatus::COMPLETED)->count(),
        ];
    }

    /**
     * Lấy danh sách options cho bộ lọc status (Admin view)
     */
    public function getStatusOptionsProperty(): array
    {
        $options = [['value' => 'all', 'label' => '-- Trạng thái --']];
        foreach (WorkOrderStatus::cases() as $s) {
            $options[] = ['value' => $s->value, 'label' => $s->label()];
        }
        return $options;
    }

    /**
     * Kiểm tra user có thể tạo work order không
     */
    public function getCanCreateProperty(): bool
    {
        return auth('admin')->user()?->can('create_work_orders') ?? false;
    }

    /**
     * Kiểm tra user là Staff không (để chọn view)
     */
    public function getIsStaffProperty(): bool
    {
        return auth('admin')->user()?->hasRole('staff') ?? false;
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        $query = $this->getBaseQuery();

        // Lọc theo từ khóa
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('code', 'like', '%' . $this->search . '%')
                  ->orWhere('title', 'like', '%' . $this->search . '%')
                  ->orWhere('contact_phone', 'like', '%' . $this->search . '%')
                  ->orWhereHas('customer', function($c) {
                      $c->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Lọc theo trạng thái
        if ($this->status != 'all') {
            if ($this->status === 'urgent') {
                // Urgent = priority high/urgent + not completed
                $query->whereIn('priority', [WorkOrderPriority::URGENT->value, WorkOrderPriority::HIGH->value])
                      ->whereNotIn('status', [WorkOrderStatus::COMPLETED, WorkOrderStatus::CANCELLED]);
            } else {
                $query->where('status', $this->status);
            }
        }

        $orders = $query->paginate(10);

        // Chọn view theo role
        $view = $this->isStaff 
            ? 'livewire.work-order.work-order-list-staff'
            : 'livewire.work-order.work-order-list';

        return view($view, [
            'orders' => $orders
        ])->layout(auth('admin')->user()->layout);
    }

    // --- HÀNH ĐỘNG CỦA QUẢN TRÒ ---
    
    public function markAsCompleted($id)
    {
        $order = WorkOrder::find($id);
        if ($order && auth('admin')->user()->can('approve_work_orders')) {
            $order->update([
                'status' => WorkOrderStatus::COMPLETED,
                'approved_at' => now(),
                'approved_by' => auth('admin')->id(),
            ]);
            session()->flash('success', "Đã đóng Job {$order->code} thành công!");
        }
    }

    public function markAsCancelled($id)
    {
        $order = WorkOrder::find($id);
        if ($order && auth('admin')->user()->can('approve_work_orders')) {
            $order->update(['status' => WorkOrderStatus::CANCELLED]);
            session()->flash('success', "Đã hủy Job {$order->code}.");
        }
    }

    public function markAsProcessing($id)
    {
        $order = WorkOrder::find($id);
        if ($order && auth('admin')->user()->can('approve_work_orders')) {
            $order->update(['status' => WorkOrderStatus::PROCESSING]);
            session()->flash('success', "Đã mở lại Job {$order->code}.");
        }
    }

    /**
     * Đồng bộ hóa lại dữ liệu tiêu đề và nội dung báo cáo cho TOÀN BỘ task
     */
    public function syncAll()
    {
        if (!auth('admin')->user()->can('update_work_orders')) {
            return;
        }

        // Chunking để tránh memory leak nếu dữ liệu lớn
        \App\Models\Task::chunk(100, function ($tasks) {
            foreach ($tasks as $task) {
                // Logic: Nếu title rỗng thì lấy report_content, sau đó đồng bộ report_content theo title
                $title = $task->title ?: $task->report_content;
                
                // Nếu cả 2 đều rỗng thì bỏ qua hoặc để mặc định (ở đây chỉ xử lý khi có dữ liệu)
                if ($title) {
                    $task->update([
                        'title' => $title,
                        'report_content' => $title
                    ]);
                }
            }
        });

        session()->flash('success', "Đã đồng bộ hóa dữ liệu cho tất cả Work Orders thành công!");
    }
}