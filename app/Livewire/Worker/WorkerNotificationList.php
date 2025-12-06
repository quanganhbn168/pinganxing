<?php

namespace App\Livewire\Worker;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

class WorkerNotificationList extends Component
{
    use WithPagination;

    public function markAsRead($id)
    {
        $notification = auth('admin')->user()->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
        }
    }

    public function markAllAsRead()
    {
        auth('admin')->user()->unreadNotifications->markAsRead();
        session()->flash('success', 'Đã đánh dấu tất cả là đã đọc.');
    }

    public function handleClick($id, $url)
    {
        $this->markAsRead($id);
        if ($url) {
            return redirect()->to($url);
        }
    }

    #[Layout('layouts.mobile', ['title' => 'Thông báo'])]
    public function render()
    {
        $notifications = auth('admin')->user()->notifications()->paginate(15);
        
        return view('livewire.worker.worker-notification-list', [
            'notifications' => $notifications
        ]);
    }
}
