<?php

namespace App\Livewire\WorkOrder;

use Livewire\Component;
use App\Models\WorkOrder;
use App\Models\Task; 
use App\Models\TaskReport;
use App\Models\TaskItem;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url; // Import Url attribute

class WorkOrderDetail extends Component
{
    public $workOrder;
    public $tasks;
    
    #[Url(as: 'tab')] // Bind query param 'tab' to $activeTab
    public $activeTab = 'progress';

    // Financial Data
    // Financial Data
    public $totalCollected = 0;
    public $allItems = [];
    public $allPayments = [];
    public $allReports = []; // Added missing property

    // Material Management
    public $showMaterialModal = false;
    public $newMaterial = [
        'task_id' => '',
        'name' => '',
        'serial' => '',
        'quantity' => 1,
        'price' => 0,
    ];

    public function mount($id)
    {
        $this->workOrder = WorkOrder::with(['customer.contacts', 'tasks.reports.items', 'tasks.reports', 'creator'])
            ->findOrFail($id);
        $this->refreshTasks(); 
    }

    public function refreshTasks()
    {
        $this->tasks = $this->workOrder->tasks()->with(['reports.items', 'performer'])->orderBy('id', 'asc')->get();
        
        // Aggregate all reports for History tab
        $this->allReports = $this->workOrder->tasks
            ->flatMap(function($task) { return $task->reports; })
            ->sortByDesc('created_at');

        $this->calculateFinancials();
    }

    public function calculateFinancials()
    {
        $this->totalCollected = 0;
        $this->allItems = [];
        $this->allPayments = [];

        foreach ($this->tasks as $task) {
            foreach ($task->reports as $report) {
                // 1. Tổng hợp vật tư
                foreach ($report->items as $item) {
                    $this->allItems[] = [
                        'id' => $item->id,
                        'name' => $item->item_name,
                        'serial' => $item->serial_number,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'task_id' => $task->id,
                        'report_date' => $report->created_at
                    ];
                }

                // 2. Tổng hợp thanh toán
                if ($report->collected_amount > 0) {
                    // Chỉ tính những khoản đã được kế toán xác nhận (verified hoặc handed_over)
                    if (in_array($report->finance_status, ['verified', 'handed_over'])) {
                        $this->totalCollected += $report->collected_amount;
                    }
                    
                    $this->allPayments[] = [
                        'amount' => $report->collected_amount,
                        'method' => $report->payment_method ?? 'cash',
                        'target' => $report->transfer_target,
                        'reporter' => $report->reporter->name ?? 'N/A',
                        'date' => $report->created_at,
                        'status' => $report->finance_status
                    ];
                }
            }
        }
    }

    // --- MATERIAL MANAGEMENT ---

    public function openMaterialModal()
    {
        $this->newMaterial = [
            'task_id' => $this->tasks->first()->id ?? '',
            'name' => '',
            'serial' => '',
            'quantity' => 1,
            'price' => 0,
        ];
        $this->showMaterialModal = true;
    }

    public function saveMaterial()
    {
        $this->validate([
            'newMaterial.task_id' => 'required|exists:tasks,id',
            'newMaterial.name' => 'required|string|max:255',
            'newMaterial.quantity' => 'required|integer|min:1',
            'newMaterial.price' => 'required|numeric|min:0',
        ]);

        // 1. Tạo (hoặc tìm) báo cáo để gắn vật tư
        // Ở đây ta tạo mới 1 báo cáo "System" để ghi nhận việc Admin thêm vật tư
        $report = TaskReport::create([
            'task_id' => $this->newMaterial['task_id'],
            'reporter_id' => auth('admin')->id(),
            'content' => 'Cập nhật vật tư từ Admin',
            'is_completed' => false,
        ]);

        // 2. Tạo vật tư
        TaskItem::create([
            'task_report_id' => $report->id,
            'item_name' => $this->newMaterial['name'],
            'serial_number' => $this->newMaterial['serial'],
            'quantity' => $this->newMaterial['quantity'],
            'price' => $this->newMaterial['price'],
        ]);

        $this->showMaterialModal = false;
        session()->flash('message', 'Đã thêm vật tư thành công.');
        $this->refreshTasks();
    }

    public function deleteMaterial($itemId)
    {
        $item = TaskItem::find($itemId);
        if ($item) {
            // Nếu báo cáo chỉ có 1 vật tư này và nội dung là "Cập nhật vật tư từ Admin" thì xóa luôn báo cáo cho sạch
            $report = $item->report;
            $item->delete();

            if ($report && $report->items()->count() == 0 && $report->content == 'Cập nhật vật tư từ Admin') {
                $report->delete();
            }

            session()->flash('message', 'Đã xóa vật tư.');
            $this->refreshTasks();
        }
    }

    // --- HÀM HOÀN THÀNH NHANH TASK (ADMIN) ---
    public function quickFinishTask($taskId)
    {
        $task = Task::find($taskId);
        
        // Chỉ cho phép nếu task chưa xong
        if ($task && $task->status !== \App\Enums\TaskStatus::COMPLETED) {
            
            // 1. Cập nhật trạng thái Task
            $task->update(['status' => \App\Enums\TaskStatus::COMPLETED]);

            // 2. Kiểm tra xem có báo cáo nào chưa?
            if ($task->reports()->count() == 0) {
                 $this->dispatch('alert', ['type' => 'error', 'message' => 'Nhiệm vụ chưa có báo cáo nào, không thể hoàn thành!']);
                 return;
            }

            // 3. Tạo báo cáo tự động (System Log)
            TaskReport::create([
                'task_id' => $task->id,
                'reporter_id' => auth('admin')->id(),
                'content' => 'Hoàn thành bởi Admin',
                'is_completed' => true,
            ]);

            // 3. Kiểm tra xem tất cả task đã xong chưa?
            $allTasksCompleted = $this->workOrder->tasks()->where('status', '!=', \App\Enums\TaskStatus::COMPLETED)->count() == 0;
            
            if ($allTasksCompleted) {
                // Nếu xong hết rồi thì không tự động đóng WorkOrder like Worker, 
                // mà để Admin tự quyết định nút "Duyệt". 
                // Hoặc có thể tự chuyển sang Pending Approval.
                // Tạm thời giữ nguyên trạng thái WorkOrder để Admin review.
            }

            session()->flash('message', 'Đã đánh dấu hoàn thành nhiệm vụ.');
            $this->refreshTasks();
        }
    }

    public function reopenTask($taskId)
    {
        $task = Task::find($taskId);
        if ($task && $task->status === \App\Enums\TaskStatus::COMPLETED) {
            $task->update(['status' => \App\Enums\TaskStatus::PROCESSING]);
            
            // Nếu WorkOrder đang ở trạng thái chờ duyệt (pending_approval) thì cũng phải quay về processing
            if($this->workOrder->status === \App\Enums\WorkOrderStatus::PENDING_APPROVAL) {
                $this->workOrder->update(['status' => \App\Enums\WorkOrderStatus::PROCESSING]);
            }

            session()->flash('message', 'Đã mở lại công việc thành công.');
            $this->refreshTasks(); // Load lại danh sách để cập nhật giao diện
        }
    }

    public function updateWorkOrderStatus($status)
    {
        $statusEnum = \App\Enums\WorkOrderStatus::tryFrom($status);
        if ($statusEnum) {
            $this->workOrder->update(['status' => $statusEnum]);
            session()->flash('message', 'Đã cập nhật trạng thái đơn hàng.');
        }
    }

    public function render()
    {
        return view('livewire.work-order.work-order-detail')
            ->layout(auth('admin')->user()->layout);
    }
}