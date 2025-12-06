<div>
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between">
                <h1><i class="fas fa-list-alt"></i> Quản lý Phiếu Việc</h1>
                <a href="{{ route('admin.work-orders.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tạo mới
                </a>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            
            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    {{ session('success') }}
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        
                        {{-- BỘ LỌC --}}
                        <div class="d-flex" style="gap: 10px;">
                            <select wire:model.live="status" class="form-control form-control-sm" style="width: 150px;">
                                <option value="all">-- Trạng thái --</option>
                                <option value="pending">Chờ xử lý</option>
                                <option value="processing">Đang làm</option>
                                <option value="completed">Đã xong</option>
                                <option value="cancelled">Đã hủy</option>
                            </select>
                        </div>

                        {{-- TÌM KIẾM --}}
                        <div class="card-tools mt-2 mt-md-0">
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <input type="text" wire:model.live.debounce.300ms="search" class="form-control float-right" placeholder="Tìm tên, mã, sđt...">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr class="bg-light">
                                <th>Mã Job</th>
                                <th>Độ ưu tiên</th>
                                <th>Khách hàng</th>
                                <th>Yêu cầu</th>
                                <th>Nhân viên</th>
                                <th>Tiến độ</th>
                                <th class="text-center">Trạng thái</th>
                                <th class="text-right">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.work-orders.show', $order->id) }}">
                                            <strong>{{ $order->code }}</strong>
                                        </a>
                                        <br>
                                        <small class="text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                    
                                    <td>
                                        @php
                                            $priorityEnum = $order->priority instanceof \App\Enums\WorkOrderPriority 
                                                ? $order->priority 
                                                : \App\Enums\WorkOrderPriority::tryFrom($order->priority);
                                        @endphp
                                        @if($priorityEnum)
                                            <span class="badge badge-{{ $priorityEnum->color() }}">
                                                {{ $priorityEnum->label() }}
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">{{ $order->priority }}</span>
                                        @endif
                                    </td>

                                    <td>
                                        {{ $order->customer->name }} <br>
                                        <small class="text-muted">
                                            <i class="fas fa-phone-alt text-xs"></i> 
                                            {{ $order->customer->contacts->where('type','phone')->first()->value ?? '---' }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="d-inline-block text-truncate" style="max-width: 200px;" title="{{ $order->title }}">
                                            {{ $order->title }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            @forelse($order->assignees as $staff)
                                                <small><i class="fas fa-user text-xs text-muted"></i> {{ $staff->name }}</small>
                                            @empty
                                                <small class="text-danger font-italic">Chưa gán</small>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $totalTasks = $order->tasks->count();
                                            $completedTasks = $order->tasks->where('status', \App\Enums\TaskStatus::COMPLETED)->count();
                                            $percent = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                                            $color = $percent == 100 ? 'bg-success' : ($percent > 50 ? 'bg-primary' : 'bg-warning');
                                        @endphp
                                        <div class="progress progress-xs">
                                            <div class="progress-bar {{ $color }}" style="width: {{ $percent }}%"></div>
                                        </div>
                                        <small class="text-muted">{{ $completedTasks }}/{{ $totalTasks }} việc ({{ $percent }}%)</small>
                                    </td>
                                    <td class="text-center">
                                        @if($order->status instanceof \App\Enums\WorkOrderStatus)
                                            <span class="badge badge-{{ $order->status->color() }}">
                                                {{ $order->status->label() }}
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">{{ $order->status }}</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <div class="btn-group">
                                            
                                            {{-- TRƯỜNG HỢP 1: ĐÃ DUYỆT HOÀN THÀNH --}}
                                            @if($order->status === \App\Enums\WorkOrderStatus::COMPLETED)
                                                
                                                {{-- Xem --}}
                                                <a href="{{ route('admin.work-orders.show', $order->id) }}" class="btn btn-sm btn-default border" title="Xem chi tiết">
                                                    <i class="fas fa-eye text-info"></i>
                                                </a>

                                                {{-- Bảo Hành --}}
                                                @if($order->warrantyService)
                                                    <a href="{{ route('admin.warranty.index', ['search' => $order->code]) }}" class="btn btn-sm border" title="Đã có BH">
                                                        <i class="fas fa-shield-alt text-success"></i>
                                                    </a>
                                                @else
                                                    <a href="{{ route('admin.warranty.create', ['work_order_id' => $order->id]) }}" class="btn btn-sm border" title="Tạo Bảo hành">
                                                        <i class="fas fa-shield-alt text-success"></i>
                                                    </a>
                                                @endif

                                                {{-- Tài Chính --}}
                                                <a href="{{ route('admin.finance.work-order', $order->id) }}" class="btn btn-sm btn-default border" title="Duyệt tài chính">
                                                    <i class="fas fa-file-invoice-dollar text-warning"></i>
                                                </a>

                                                {{-- Mở Lại (Admin only) --}}
                                                <button wire:confirm="CẢNH BÁO: Mở lại phiếu này sẽ cho phép chỉnh sửa task con. Tiếp tục?" 
                                                        wire:click="markAsProcessing({{ $order->id }})" 
                                                        class="btn btn-sm btn-outline-danger border ml-1" title="Mở lại phiếu">
                                                    <i class="fas fa-undo"></i>
                                                </button>

                                            {{-- TRƯỜNG HỢP 2: CHƯA HOÀN THÀNH --}}
                                            @elseif($order->status !== \App\Enums\WorkOrderStatus::CANCELLED)
                                                
                                                {{-- Sửa --}}
                                                <a href="{{ route('admin.work-orders.edit', $order->id) }}" class="btn btn-sm btn-warning border" title="Sửa thông tin">
                                                    <i class="fas fa-edit text-white"></i>
                                                </a>

                                                {{-- Xem --}}
                                                <a href="{{ route('admin.work-orders.show', $order->id) }}" class="btn btn-sm btn-default border" title="Xem tiến độ">
                                                    <i class="fas fa-eye text-info"></i>
                                                </a>

                                                {{-- Nút Duyệt --}}
                                                @if($incompleteTasks = $order->tasks->where('status', '!=', \App\Enums\TaskStatus::COMPLETED)->count() === 0 && $order->tasks->count() > 0)
                                                    <button wire:confirm="Xác nhận: Tất cả công việc đã xong. DUYỆT & ĐÓNG phiếu này?" 
                                                            wire:click="markAsCompleted({{ $order->id }})" 
                                                            class="btn btn-sm btn-success" title="Duyệt hoàn thành">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-light border" disabled 
                                                            title="Chưa xong hết việc">
                                                        <i class="fas fa-clock text-muted"></i>
                                                    </button>
                                                @endif
                                                
                                                {{-- Hủy --}}
                                                <button wire:confirm="Hủy bỏ Job này?" wire:click="markAsCancelled({{ $order->id }})" class="btn btn-sm btn-default text-danger border ml-1">
                                                    <i class="fas fa-trash"></i>
                                                </button>

                                            @else 
                                                <span class="badge badge-secondary">Đã hủy</span>
                                                <button wire:click="markAsProcessing({{ $order->id }})" class="btn btn-xs btn-link">Khôi phục</button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-5">
                                        <i class="fas fa-inbox fa-3x mb-3 text-gray-300"></i><br>
                                        Không tìm thấy dữ liệu.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </section>
</div>