<?php

namespace App\Livewire\Worker;

use Livewire\Component;

class MobileBottomNav extends Component
{
    public $counts = [
        'unread' => 0,
        'pending' => 0,
        'urgent' => 0
    ];

    public function mount()
    {
        $this->updateCounts();
    }
    
    public function updateCounts()
    {
        $user = auth('admin')->user();
        if (!$user) return;

        // 1. Notification Count
        $this->counts['unread'] = $user->unreadNotifications()->count();

        // 2. Job Counts
        // Pending
        $this->counts['pending'] = \App\Models\WorkOrder::query()
            ->whereHas('assignees', function($q) use ($user) {
                $q->where('admin_id', $user->id);
            })
            ->where('status', \App\Enums\WorkOrderStatus::PENDING)
            ->count();

        // Urgent (Pending or Processing with High/Urgent priority)
        $this->counts['urgent'] = \App\Models\WorkOrder::query()
            ->whereHas('assignees', function($q) use ($user) {
                $q->where('admin_id', $user->id);
            })
            ->whereIn('status', [\App\Enums\WorkOrderStatus::PENDING, \App\Enums\WorkOrderStatus::PROCESSING])
            ->whereIn('priority', ['high', 'urgent'])
            ->count();
    }

    public function render()
    {
        return view('livewire.worker.mobile-bottom-nav');
    }
}
