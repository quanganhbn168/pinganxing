<div>
    {{-- Header --}}
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary mr-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="d-flex align-items-center">
                    <div class="avatar-lg mr-3">
                        @if($staff->avatar)
                            <img src="{{ $staff->avatar }}" class="img-circle elevation-2" style="width: 60px; height: 60px; object-fit: cover;">
                        @else
                            <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 24px;">
                                {{ strtoupper(substr($staff->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <h4 class="mb-0">{{ $staff->name }}</h4>
                        <span class="badge badge-primary">{{ $staff->roles->first()?->name ?? 'Nhân viên' }}</span>
                        <span class="text-muted">{{ $staff->email }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 text-right">
            {{-- Month/Year Filter --}}
            <div class="d-inline-flex align-items-center">
                <select wire:model.live="filterMonth" class="form-control form-control-sm mr-2" style="width: 100px;">
                    @foreach($months as $m)
                        <option value="{{ $m }}">Tháng {{ $m }}</option>
                    @endforeach
                </select>
                <select wire:model.live="filterYear" class="form-control form-control-sm" style="width: 90px;">
                    @foreach($years as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['totalAssigned'] }}</h3>
                    <p>Task được gán</p>
                </div>
                <div class="icon"><i class="fas fa-tasks"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['totalCompleted'] }}</h3>
                    <p>Task hoàn thành</p>
                </div>
                <div class="icon"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['completionRate'] }}%</h3>
                    <p>Tỷ lệ hoàn thành</p>
                </div>
                <div class="icon"><i class="fas fa-percentage"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ number_format($stats['totalCollected']) }}đ</h3>
                    <p>Tiền thu hộ</p>
                </div>
                <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Tag Analysis --}}
        <div class="col-md-4">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-tags"></i> Phân tích theo Tags</h3>
                </div>
                <div class="card-body p-0">
                    @if($stats['tagStats']->count() > 0)
                        <table class="table table-sm mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Tag</th>
                                    <th class="text-right">Số task</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats['tagStats'] as $tag)
                                    <tr>
                                        <td>
                                            <span class="badge" style="background-color: {{ $tag->color ?? '#6c757d' }}; color: #fff;">
                                                {{ $tag->name }}
                                            </span>
                                        </td>
                                        <td class="text-right font-weight-bold">{{ $tag->task_count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-tag fa-2x mb-2 d-block"></i>
                            Chưa có dữ liệu tags
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Recent Tasks --}}
        <div class="col-md-8">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-history"></i> Công việc gần đây</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Phiếu việc</th>
                                    <th>Task</th>
                                    <th>Tags</th>
                                    <th>Trạng thái</th>
                                    <th class="text-right">Thu hộ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTasks as $task)
                                    @php
                                        $latestReport = $task->reports->first();
                                        $isCompleted = $latestReport?->is_completed ?? false;
                                    @endphp
                                    <tr>
                                        <td>
                                            @if($task->workOrder)
                                                <a href="{{ route('admin.work-orders.show', $task->workOrder->id) }}" class="text-primary font-weight-bold">
                                                    {{ $task->workOrder->code }}
                                                </a>
                                            @else
                                                <span class="text-muted">---</span>
                                            @endif
                                        </td>
                                        <td>{{ Str::limit($task->title, 30) }}</td>
                                        <td>
                                            @foreach($task->workOrder?->tags ?? [] as $tag)
                                                <span class="badge badge-sm" style="background-color: {{ $tag->color }}; color: #fff; font-size: 10px;">
                                                    {{ $tag->name }}
                                                </span>
                                            @endforeach
                                        </td>
                                        <td>
                                            @if($isCompleted)
                                                <span class="badge badge-success"><i class="fas fa-check"></i> Hoàn thành</span>
                                            @else
                                                <span class="badge badge-warning">Đang làm</span>
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            @if($latestReport?->collected_amount > 0)
                                                <span class="text-success font-weight-bold">
                                                    {{ number_format($latestReport->collected_amount) }}đ
                                                </span>
                                            @else
                                                <span class="text-muted">---</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                            Không có task nào trong tháng này
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
