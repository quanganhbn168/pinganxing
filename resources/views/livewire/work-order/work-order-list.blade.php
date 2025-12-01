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
                    <div class="d-flex justify-content-between align-items-center">
                        {{-- Bộ lọc trạng thái --}}
                        <div class="d-flex" style="gap: 10px;">
                            <select wire:model.live="status" class="form-control form-control-sm" style="width: 150px;">
                                <option value="all">-- Tất cả --</option>
                                <option value="pending">Chờ xử lý</option>
                                <option value="processing">Đang làm</option>
                                <option value="completed">Đã xong (Đóng)</option>
                                <option value="cancelled">Đã hủy</option>
                            </select>
                        </div>

                        {{-- Tìm kiếm --}}
                        <div class="card-tools">
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <input type="text" wire:model.live.debounce.300ms="search" class="form-control float-right" placeholder="Tìm tên khách, mã job...">
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
                                <th>Khách hàng</th>
                                <th>Yêu cầu (Title)</th>
                                <th>Nhân viên phụ trách</th>
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
                                        <small class="text-muted">{{ $order->created_at->format('d/m/Y') }}</small>
                                    </td>
                                    <td>
                                        {{ $order->customer->name }}
                                        <br>
                                        <small class="text-muted">
                                            {{ $order->customer->contacts->where('type','phone')->first()->value ?? '' }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="d-inline-block text-truncate" style="max-width: 200px;" title="{{ $order->title }}">
                                            {{ $order->title }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            @foreach($order->assignees as $staff)
                                                <small><i class="fas fa-user-tag text-xs"></i> {{ $staff->name }}</small>
                                            @endforeach
                                            @if($order->assignees->isEmpty()) <small class="text-danger">Chưa gán</small> @endif
                                        </div>
                                    </td>
                                    <td>
                                        {{-- Đếm số task đã báo cáo --}}
                                        <span class="badge badge-light border">
                                            {{ $order->tasks->count() }} báo cáo
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($order->status == 'pending')
                                            <span class="badge badge-warning">Chờ xử lý</span>
                                        @elseif($order->status == 'processing')
                                            <span class="badge badge-primary">Đang thực hiện</span>
                                        @elseif($order->status == 'completed')
                                            <span class="badge badge-success">Hoàn thành</span>
                                        @else
                                            <span class="badge badge-secondary">Đã hủy</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <div class="btn-group">
                                            {{-- Xem chi tiết --}}
                                            <a href="{{ route('admin.work-orders.show', $order->id) }}" class="btn btn-sm btn-info" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            {{-- Nút Đóng Job / Mở lại --}}
                                            @if($order->status != 'completed' && $order->status != 'cancelled')
                                                <button wire:confirm="Bạn có chắc chắn muốn ĐÓNG Job này? (Xác nhận đã hoàn thành)" 
                                                        wire:click="markAsCompleted({{ $order->id }})" 
                                                        class="btn btn-sm btn-success" title="Hoàn thành & Đóng">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                
                                                <button wire:confirm="Hủy Job này?" 
                                                        wire:click="markAsCancelled({{ $order->id }})" 
                                                        class="btn btn-sm btn-danger" title="Hủy bỏ">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            @else
                                                {{-- Nếu đã đóng/hủy thì cho phép mở lại --}}
                                                <button wire:confirm="Mở lại Job này để làm tiếp?" 
                                                        wire:click="markAsProcessing({{ $order->id }})" 
                                                        class="btn btn-sm btn-default" title="Mở lại">
                                                    <i class="fas fa-redo"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">
                                        <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                        Không tìm thấy phiếu việc nào.
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