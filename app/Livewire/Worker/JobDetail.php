<?php

namespace App\Livewire\Worker;

use Livewire\Component;
use App\Models\WorkOrder;
use App\Models\Task; 
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url; 

class JobDetail extends Component
{
    public $workOrder;
    public $tasks;
    
    #[Url(as: 'tab')] 
    public $activeTab = 'progress';

    // Financial Data
    public $totalCollected = 0;
    public $allItems = [];
    public $allPayments = [];
    public $allReports = [];

    public function mount($id)
    {
        // Workers can only see jobs assigned to them or relevant to them? 
        // For now, we allow them to see the job if they know the ID, but normally we check permissions.
        // In 'admin' guard, we assume they are the assignee or related.
        // Ideally: check if $user->id is in assignment list. 
        
        $this->workOrder = WorkOrder::with(['customer.contacts', 'tasks.reports.items', 'tasks.reports.images', 'tasks.reports.reporter', 'creator'])
            ->findOrFail($id);
        
        $this->refreshTasks(); 
        
        // Tự động sửa trạng thái nếu đã làm việc mà vẫn đang Pending (Self-healing)
        if ($this->workOrder->status === \App\Enums\WorkOrderStatus::PENDING) {
             $hasProgress = $this->workOrder->tasks->contains(function($task) {
                 return $task->status === \App\Enums\TaskStatus::COMPLETED || $task->reports->count() > 0;
             });

             if ($hasProgress) {
                 $this->workOrder->update(['status' => \App\Enums\WorkOrderStatus::PROCESSING]);
                 $this->workOrder->refresh(); // Reload model
             }
        }
    }

    public function refreshTasks()
    {
        $this->tasks = $this->workOrder->tasks()->with(['reports.items', 'performer'])->orderBy('id', 'asc')->get();
        
        // Aggregate all reports for History tab
        $this->allReports = $this->workOrder->tasks
            ->flatMap(function($task) { return $task->reports; })
            ->sortByDesc('created_at');

        // Calculate financials just for display (read-only for worker here, mostly)
        $this->calculateFinancials();
    }

    public function calculateFinancials()
    {
        $this->totalCollected = 0;
        $this->allItems = [];
        $this->allPayments = [];

        foreach ($this->tasks as $task) {
            foreach ($task->reports as $report) {
                // 1. Materials
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

                // 2. Payments (Handed over or Verified)
                if ($report->collected_amount > 0) {
                     // Workers see what they collected or what was collected on the job
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

    public function quickFinishTask($taskId)
    {
        $task = $this->tasks->find($taskId);

        if (!$task || $task->status === \App\Enums\TaskStatus::COMPLETED) {
            return;
        }

        // Must have at least one report to finish
        if ($task->reports->count() == 0) {
            $this->dispatch('error', 'Chưa có báo cáo nào, không thể chốt xong!');
            return;
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($task) {
            \App\Models\TaskReport::create([
                'task_id' => $task->id,
                'reporter_id' => auth('admin')->id(),
                'content' => 'Hoàn thành: ' . $task->report_content,
                'is_completed' => true,
            ]);

            $task->update(['status' => \App\Enums\TaskStatus::COMPLETED]);

            // Cập nhật trạng thái phiếu việc
            // 1. Nếu đang là Pending -> Chuyển sang Processing
            if ($this->workOrder->status === \App\Enums\WorkOrderStatus::PENDING) {
                $this->workOrder->update(['status' => \App\Enums\WorkOrderStatus::PROCESSING]);
            }
            
            // 2. Kiểm tra nếu TẤT CẢ task đã xong -> Chuyển phiếu sang Completed
            if ($this->workOrder->tasks()->where('status', '!=', \App\Enums\TaskStatus::COMPLETED)->count() == 0) {
                 $this->workOrder->update(['status' => \App\Enums\WorkOrderStatus::COMPLETED]);
            }
        });

        $this->refreshTasks(); 
        session()->flash('message', 'Đã chốt xong nhiệm vụ #' . $taskId);
    }

    public function reopenTask($taskId)
    {
        $task = $this->tasks->find($taskId);

        if ($task && $task->status === \App\Enums\TaskStatus::COMPLETED) {
            
            \Illuminate\Support\Facades\DB::transaction(function () use ($task) {
                // Ghi log mở lại
                \App\Models\TaskReport::create([
                    'task_id' => $task->id,
                    'reporter_id' => auth('admin')->id(),
                    'content' => 'Mở lại: ' . $task->report_content,
                    'is_completed' => false,
                ]);

                $task->update(['status' => \App\Enums\TaskStatus::PROCESSING]);
                
                // Re-open Work Order if needed
                if($this->workOrder->status === \App\Enums\WorkOrderStatus::COMPLETED) {
                     $this->workOrder->update(['status' => \App\Enums\WorkOrderStatus::PROCESSING]);
                }
            });

            $this->refreshTasks();
            session()->flash('message', 'Đã mở lại nhiệm vụ #' . $taskId);
        }
    }

    public function startJob()
    {
        if ($this->workOrder->status !== \App\Enums\WorkOrderStatus::PENDING) {
            return;
        }

        \Illuminate\Support\Facades\DB::transaction(function () {
             $this->workOrder->update(['status' => \App\Enums\WorkOrderStatus::PROCESSING]);
             
             // Ghi log
             // Create a dummy task report or just a log? 
             // Currently TaskReport is linked to Task. We don't have a direct WorkOrder log table yet 
             // other than activity log if installed. 
             // We can use the first task to log "Started Job" or just leave it as status update.
             // For now, simple status update is enough as the UI will reflect it.
        });
        
        $this->refreshTasks(); 
        session()->flash('message', 'Đã bắt đầu công việc! Chúc anh làm việc hiệu quả.');
    }

    public function render()
    {
        return view('livewire.worker.job-detail')
            ->layout('layouts.mobile', ['title' => $this->workOrder->title]);
    }
}
