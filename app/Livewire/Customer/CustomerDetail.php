<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use App\Models\Customer;
use App\Models\Task; // Import Model Task
use Livewire\Attributes\Layout;

class CustomerDetail extends Component
{
    public $customer;
    public $stats = [];

    public function mount($id)
    {
        // Eager load sâu để lấy hết dữ liệu: Khách -> Job -> Task -> Vật tư & Người làm
        $this->customer = Customer::with([
            'contacts',
            'workOrders' => function($q) {
                $q->latest()->with(['tasks.items', 'tasks.performer']);
            }
        ])->findOrFail($id);

        // Tính toán số liệu thống kê (CRM)
        // 1. Tổng chi tiêu (Dựa trên số tiền thực thu từ các Task)
        $totalSpent = 0;
        foreach ($this->customer->workOrders as $job) {
            foreach ($job->tasks as $task) {
                $totalSpent += $task->collected_amount;
            }
        }

        // 2. Lần cuối tương tác
        $lastInteraction = $this->customer->workOrders->first()?->created_at;

        $this->stats = [
            'total_jobs' => $this->customer->workOrders->count(),
            'total_spent' => $totalSpent,
            'last_date' => $lastInteraction ? $lastInteraction->format('d/m/Y') : 'Chưa có',
            // Giả định công nợ: Hiện tại mình chưa có trường "Tổng giá trị Job" nên chưa tính được nợ chính xác. 
            // Tạm thời em để placeholder.
            'debt' => 0 
        ];
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        return view('livewire.customer.customer-detail');
    }
}