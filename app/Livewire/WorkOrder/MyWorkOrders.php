<?php

namespace App\Livewire\WorkOrder;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

class MyWorkOrders extends Component
{
    #[Layout('layouts.admin')]
    public function render()
    {
        // Lấy user hiện tại
        $user = Auth::guard('admin')->user();

        // Lấy danh sách Job được gán, sắp xếp mới nhất lên đầu
        // Nếu là Super Admin (ID=1) thì cho xem hết (tùy anh, ở đây mình làm đúng logic thợ)
        $orders = $user->assignedWorkOrders()
            ->with(['customer', 'tasks']) // Eager load để tránh N+1 query
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.work-order.my-work-orders', [
            'orders' => $orders
        ]);
    }
}