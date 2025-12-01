<div>
    <section class="content-header">
        <div class="container-fluid">
            <h1><i class="fas fa-file-invoice-dollar"></i> Duyệt Nhật Ký & Doanh Thu</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            
            {{-- Bộ lọc --}}
            <div class="card mb-3">
                <div class="card-body p-2 d-flex align-items-center">
                    <span class="mr-2">Lọc trạng thái:</span>
                    <select wire:model.live="filter_status" class="form-control form-control-sm w-auto">
                        <option value="all">Tất cả báo cáo</option>
                        <option value="unpaid">Có thu tiền - Chưa nộp về quỹ (Unpaid)</option>
                    </select>
                </div>
            </div>

            @if (session()->has('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            {{-- Bảng dữ liệu --}}
            <div class="card">
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr class="bg-light">
                                <th>Thời gian</th>
                                <th>Phiếu việc (Job)</th>
                                <th>Thợ thực hiện</th>
                                <th>Nội dung báo cáo</th>
                                <th>Vật tư (SL)</th>
                                <th class="text-right">Tiền mặt (COD)</th>
                                <th class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tasks as $task)
                                <tr>
                                    <td>{{ $task->created_at->format('d/m H:i') }}</td>
                                    <td>
                                        <a href="{{ route('admin.work-orders.show', $task->work_order_id) }}" target="_blank">
                                            <strong>{{ $task->workOrder->code }}</strong>
                                        </a>
                                        <br>
                                        <small class="text-muted">{{ Str::limit($task->workOrder->customer->name, 15) }}</small>
                                    </td>
                                    <td>{{ $task->performer->name ?? '---' }}</td>
                                    <td>
                                        <span class="d-inline-block text-truncate" style="max-width: 200px;" title="{{ $task->report_content }}">
                                            {{ $task->report_content }}
                                        </span>
                                    </td>
                                    <td>
                                        @foreach($task->items as $item)
                                            <span class="badge badge-info">{{ $item->item_name }} ({{ $item->quantity }})</span><br>
                                        @endforeach
                                        @if($task->items->isEmpty()) <span class="text-muted small">-Không-</span> @endif
                                    </td>
                                    <td class="text-right">
                                        @if($task->collected_amount > 0)
                                            <span class="font-weight-bold {{ $task->is_paid ? 'text-success' : 'text-danger' }}">
                                                {{ number_format($task->collected_amount) }}
                                            </span>
                                            <br>
                                            @if($task->is_paid)
                                                <small class="text-success"><i class="fas fa-check-circle"></i> Đã nộp</small>
                                            @else
                                                <small class="text-danger"><i class="fas fa-exclamation-circle"></i> Đang giữ</small>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            {{-- Nút Sửa Vật Tư --}}
                                            <button wire:click="openEditModal({{ $task->id }})" class="btn btn-sm btn-default" title="Sửa vật tư / Giá">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            {{-- Nút Xác nhận tiền (Chỉ hiện nếu có tiền và chưa nộp) --}}
                                            @if($task->collected_amount > 0 && !$task->is_paid)
                                                <button wire:confirm="Xác nhận đã nhận {{ number_format($task->collected_amount) }}đ từ thợ?" 
                                                        wire:click="confirmPayment({{ $task->id }})" 
                                                        class="btn btn-sm btn-warning" 
                                                        title="Xác nhận đã thu tiền">
                                                    <i class="fas fa-hand-holding-usd"></i> Thu
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Không có dữ liệu báo cáo nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    {{ $tasks->links() }}
                </div>
            </div>
        </div>
    </section>

    {{-- === MODAL CHỈNH SỬA === --}}
    <div class="modal fade" id="auditModal" tabindex="-1" role="dialog" wire:ignore.self>
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title">Chuẩn hóa thông tin & Vật tư</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    @if($editingTask)
                        <div class="form-group">
                            <label>Số tiền thợ báo thu (VNĐ)</label>
                            
                            {{-- SỬA CHỖ NÀY: Dùng wire:model="editing_amount" và type="number" --}}
                            <input type="number" wire:model="editing_amount" class="form-control font-weight-bold text-success" placeholder="Nhập số tiền...">
                            
                            <small class="text-muted">Chỉ sửa nếu thợ nhập sai tiền thu của khách.</small>
                        </div>

                        <hr>
                        <h6>Danh sách vật tư sử dụng:</h6>
                        <table class="table table-bordered table-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th>Tên thiết bị (Chuẩn hóa)</th>
                                    <th>Serial</th>
                                    <th style="width: 70px">SL</th>
                                    <th style="width: 150px">Đơn giá (Lưu kho)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($editingItems as $index => $item)
                                    <tr>
                                        <td>
                                            <input type="text" wire:model="editingItems.{{ $index }}.item_name" class="form-control form-control-sm">
                                        </td>
                                        <td>
                                            <input type="text" wire:model="editingItems.{{ $index }}.serial_number" class="form-control form-control-sm">
                                        </td>
                                        <td>
                                            <input type="number" wire:model="editingItems.{{ $index }}.quantity" class="form-control form-control-sm text-center">
                                        </td>
                                        <td>
                                            <input type="text" wire:model="editingItems.{{ $index }}.price" class="form-control form-control-sm text-right" placeholder="0">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="alert alert-info text-sm">
                            <i class="fas fa-info-circle"></i> Điền <b>Đơn giá</b> để hệ thống tính toán doanh thu/lợi nhuận sau này. Thợ sẽ không thấy giá này.
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="button" wire:click="saveChanges" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Script bật tắt Modal --}}
@push('js')
<script>
    document.addEventListener('livewire:initialized', () => {
        @this.on('open-audit-modal', () => {
            $('#auditModal').modal('show');
        });
        @this.on('close-audit-modal', () => {
            $('#auditModal').modal('hide');
        });
    });
</script>
@endpush