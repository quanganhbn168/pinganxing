<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Admin;
use App\Models\Task;
use App\Models\TaskReport;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;

class StaffPerformance extends Component
{
    public Admin $staff;
    
    // Filter
    public $filterMonth;
    public $filterYear;

    public function mount($id)
    {
        $this->staff = Admin::with('roles')->findOrFail($id);
        $this->filterMonth = now()->month;
        $this->filterYear = now()->year;
    }

    public function getStats()
    {
        $startDate = now()->setYear($this->filterYear)->setMonth($this->filterMonth)->startOfMonth();
        $endDate = now()->setYear($this->filterYear)->setMonth($this->filterMonth)->endOfMonth();

        // Tasks được gán cho nhân viên này
        $tasksQuery = Task::where('performer_id', $this->staff->id)
            ->whereBetween('created_at', [$startDate, $endDate]);

        // Tasks đã hoàn thành (có report với is_completed = true)
        $completedTaskIds = TaskReport::where('is_completed', true)
            ->whereHas('task', fn($q) => $q->where('performer_id', $this->staff->id))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->pluck('task_id')
            ->unique();

        // Tổng tiền thu hộ
        $totalCollected = TaskReport::whereIn('task_id', $completedTaskIds)
            ->where('is_completed', true)
            ->sum('collected_amount');

        // Phân tích theo Work Order Tags
        $tagStats = DB::table('tasks')
            ->join('work_orders', 'tasks.work_order_id', '=', 'work_orders.id')
            ->join('work_order_tag', 'work_orders.id', '=', 'work_order_tag.work_order_id')
            ->join('tags', 'work_order_tag.tag_id', '=', 'tags.id')
            ->where('tasks.performer_id', $this->staff->id)
            ->whereIn('tasks.id', $completedTaskIds)
            ->select('tags.name', 'tags.color', DB::raw('COUNT(DISTINCT tasks.id) as task_count'))
            ->groupBy('tags.id', 'tags.name', 'tags.color')
            ->orderByDesc('task_count')
            ->get();

        return [
            'totalAssigned' => $tasksQuery->count(),
            'totalCompleted' => $completedTaskIds->count(),
            'totalCollected' => $totalCollected,
            'completionRate' => $tasksQuery->count() > 0 
                ? round(($completedTaskIds->count() / $tasksQuery->count()) * 100) 
                : 0,
            'tagStats' => $tagStats,
        ];
    }

    public function getRecentTasks()
    {
        $startDate = now()->setYear($this->filterYear)->setMonth($this->filterMonth)->startOfMonth();
        $endDate = now()->setYear($this->filterYear)->setMonth($this->filterMonth)->endOfMonth();

        return Task::with([
                'workOrder:id,code,title',
                'workOrder.tags:id,name,color',
                'reports' => fn($q) => $q->latest()->limit(1)
            ])
            ->where('performer_id', $this->staff->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        return view('livewire.admin.staff-performance', [
            'stats' => $this->getStats(),
            'recentTasks' => $this->getRecentTasks(),
            'months' => range(1, 12),
            'years' => range(now()->year - 2, now()->year),
        ]);
    }
}
