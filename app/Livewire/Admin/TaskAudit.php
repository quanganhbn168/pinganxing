<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Task;
use App\Models\TaskItem;
use Livewire\Attributes\Layout;

class TaskAudit extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Filter
    public $filter_status = 'all'; // all, unpaid (chưa nộp tiền)

    // --- Biến cho Modal Sửa ---
    public $editingTask = null; // Task đang được chọn để sửa
    public $editingItems = [];  // Danh sách vật tư của task đang sửa
    
    // !!! QUAN TRỌNG: Biến riêng để sửa tiền, tránh lỗi binding trực tiếp !!!
    public $editing_amount = 0; 

    #[Layout('layouts.admin')]
    public function render()
    {
        $query = Task::with(['workOrder.customer', 'performer', 'items'])
            ->orderByDesc('created_at');

        // Lọc: Chỉ xem các task có thu tiền mà chưa nộp
        if ($this->filter_status == 'unpaid') {
            $query->where('collected_amount', '>', 0)
                  ->where('is_paid', false);
        }

        return view('livewire.admin.task-audit', [
            'tasks' => $query->paginate(15)
        ]);
    }

    // Xác nhận đã nhận tiền từ thợ
    public function confirmPayment($taskId)
    {
        $task = Task::find($taskId);
        if ($task) {
            $task->is_paid = true;
            $task->save();
            session()->flash('success', 'Đã xác nhận nhận tiền: ' . number_format($task->collected_amount) . ' đ');
        }
    }

    // Mở Modal để sửa vật tư/thông tin
    public function openEditModal($taskId)
    {
        $this->editingTask = Task::with('items')->find($taskId);
        
        // !!! Lấy tiền từ DB đổ vào biến tạm !!!
        $this->editing_amount = $this->editingTask->collected_amount;

        // Map dữ liệu items ra mảng để bind vào form
        $this->editingItems = $this->editingTask->items->map(function($item) {
            return [
                'id' => $item->id,
                'item_name' => $item->item_name,
                'serial_number' => $item->serial_number,
                'quantity' => $item->quantity,
                'price' => $item->price, // Admin điền giá vào đây
            ];
        })->toArray();

        // Mở modal bằng Browser Event
        $this->dispatch('open-audit-modal');
    }

    // Lưu lại thông tin sau khi sửa
    public function saveChanges()
    {
        // 1. Cập nhật items
        foreach ($this->editingItems as $itemData) {
            TaskItem::where('id', $itemData['id'])->update([
                'item_name' => $itemData['item_name'],
                'serial_number' => $itemData['serial_number'],
                'quantity' => $itemData['quantity'],
                'price' => $itemData['price'] ?: 0, // Lưu giá
            ]);
        }
        
        // 2. Cập nhật lại số tiền thu từ biến tạm vào DB
        // (float) str_replace... để xóa dấu phẩy nếu anh lỡ nhập kiểu 500,000
        $this->editingTask->collected_amount = (float) str_replace([',','.'], '', $this->editing_amount);
        $this->editingTask->save();

        $this->dispatch('close-audit-modal');
        session()->flash('success', 'Đã cập nhật thông tin phiếu thành công!');
    }
}