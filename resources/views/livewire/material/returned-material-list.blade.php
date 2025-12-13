<div>
    <div class="card card-outline card-info">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-undo-alt"></i> Vật tư thu hồi</h3>
        </div>
        <div class="card-body">
            
            {{-- Stats Cards - Theo Status --}}
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
                        <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Chờ xử lý</span>
                            <span class="info-box-number">{{ $stats['pending'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="info-box bg-primary mb-3">
                        <span class="info-box-icon"><i class="fas fa-truck"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Đã gửi NCC</span>
                            <span class="info-box-number">{{ $stats['sent_to_supplier'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="info-box bg-success mb-3">
                        <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Đã mang về</span>
                            <span class="info-box-number">{{ $stats['returned'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="info-box bg-secondary mb-3">
                        <span class="info-box-icon"><i class="fas fa-lock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Đã đóng</span>
                            <span class="info-box-number">{{ $stats['closed'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filters --}}
            <div class="row mb-3 align-items-end">
                <div class="col-md-2">
                    <label class="small mb-1">Tìm kiếm</label>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control form-control-sm" placeholder="Tên, Serial...">
                </div>
                <div class="col-md-2">
                    <label class="small mb-1">Trạng thái</label>
                    <select wire:model.live="filterStatus" class="form-control form-control-sm">
                        <option value="">Tất cả</option>
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
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
                            <th style="width: 40px;">#</th>
                            <th>Thiết bị</th>
                            <th>Serial</th>
                            <th>Lý do</th>
                            <th style="width: 140px;">Trạng thái</th>
                            <th>Nhà cung cấp</th>
                            <th>Phiếu việc</th>
                            <th style="width: 100px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $index => $item)
                            <tr>
                                <td>{{ $items->firstItem() + $index }}</td>
                                <td>
                                    <span class="font-weight-bold">{{ $item->item_name }}</span>
                                    @if($item->condition_note)
                                        <br><small class="text-muted">{{ Str::limit($item->condition_note, 30) }}</small>
                                    @endif
                                </td>
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
                                    {{-- Status Dropdown --}}
                                    <select wire:change="updateStatus({{ $item->id }}, $event.target.value)" 
                                            class="form-control form-control-sm border-{{ $item->status_color }}">
                                        @foreach($statuses as $key => $label)
                                            <option value="{{ $key }}" {{ $item->status === $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if($item->returned_at && $item->returnedByAdmin)
                                        <small class="text-muted d-block mt-1">
                                            <i class="fas fa-user"></i> {{ $item->returnedByAdmin->name }}
                                            <br>{{ $item->returned_at->format('d/m H:i') }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    @if($item->supplier)
                                        <span class="text-primary">{{ $item->supplier->name }}</span>
                                    @else
                                        <span class="text-muted">Chưa gán</span>
                                    @endif
                                    @if($item->notes)
                                        <br><small class="text-muted"><i class="fas fa-sticky-note"></i> {{ Str::limit($item->notes, 20) }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($item->report?->task?->workOrder)
                                        <a href="{{ route('admin.work-orders.show', $item->report->task->workOrder->id) }}" 
                                           class="text-primary font-weight-bold">
                                            {{ $item->report->task->workOrder->code }}
                                        </a>
                                        <br>
                                        <small class="text-muted">{{ $item->created_at->format('d/m/Y') }}</small>
                                    @else
                                        <span class="text-muted">---</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button wire:click="openEditModal({{ $item->id }})" class="btn btn-xs btn-outline-info" title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    Không có vật tư thu hồi trong khoảng thời gian này.
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

    {{-- Edit Modal --}}
    <div class="modal fade" id="editReturnedItemModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title text-white"><i class="fas fa-edit"></i> Chỉnh sửa thông tin</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nhà cung cấp</label>
                        <select wire:model="editSupplierId" class="form-control">
                            <option value="">-- Chọn nhà cung cấp --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Nơi đang giữ thiết bị để bảo hành/sửa chữa</small>
                    </div>
                    <div class="form-group">
                        <label>Ghi chú</label>
                        <textarea wire:model="editNotes" class="form-control" rows="3" placeholder="Ghi chú thêm..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button wire:click="saveDetails" class="btn btn-primary">
                        <i class="fas fa-save"></i> Lưu
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('open-edit-modal', () => {
                $('#editReturnedItemModal').modal('show');
            });
            Livewire.on('close-edit-modal', () => {
                $('#editReturnedItemModal').modal('hide');
            });
        });
    </script>
    @endpush
</div>

