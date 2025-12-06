<?php

namespace App\Livewire\Worker;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Enums\WorkOrderStatus;

class MyJobList extends Component
{
    use WithPagination;

    public $filter = 'unfinished';

    public function mount()
    {
        $this->filter = request()->query('filter', 'unfinished');
    }

    public function setFilter($filter)
    {
        $this->filter = $filter;
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::guard('admin')->user();
        
        $baseQuery = $user->assignedWorkOrders()
            // Explicitly select work_orders columns to avoid collision with pivot tables
            ->select('work_orders.*') 
            ->with(['customer', 'tasks']);

        // Calculate counters for the Dashboard
        $counts = [
            'all' => $baseQuery->count(), 
            
        // "Việc khẩn cấp" (Priority Urgent or High) AND Not Completed
            'urgent' => (clone $baseQuery)->whereIn('priority', ['urgent', 'high'])
                                          ->where('status', '!=', WorkOrderStatus::COMPLETED)
                                          ->where('status', '!=', WorkOrderStatus::CANCELLED)
                                          ->count(),

            // "Việc cần làm" (Pending)
            'pending' => (clone $baseQuery)->where('status', WorkOrderStatus::PENDING)->count(),

            // "Việc đang làm" (Processing)
            'processing' => (clone $baseQuery)->where('status', WorkOrderStatus::PROCESSING)->count(),

            // "Việc chưa làm" (Unfinished / Active = Pending + Processing + Pending Approval)
            'unfinished' => (clone $baseQuery)->whereNotIn('status', [
                WorkOrderStatus::COMPLETED,
                WorkOrderStatus::CANCELLED
            ])->count(),

            // "Việc đã làm" (Completed)
            'completed' => (clone $baseQuery)->where('status', WorkOrderStatus::COMPLETED)->count(),

            // Placeholder for notifications
            'notifications' => 0 
        ];

        // Apply filter to the main list
        $query = $baseQuery->clone();

        match ($this->filter) {
            'unfinished' => $query->whereNotIn('status', [WorkOrderStatus::COMPLETED, WorkOrderStatus::CANCELLED]),
            'pending'    => $query->where('status', WorkOrderStatus::PENDING),
            'processing' => $query->where('status', WorkOrderStatus::PROCESSING),
            'urgent'     => $query->whereIn('priority', ['urgent', 'high'])
                                  ->whereNotIn('status', [WorkOrderStatus::COMPLETED, WorkOrderStatus::CANCELLED]),
            'completed'  => $query->where('status', WorkOrderStatus::COMPLETED),
            default      => null, // 'all' or others: No extra filter
        };

        // filter == 'notifications' -> handled in view (maybe hide list?) used for counter only now.

        $jobs = $query->orderBy('priority', 'desc')
                      ->orderBy('work_orders.created_at', 'desc')
                      ->paginate(10);

        return view('livewire.worker.my-job-list', [
            'jobs' => $jobs,
            'counts' => $counts
        ])->layout('layouts.mobile', ['title' => 'Tổng quan công việc']); 
    }
}
