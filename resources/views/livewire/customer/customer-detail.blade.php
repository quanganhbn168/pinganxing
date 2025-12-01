<div>
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between">
                <h1>Hồ sơ khách hàng</h1>
                <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Quay lại danh sách
                </a>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                {{-- CỘT TRÁI: PROFILE CARD --}}
                <div class="col-md-3">
                    {{-- Card Profile --}}
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <div class="profile-user-img img-fluid img-circle bg-gray d-flex justify-content-center align-items-center" 
                                     style="height: 100px; width: 100px; margin: 0 auto; font-size: 40px; color: white;">
                                    {{ substr($customer->name, 0, 1) }}
                                </div>
                            </div>

                            <h3 class="profile-username text-center mt-3">{{ $customer->name }}</h3>
                            <p class="text-muted text-center">Mã KH: #{{ $customer->id }}</p>

                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>Tổng Job</b> <a class="float-right">{{ $stats['total_jobs'] }}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Tổng chi tiêu</b> <a class="float-right text-success font-weight-bold">{{ number_format($stats['total_spent']) }} đ</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Gần nhất</b> <a class="float-right">{{ $stats['last_date'] }}</a>
                                </li>
                            </ul>

                            <a href="{{ route('admin.customers.edit', $customer->id) }}" class="btn btn-primary btn-block">
                                <i class="fas fa-pen"></i> Sửa thông tin
                            </a>
                        </div>
                    </div>

                    {{-- Card Liên hệ --}}
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Thông tin liên hệ</h3>
                        </div>
                        <div class="card-body">
                            @foreach($customer->contacts as $contact)
                                @if($contact->type == 'phone')
                                    <strong><i class="fas fa-phone-alt mr-1"></i> Điện thoại ({{ $contact->label }})</strong>
                                    <p class="text-muted"><a href="tel:{{ $contact->value }}">{{ $contact->value }}</a></p>
                                @else
                                    <strong><i class="fas fa-map-marker-alt mr-1"></i> Địa chỉ ({{ $contact->label }})</strong>
                                    <p class="text-muted">{{ $contact->value }}</p>
                                @endif
                                @if(!$loop->last) <hr> @endif
                            @endforeach
                        </div>
                    </div>
                    
                    {{-- Card Ghi chú --}}
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Ghi chú</h3>
                        </div>
                        <div class="card-body text-muted small">
                            {{ $customer->notes ?? 'Chưa có ghi chú nào.' }}
                        </div>
                    </div>
                </div>

                {{-- CỘT PHẢI: LỊCH SỬ GIAO DỊCH --}}
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item"><a class="nav-link active" href="#timeline" data-toggle="tab">Lịch sử hoạt động</a></li>
                                <li class="nav-item"><a class="nav-link" href="#equipment" data-toggle="tab">Thiết bị đang sử dụng</a></li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                
                                {{-- TAB 1: TIMELINE (Lịch sử làm việc) --}}
                                <div class="active tab-pane" id="timeline">
                                    @if($customer->workOrders->isEmpty())
                                        <div class="alert alert-info">Khách hàng này chưa có giao dịch nào.</div>
                                    @else
                                        <div class="timeline timeline-inverse">
                                            @foreach($customer->workOrders as $job)
                                                <div class="time-label">
                                                    <span class="bg-{{ $job->status == 'completed' ? 'success' : ($job->status == 'cancelled' ? 'secondary' : 'warning') }}">
                                                        {{ $job->created_at->format('d/m/Y') }} ({{ $job->code }})
                                                    </span>
                                                </div>
                                                
                                                {{-- Nội dung Job --}}
                                                <div>
                                                    <i class="fas fa-tools bg-primary"></i>
                                                    <div class="timeline-item">
                                                        <span class="time"><i class="far fa-clock"></i> {{ $job->created_at->format('H:i') }}</span>
                                                        <h3 class="timeline-header">
                                                            <a href="{{ route('admin.work-orders.show', $job->id) }}">Yêu cầu: {{ $job->title }}</a>
                                                        </h3>
                                                        <div class="timeline-body">
                                                            {{ $job->description }}
                                                            
                                                            {{-- Liệt kê các Task con trong Job này --}}
                                                            @if($job->tasks->isNotEmpty())
                                                                <div class="mt-2 p-2 bg-light rounded border">
                                                                    <small class="text-uppercase text-muted font-weight-bold">Chi tiết thực hiện:</small>
                                                                    <ul class="list-unstyled mb-0 mt-1">
                                                                        @foreach($job->tasks as $task)
                                                                            <li class="mb-2 border-bottom pb-2">
                                                                                <i class="fas fa-check-circle text-success text-xs"></i> 
                                                                                <b>{{ $task->performer->name ?? 'NV' }}:</b> {{ $task->report_content }}
                                                                                
                                                                                {{-- Vật tư dùng trong task --}}
                                                                                @if($task->items->isNotEmpty())
                                                                                    <br>
                                                                                    <span class="ml-3 text-muted text-xs">
                                                                                        <i class="fas fa-box"></i> 
                                                                                        @foreach($task->items as $item)
                                                                                            {{ $item->item_name }} (SL:{{ $item->quantity }}){{ !$loop->last ? ',' : '' }}
                                                                                        @endforeach
                                                                                    </span>
                                                                                @endif

                                                                                {{-- Tiền thu --}}
                                                                                @if($task->collected_amount > 0)
                                                                                    <br>
                                                                                    <span class="ml-3 text-success font-weight-bold text-xs">
                                                                                        <i class="fas fa-money-bill"></i> Thu: {{ number_format($task->collected_amount) }}đ
                                                                                    </span>
                                                                                @endif
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="timeline-footer">
                                                            <a href="{{ route('admin.work-orders.show', $job->id) }}" class="btn btn-primary btn-sm">Xem chi tiết Phiếu</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                            <div>
                                                <i class="far fa-clock bg-gray"></i>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                {{-- TAB 2: DANH SÁCH THIẾT BỊ (Để tra cứu bảo hành nhanh) --}}
                                <div class="tab-pane" id="equipment">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Tên thiết bị</th>
                                                <th>Serial / IMEI</th>
                                                <th>Ngày lắp</th>
                                                <th>Phiếu việc</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $hasItems = false; @endphp
                                            @foreach($customer->workOrders as $job)
                                                @foreach($job->tasks as $task)
                                                    @foreach($task->items as $item)
                                                        @php $hasItems = true; @endphp
                                                        <tr>
                                                            <td>{{ $item->item_name }}</td>
                                                            <td>
                                                                @if($item->serial_number)
                                                                    <span class="badge badge-warning">{{ $item->serial_number }}</span>
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $task->created_at->format('d/m/Y') }}</td>
                                                            <td>
                                                                <a href="{{ route('admin.work-orders.show', $job->id) }}">{{ $job->code }}</a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endforeach
                                            @endforeach
                                            
                                            @if(!$hasItems)
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">Khách chưa thay thế/lắp đặt thiết bị nào có ghi nhận.</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>