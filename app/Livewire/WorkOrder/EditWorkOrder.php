<?php

namespace App\Livewire\WorkOrder;

use Livewire\Component;
use App\Models\WorkOrder;
use App\Models\Admin; // Model nhân viên
use Livewire\Attributes\Layout;

class EditWorkOrder extends Component
{
    public $workOrderId;
    public $code;
    public $customer_name; // Chỉ hiển thị, không sửa khách hàng ở đây (tránh lộn xộn)

    // Các trường cho phép sửa
    public $title;
    public $description;
    public $priority;
    public $site_address;
    public $contact_person;
    public $contact_phone;
    public $assignee_ids = []; // Mảng ID nhân viên
    public $tasks = []; // Danh sách nhiệm vụ

    public function mount($id)
    {
        $order = WorkOrder::with(['customer', 'assignees', 'tasks'])->findOrFail($id);

        // 1. Chặn nếu đã hoàn thành hoặc hủy
        if (in_array($order->status, ['completed', 'cancelled'])) {
            return redirect()->route('admin.work-orders.index')
                ->with('error', 'Không thể chỉnh sửa phiếu đã Hoàn thành hoặc Đã hủy.');
        }

        // 2. Đổ dữ liệu cũ vào form
        $this->workOrderId = $order->id;
        $this->code = $order->code;
        $this->customer_name = $order->customer->name;

        $this->title = $order->title;
        $this->description = $order->description;
        $this->priority = $order->priority->value ?? $order->priority;
        $this->site_address = $order->site_address;
        $this->contact_person = $order->contact_person;
        $this->contact_phone = $order->contact_phone;

        // Lấy danh sách ID nhân viên đã gán
        $this->assignee_ids = $order->assignees->pluck('id')->map(fn($id) => (string)$id)->toArray();

        // Lấy danh sách Task
        foreach ($order->tasks as $task) {
            $this->tasks[] = [
                'id' => $task->id,
                'content' => $task->report_content, // Dùng report_content làm tên task như lúc tạo
                'status' => $task->status,
                'is_deleted' => false // Flag để đánh dấu xóa
            ];
        }
    }

    public function addTask()
    {
        $this->tasks[] = [
            'id' => null, // Task mới chưa có ID
            'content' => '',
            'status' => 'pending',
            'is_deleted' => false
        ];
    }

    public function removeTask($index)
    {
        // Nếu task mới (chưa có ID) -> Xóa khỏi mảng luôn
        if (empty($this->tasks[$index]['id'])) {
            unset($this->tasks[$index]);
            $this->tasks = array_values($this->tasks);
        } else {
            // Nếu task cũ -> Đánh dấu xóa để xử lý trong DB sau
            $this->tasks[$index]['is_deleted'] = true;
        }
    }

    public function update()
    {
        $this->validate([
            'title' => 'required|min:5',
            'priority' => 'required|in:low,medium,high,urgent',
            'site_address' => 'required',
            'contact_person' => 'required',
            'contact_phone' => 'required',
            'assignee_ids' => 'array',
            'tasks.*.content' => 'required_unless:tasks.*.is_deleted,true', // Validate content nếu không bị xóa
        ]);

        $order = WorkOrder::find($this->workOrderId);

        // Cập nhật thông tin chính
        $order->update([
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority,
            'site_address' => $this->site_address,
            'contact_person' => $this->contact_person,
            'contact_phone' => $this->contact_phone,
        ]);

        // Cập nhật danh sách nhân viên
        $order->assignees()->sync($this->assignee_ids);

        // Xử lý Tasks
        // Lấy ID người thực hiện mặc định (Leader)
        $mainPerformer = $this->assignee_ids[0] ?? auth('admin')->id();

        foreach ($this->tasks as $taskData) {
            if ($taskData['is_deleted']) {
                // Xóa task cũ
                if ($taskData['id']) {
                    \App\Models\Task::destroy($taskData['id']);
                }
                continue;
            }

            if ($taskData['id']) {
                // Update task cũ
                \App\Models\Task::where('id', $taskData['id'])->update([
                    'report_content' => $taskData['content']
                ]);
            } else {
                // Tạo task mới
                \App\Models\Task::create([
                    'work_order_id' => $order->id,
                    'performer_id' => $mainPerformer,
                    'report_content' => $taskData['content'],
                    'status' => 'pending',
                    'collected_amount' => 0,
                    'is_paid' => false
                ]);
            }
        }

        session()->flash('success', "Đã cập nhật phiếu {$this->code} thành công!");
        return redirect()->route('admin.work-orders.index');
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        // Lấy list nhân viên để đổ vào Select2
        $staffs = Admin::all(); 
        return view('livewire.work-order.edit-work-order', [
            'staffs' => $staffs,
            'assignee_ids' => $this->assignee_ids
        ]);
    }
}