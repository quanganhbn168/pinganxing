<div>
    <section class="content-header">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Quản lý công việc</h1>
            </div>
            
        </div>
        
        {{-- Status Counters (Mobile Friendly - Horizontal Scroll) --}}
        <div class="row">
            <div class="col-12">
                <div class="d-flex flex-nowrap overflow-auto pb-2" style="gap: 10px;">
                    <a href="?filter=all" class="btn btn-app {{ $filter == 'all' ? 'bg-info' : 'bg-light' }} m-0">
                        <span class="badge bg-purple">{{ $statusCounts['all'] }}</span>
                        <i class="fas fa-list"></i> Các loại
                    </a>
                    <a href="?filter=active" class="btn btn-app {{ $filter == 'active' ? 'bg-primary' : 'bg-light' }} m-0">
                        <span class="badge bg-warning">{{ $statusCounts['active'] }}</span>
                        <i class="fas fa-bolt"></i> Cần làm
                    </a>
                    <a href="#" class="btn btn-app bg-light m-0 disabled">
                        <span class="badge bg-warning">{{ $statusCounts['pending'] }}</span>
                        <i class="fas fa-clock"></i> Chờ xử lý
                    </a>
                    <a href="#" class="btn btn-app bg-light m-0 disabled">
                        <span class="badge bg-primary">{{ $statusCounts['processing'] }}</span>
                        <i class="fas fa-spinner"></i> Đang làm
                    </a>
                    <a href="#" class="btn btn-app bg-light m-0 disabled">
                        <span class="badge bg-success">{{ $statusCounts['completed'] }}</span>
                        <i class="fas fa-check"></i> Xong
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                @forelse($orders as $order)
                    <div class="col-md-6 col-lg-4">
                            <div class="card card-outline {{ $order->status === \App\Enums\WorkOrderStatus::COMPLETED ? 'card-success' : 'card-primary' }}">
                                <div class="card-header">
                                    <h3 class="card-title font-weight-bold">
                                        <i class="fas fa-hashtag"></i> {{ $order->code }}
                                    </h3>
                                    <div class="card-tools">
                                        {{-- Priority Badge --}}
                                        {{-- Priority Badge --}}
                                        @if($order->priority === 'urgent')
                                            <span class="badge badge-danger mr-1"><i class="fas fa-exclamation-circle"></i> Khẩn cấp</span>
                                        @elseif($order->priority === 'high')
                                            <span class="badge badge-warning mr-1" style="color: #fff; background-color: #fd7e14;"><i class="fas fa-arrow-up"></i> Cao</span>
                                        @elseif($order->priority === 'medium')
                                            <span class="badge badge-info mr-1">TB</span>
                                        @else
                                            <span class="badge badge-secondary mr-1">Thấp</span>
                                        @endif

                                        {{-- Badge trạng thái --}}
                                        <span class="badge badge-{{ $order->status->color() }}">
                                            {{ $order->status->label() }}
                                        </span>
                                    </div>
                                </div>
                            <div class="card-body">
                                <h5 class="text-primary">{{ $order->title }}</h5>
                                <p class="text-muted small mb-2"><i class="far fa-clock"></i> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                                
                                <ul class="list-unstyled">
                                    <li><strong>Người phụ trách:</strong> {{ $order->contact_person }}</li>
                                    {{-- Lấy SĐT chính --}}
                                    <li><strong>SĐT:</strong> <a href="tel:{{ $order->contact_phone ?? '' }}">{{ $order->contact_phone ?? '---' }}</a></li>
                                    <li><strong>Địa chỉ:</strong> <a href="tel:{{ $order->site_address ?? '' }}">{{ $order->site_address ?? '---' }}</a></li>
                                </ul>
                                
                                <a href="{{ route('admin.work-orders.show', $order->id) }}" class="btn btn-block btn-info">
                                    <i class="fas fa-eye"></i> Xem chi tiết & Báo cáo
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info">
                            Bạn chưa được giao công việc nào.
                        </div>
                    </div>
                @endforelse
            </div>
            
            {{-- Phân trang --}}
            <div class="d-flex justify-content-center">
                {{ $orders->links() }}
            </div>
        </div>
    </section>
</div>