<div>
    <section class="content-header">
        <div class="container-fluid">
            <h1>Danh sách công việc của tôi</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                @forelse($orders as $order)
                    <div class="col-md-6 col-lg-4">
                        <div class="card card-outline {{ $order->status == 'completed' ? 'card-success' : 'card-primary' }}">
                            <div class="card-header">
                                <h3 class="card-title font-weight-bold">
                                    <i class="fas fa-hashtag"></i> {{ $order->code }}
                                </h3>
                                <div class="card-tools">
                                    {{-- Badge trạng thái --}}
                                    @if($order->status == 'pending')
                                        <span class="badge badge-warning">Chờ xử lý</span>
                                    @elseif($order->status == 'processing')
                                        <span class="badge badge-primary">Đang làm</span>
                                    @elseif($order->status == 'completed')
                                        <span class="badge badge-success">Hoàn thành</span>
                                    @else
                                        <span class="badge badge-secondary">Đã hủy</span>
                                    @endif
                                </div>
                            </div>
                            <div class="card-body">
                                <h5 class="text-primary">{{ $order->title }}</h5>
                                <p class="text-muted small mb-2"><i class="far fa-clock"></i> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                                
                                <ul class="list-unstyled">
                                    <li><strong>Khách:</strong> {{ $order->customer->name }}</li>
                                    {{-- Lấy SĐT chính --}}
                                    @php $phone = $order->customer->contacts->where('type', 'phone')->first(); @endphp
                                    <li><strong>SĐT:</strong> <a href="tel:{{ $phone->value ?? '' }}">{{ $phone->value ?? '---' }}</a></li>
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