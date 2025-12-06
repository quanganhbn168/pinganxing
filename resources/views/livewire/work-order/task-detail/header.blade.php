<div class="bg-white shadow-sm border-bottom" style="height: 60px; z-index: 1020;">
    <div class="container-fluid h-100 d-flex align-items-center">
        <a href="{{ route('admin.work-orders.show', $task->work_order_id) }}" class="btn btn-light btn-sm mr-3 border-0 text-muted">
            <i class="fas fa-arrow-left fa-lg"></i>
        </a>
        <div style="flex: 1; min-width: 0;">
            <h6 class="m-0 font-weight-bold text-dark text-truncate">{{ $task->report_content }}</h6>
        </div>
    </div>
</div>