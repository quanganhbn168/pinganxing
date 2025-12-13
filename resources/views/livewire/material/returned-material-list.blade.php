<div>
    <div class="card card-outline card-info">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-undo-alt"></i> Vật tư thu về</h3>
        </div>
        <div class="card-body">
            
            {{-- Stats Cards --}}
            <div class="row mb-4">
                <div class="col-md-2 col-6">
                    <div class="info-box bg-info mb-3">
                        <span class="info-box-icon"><i class="fas fa-boxes"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Tổng cộng</span>
                            <span class="info-box-number">{{ $stats['total'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="info-box bg-warning mb-3">
                        <span class="info-box-icon"><i class="fas fa-shield-alt"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Bảo hành</span>
                            <span class="info-box-number">{{ $stats['warranty'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="info-box bg-primary mb-3">
                        <span class="info-box-icon"><i class="fas fa-exchange-alt"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Đổi model</span>
                            <span class="info-box-number">{{ $stats['replace'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="info-box bg-danger mb-3">
                        <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Lỗi nhà SX</span>
                            <span class="info-box-number">{{ $stats['defective'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="info-box bg-success mb-3">
                        <span class="info-box-icon"><i class="fas fa-arrow-up"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Nâng cấp</span>
                            <span class="info-box-number">{{ $stats['upgrade'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filters --}}
            <div class="row mb-3 align-items-end">
                <div class="col-md-3">
                    <label class="small mb-1">Tìm kiếm</label>
                    <input type="text" wire:model.live="search" class="form-control form-control-sm" placeholder="Tên thiết bị, Serial...">
                </div>
                <div class="col-md-2">
                    <label class="small mb-1">Lý do</label>
                    <select wire:model.live="filterReason" class="form-control form-control-sm">
                        <option value="">Tất cả</option>
                        @foreach($reasons as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="small mb-1">Từ ngày</label>
                    <input type="date" wire:model.live="filterFrom" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="small mb-1">Đến ngày</label>
                    <input type="date" wire:model.live="filterTo" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <button wire:click="resetFilters" class="btn btn-sm btn-default">
                        <i class="fas fa-sync"></i> Reset
                    </button>
                </div>
            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Tên thiết bị</th>
                            <th>Serial</th>
                            <th>Lý do</th>
                            <th>Tình trạng</th>
                            <th>Phiếu việc</th>
                            <th style="width: 120px;">Ngày thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $index => $item)
                            <tr>
                                <td>{{ $items->firstItem() + $index }}</td>
                                <td class="font-weight-bold">{{ $item->item_name }}</td>
                                <td>
                                    @if($item->serial_number)
                                        <code>{{ $item->serial_number }}</code>
                                    @else
                                        <span class="text-muted">---</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $reasonColors = [
                                            'warranty' => 'warning',
                                            'replace' => 'primary',
                                            'defective' => 'danger',
                                            'upgrade' => 'success',
                                        ];
                                    @endphp
                                    <span class="badge badge-{{ $reasonColors[$item->reason] ?? 'secondary' }}">
                                        {{ $item->reason_label }}
                                    </span>
                                </td>
                                <td>
                                    <small>{{ $item->condition_note ?? '---' }}</small>
                                </td>
                                <td>
                                    @if($item->report?->task?->workOrder)
                                        <a href="{{ route('admin.work-orders.show', $item->report->task->workOrder->id) }}" 
                                           class="text-primary font-weight-bold">
                                            {{ $item->report->task->workOrder->code }}
                                        </a>
                                        <br>
                                        <small class="text-muted">{{ Str::limit($item->report->task->title, 25) }}</small>
                                    @else
                                        <span class="text-muted">---</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $item->created_at->format('d/m/Y H:i') }}</small>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    Không có vật tư thu về trong khoảng thời gian này.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($items->hasPages())
                <div class="float-right mt-3">
                    {{ $items->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
