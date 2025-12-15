<div>
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between">
                <h1><i class="fas fa-list-alt"></i> Quản lý Phiếu Việc</h1>
                @if($this->canCreate)
                    <div class="btn-group">
                        <button wire:click="syncAll" 
                                wire:confirm="Bạn có chắc chắn muốn đồng bộ hóa lại TOÀN BỘ dữ liệu (Tiêu đề & Nội dung báo cáo) của tất cả Work Orders? Hành động này không thể hoàn tác."
                                wire:loading.attr="disabled"
                                class="btn btn-warning mr-2">
                            <i class="fas fa-sync-alt" wire:loading.remove></i>
                            <i class="fas fa-spinner fa-spin" wire:loading></i>
                            Đồng bộ
                        </button>
                        <a href="{{ route('admin.work-orders.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Tạo mới
                        </a>
                    </div>
                @endif
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
                                @foreach($this->statusOptions as $opt)
                                    <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
                                @endforeach
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
                                <th>Hạn</th>
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
                                    
                                    <td>{!! $order->priority_badge !!}</td>

                                    <td>
                                        {{ $order->customer->name ?? 'Khách lẻ' }} <br>
                                        <small class="text-muted">
                                            <i class="fas fa-phone-alt text-xs"></i> 
                                            {{ $order->customer_phone }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="d-inline-block text-truncate" style="max-width: 200px;" title="{{ $order->title }}">
                                            {{ $order->title }}
                                        </span>
                                        @if($order->tags->count())
                                            <br>
                                            @foreach($order->tags as $tag)
                                                {!! $tag->badge_html !!}
                                            @endforeach
                                        @endif
                                    </td>
                                    
                                    {{-- DEADLINE --}}
                                    <td>
                                        @if($order->deadline)
                                            <div class="{{ $order->isOverdue() ? 'text-danger font-weight-bold' : 'text-muted' }}">
                                                <i class="far fa-calendar-alt text-xs mr-1"></i>
                                                {{ $order->deadline->format('d/m/Y') }}
                                            </div>
                                            <small class="{{ $order->isOverdue() ? 'text-danger' : 'text-muted' }}">
                                                {{ $order->deadline->format('H:i') }}
                                                @if($order->isOverdue())
                                                    <span class="badge badge-danger badge-sm ml-1">Quá hạn</span>
                                                @endif
                                            </small>
                                        @else
                                            <span class="text-muted">---</span>
                                        @endif
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
                                        <div class="progress progress-xs">
                                            <div class="progress-bar {{ $order->progress_color }}" style="width: {{ $order->progress_percent }}%"></div>
                                        </div>
                                        <small class="text-muted">{{ $order->progress_text }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-{{ $order->status->color() }}">
                                            {{ $order->status->label() }}
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        <div class="btn-group">
                                            
                                            {{-- ĐÃ HOÀN THÀNH --}}
                                            @if($order->status === \App\Enums\WorkOrderStatus::COMPLETED)
                                                <a href="{{ route('admin.work-orders.show', $order->id) }}" class="btn btn-sm btn-default border" title="Xem chi tiết">
                                                    <i class="fas fa-eye text-info"></i>
                                                </a>

                                                @if($order->warrantyService)
                                                    <a href="{{ route('admin.warranty.index', ['search' => $order->code]) }}" class="btn btn-sm border" title="Đã có BH">
                                                        <i class="fas fa-shield-alt text-success"></i>
                                                    </a>
                                                @else
                                                    <a href="{{ route('admin.warranty.create', ['work_order_id' => $order->id]) }}" class="btn btn-sm border" title="Tạo Bảo hành">
                                                        <i class="fas fa-shield-alt text-success"></i>
                                                    </a>
                                                @endif

                                                <a href="{{ route('admin.finance.work-order', $order->id) }}" class="btn btn-sm btn-default border" title="Duyệt tài chính">
                                                    <i class="fas fa-file-invoice-dollar text-warning"></i>
                                                </a>

                                                @can('approve_work_orders')
                                                <button wire:confirm="CẢNH BÁO: Mở lại phiếu này sẽ cho phép chỉnh sửa task con. Tiếp tục?" 
                                                        wire:click="markAsProcessing({{ $order->id }})" 
                                                        class="btn btn-sm btn-outline-danger border ml-1" title="Mở lại phiếu">
                                                    <i class="fas fa-undo"></i>
                                                </button>
                                                @endcan

                                            {{-- CHƯA HOÀN THÀNH --}}
                                            @elseif($order->status !== \App\Enums\WorkOrderStatus::CANCELLED)
                                                
                                                @can('update_work_orders')
                                                <a href="{{ route('admin.work-orders.edit', $order->id) }}" class="btn btn-sm btn-warning border" title="Sửa thông tin">
                                                    <i class="fas fa-edit text-white"></i>
                                                </a>
                                                @endcan

                                                <a href="{{ route('admin.work-orders.show', $order->id) }}" class="btn btn-sm btn-default border" title="Xem tiến độ">
                                                    <i class="fas fa-eye text-info"></i>
                                                </a>

                                                @can('approve_work_orders')
                                                    @if($order->progress_percent === 100 && $order->tasks->count() > 0)
                                                        <button wire:confirm="Xác nhận: Tất cả công việc đã xong. DUYỆT & ĐÓNG phiếu này?" 
                                                                wire:click="markAsCompleted({{ $order->id }})" 
                                                                class="btn btn-sm btn-success" title="Duyệt hoàn thành">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn btn-sm btn-light border" disabled title="Chưa xong hết việc">
                                                            <i class="fas fa-clock text-muted"></i>
                                                        </button>
                                                    @endif
                                                    
                                                    <button wire:confirm="Hủy bỏ Job này?" wire:click="markAsCancelled({{ $order->id }})" class="btn btn-sm btn-default text-danger border ml-1">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endcan

                                            @else 
                                                <span class="badge badge-secondary">Đã hủy</span>
                                                @can('approve_work_orders')
                                                <button wire:click="markAsProcessing({{ $order->id }})" class="btn btn-xs btn-link">Khôi phục</button>
                                                @endcan
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-5">
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