<?php

namespace App\Livewire\WorkOrder;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\WorkOrder;
use App\Models\Task;
use App\Models\TaskItem;
use Illuminate\Support\Facades\DB;

class WorkOrderDetail extends Component
{
    public $workOrder;
    public $tasks; // Lịch sử công việc

    // --- FORM TẠO TASK MỚI ---
    public $showTaskForm = false; // Ẩn/Hiện form
    public $report_content;
    public $collected_amount = 0;
    
    // Mảng chứa vật tư: [['name' => '', 'serial' => '', 'qty' => 1]]
    public $items = []; 

    public function mount($id)
    {
        // Load Job kèm thông tin khách và người tạo
        $this->workOrder = WorkOrder::with(['customer.contacts', 'creator'])->findOrFail($id);
        $this->loadTasks();
        
        // Khởi tạo 1 dòng vật tư trống mặc định
        $this->items[] = ['name' => '', 'serial' => '', 'qty' => 1];
    }
    
    public function loadTasks()
    {
        // Load lịch sử task kèm vật tư và người làm
        $this->tasks = Task::with(['items', 'performer'])
            ->where('work_order_id', $this->workOrder->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // Thêm dòng vật tư mới
    public function addItem()
    {
        $this->items[] = ['name' => '', 'serial' => '', 'qty' => 1];
    }

    // Xóa dòng vật tư
    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items); // Đánh lại index
    }

    public function saveTask()
    {
        if (in_array($this->workOrder->status, ['completed', 'cancelled'])) {
            $this->dispatch('notify', type: 'error', message: 'Phiếu việc này đã đóng hoặc hủy. Không thể thêm báo cáo!');
            return;
        }
        $this->validate([
            'report_content' => 'required|min:5',
            'collected_amount' => 'numeric|min:0',
            'items.*.name' => 'nullable|string', // Tên vật tư có thể trống nếu chỉ làm dịch vụ
        ]);

        DB::transaction(function () {
            // 1. Tạo Task
            $task = Task::create([
                'work_order_id' => $this->workOrder->id,
                'performer_id' => auth('admin')->id(),
                'report_content' => $this->report_content,
                'collected_amount' => $this->collected_amount ?: 0,
                'is_paid' => false // Tiền thợ cầm, chưa nộp về cty
            ]);

            // 2. Tạo Task Items (Vật tư)
            foreach ($this->items as $item) {
                if (!empty($item['name'])) {
                    TaskItem::create([
                        'task_id' => $task->id,
                        'item_name' => $item['name'],
                        'serial_number' => $item['serial'] ?? null,
                        'quantity' => $item['qty'] ?? 1,
                        // Price để null, admin điền sau
                    ]);
                }
            }
            
            // 3. Cập nhật trạng thái Job sang "Processing" nếu đang Pending
            if ($this->workOrder->status == 'pending') {
                $this->workOrder->update(['status' => 'processing']);
            }
        });

        // Reset form
        $this->reset(['report_content', 'collected_amount', 'showTaskForm']);
        $this->items = [['name' => '', 'serial' => '', 'qty' => 1]];
        $this->loadTasks(); // Reload lại lịch sử
        
        session()->flash('success', 'Đã báo cáo công việc thành công!');
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        return view('livewire.work-order.work-order-detail');
    }
}