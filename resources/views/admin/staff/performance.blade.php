@extends('layouts.admin')

@section('title', 'Hiệu Suất: ' . $staff->name)

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="mb-1">
                    <i class="fas fa-chart-line text-primary"></i> 
                    Báo Cáo Hiệu Suất
                </h1>
                <p class="text-muted mb-0">
                    <i class="fas fa-user"></i> {{ $staff->name }}
                    <span class="mx-2">|</span>
                    <i class="fas fa-phone"></i> {{ $staff->phone }}
                </p>
            </div>
            <a href="{{ route('admin.staff.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        
        {{-- Filter Form --}}
        <div class="card card-outline card-primary">
            <div class="card-header py-2">
                <h3 class="card-title"><i class="fas fa-filter"></i> Bộ lọc thời gian</h3>
            </div>
            <div class="card-body py-3">
                <form action="{{ route('admin.staff.performance', $staff->id) }}" method="GET" class="row align-items-end">
                    <div class="col-md-3">
                        <label class="mb-1">Từ ngày</label>
                        <input type="date" name="from" value="{{ $from }}" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="mb-1">Đến ngày</label>
                        <input type="date" name="to" value="{{ $to }}" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Lọc
                        </button>
                        <a href="{{ route('admin.staff.performance', $staff->id) }}" class="btn btn-default">
                            <i class="fas fa-sync"></i> Reset
                        </a>
                    </div>
                    <div class="col-md-3 text-right">
                        <small class="text-muted">
                            Đang xem: {{ \Carbon\Carbon::parse($from)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($to)->format('d/m/Y') }}
                        </small>
                    </div>
                </form>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="row">
            <div class="col-lg-2 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $stats['total'] }}</h3>
                        <p>Tổng Task</p>
                    </div>
                    <div class="icon"><i class="fas fa-tasks"></i></div>
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $stats['completed'] }}</h3>
                        <p>Hoàn thành</p>
                    </div>
                    <div class="icon"><i class="fas fa-check-circle"></i></div>
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>{{ $stats['processing'] }}</h3>
                        <p>Đang xử lý</p>
                    </div>
                    <div class="icon"><i class="fas fa-spinner"></i></div>
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $stats['pending'] }}</h3>
                        <p>Chờ xử lý</p>
                    </div>
                    <div class="icon"><i class="fas fa-clock"></i></div>
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h3>{{ $stats['cancelled'] }}</h3>
                        <p>Đã hủy</p>
                    </div>
                    <div class="icon"><i class="fas fa-times-circle"></i></div>
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <div class="small-box bg-gradient-teal">
                    <div class="inner">
                        <h3>{{ number_format($stats['collected'] / 1000) }}<sup style="font-size: 16px">K</sup></h3>
                        <p>Tiền thu hộ</p>
                    </div>
                    <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
                </div>
            </div>
        </div>

        {{-- Task List --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-list"></i> Danh sách Task</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-striped">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Phiếu việc</th>
                            <th>Nội dung</th>
                            <th class="text-center">Trạng thái</th>
                            <th class="text-right">Tiền thu</th>
                            <th class="text-center">Cập nhật</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tasks as $index => $task)
                            <tr>
                                <td>{{ $tasks->firstItem() + $index }}</td>
                                <td>
                                    @if($task->workOrder)
                                        <a href="{{ route('admin.work-orders.show', $task->workOrder->id) }}" 
                                           class="font-weight-bold text-primary">
                                            {{ $task->workOrder->code }}
                                        </a>
                                        <br>
                                        <small class="text-muted">{{ Str::limit($task->workOrder->title, 30) }}</small>
                                    @else
                                        <span class="text-muted">---</span>
                                    @endif
                                </td>
                                <td>{{ Str::limit($task->title, 50) }}</td>
                                <td class="text-center">
                                    <span class="badge badge-{{ $task->status->color() }}">
                                        {{ $task->status->label() }}
                                    </span>
                                </td>
                                <td class="text-right">
                                    @if($task->collected_amount > 0)
                                        <span class="text-success font-weight-bold">
                                            {{ number_format($task->collected_amount) }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <small>{{ $task->updated_at->format('d/m/Y H:i') }}</small>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    Không có task nào trong khoảng thời gian này.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($tasks->hasPages())
                <div class="card-footer">
                    {{ $tasks->links() }}
                </div>
            @endif
        </div>

        {{-- Summary --}}
        @if($stats['total'] > 0)
        <div class="callout callout-info">
            <h5><i class="fas fa-chart-pie"></i> Tóm tắt</h5>
            <p class="mb-0">
                <strong>Tỷ lệ hoàn thành:</strong> 
                {{ $stats['total'] > 0 ? round(($stats['completed'] / $stats['total']) * 100) : 0 }}%
                ({{ $stats['completed'] }}/{{ $stats['total'] }} task)
                <span class="mx-3">|</span>
                <strong>Tổng thu hộ:</strong> 
                <span class="text-success">{{ number_format($stats['collected']) }} VNĐ</span>
            </p>
        </div>
        @endif

    </div>
</section>
@endsection
