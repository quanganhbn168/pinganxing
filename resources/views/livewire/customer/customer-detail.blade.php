<div>
    {{-- HEADER & STATS --}}
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-sm-6">
                    <h1>Hồ sơ: <b>{{ $customer->name }}</b></h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-default">Quay lại</a>
                    <a href="{{ route('admin.customers.edit', $customer->id) }}" class="btn btn-warning"><i class="fas fa-edit"></i> Sửa</a>
                </div>
            </div>

            {{-- Thống kê --}}
            <div class="row">
                <div class="col-md-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ number_format($stats['total_spent']) }}<sup style="font-size: 20px">đ</sup></h3>
                            <p>Tổng chi tiêu</p>
                        </div>
                        <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $stats['total_orders'] }}</h3>
                            <p>Phiếu việc</p>
                        </div>
                        <div class="icon"><i class="fas fa-clipboard-list"></i></div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $stats['active_warranties'] }}</h3>
                            <p>Thiết bị còn BH</p>
                        </div>
                        <div class="icon"><i class="fas fa-shield-alt"></i></div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3>{{ $stats['last_date'] }}</h3>
                            <p>Giao dịch cuối</p>
                        </div>
                        <div class="icon"><i class="fas fa-clock"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- MAIN CONTENT --}}
    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary card-outline card-outline-tabs">
                <div class="card-header p-0 border-bottom-0">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link {{ $activeTab == 'work_orders' ? 'active' : '' }}" 
                               href="javascript:void(0)" wire:click="switchTab('work_orders')">Lịch sử Phiếu Việc</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $activeTab == 'warranties' ? 'active' : '' }}" 
                               href="javascript:void(0)" wire:click="switchTab('warranties')">Bảo hành & Thiết bị</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $activeTab == 'info' ? 'active' : '' }}" 
                               href="javascript:void(0)" wire:click="switchTab('info')">Thông tin liên hệ</a>
                        </li>
                    </ul>
                </div>
                
                <div class="card-body">
                    
                    {{-- TAB 1: DANH SÁCH PHIẾU VIỆC --}}
                    @if($activeTab == 'work_orders')
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>Mã Phiếu</th>
                                    <th>Ngày tạo</th>
                                    <th>Tiêu đề / Yêu cầu</th>
                                    <th>Trạng thái</th>
                                    <th class="text-right">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($workOrders as $order)
                                    <tr>
                                        <td><a href="{{ route('admin.work-orders.show', $order->id) }}"><b>{{ $order->code }}</b></a></td>
                                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                        <td>{{ Str::limit($order->title, 50) }}</td>
                                        <td>
                                            @php
                                                $statusEnum = $order->status instanceof \App\Enums\WorkOrderStatus 
                                                    ? $order->status 
                                                    : \App\Enums\WorkOrderStatus::tryFrom($order->status);
                                            @endphp
                                            @if($statusEnum)
                                                <span class="badge badge-{{ $statusEnum->color() }}">{{ $statusEnum->label() }}</span>
                                            @else
                                                <span class="badge badge-secondary">{{ $order->status }}</span>
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            <a href="{{ route('admin.work-orders.show', $order->id) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-muted">Khách chưa có phiếu việc nào.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    @endif

                    {{-- TAB 2: BẢO HÀNH --}}
                    @if($activeTab == 'warranties')
                        
                        {{-- A. Bảo hành Dịch vụ (Gói) --}}
                        <h6 class="font-weight-bold text-primary mb-2"><i class="fas fa-file-contract mr-1"></i> Các gói Bảo hành Dịch vụ</h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-sm table-bordered bg-light">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Mã Job</th>
                                        <th>Nội dung / Ghi chú</th>
                                        <th>Ngày kích hoạt</th>
                                        <th>Thời hạn</th>
                                        <th>Ngày hết hạn</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($serviceWarranties as $svc)
                                        <tr>
                                            <td class="align-middle"><a href="{{ route('admin.work-orders.show', $svc->work_order_id) }}"><b>{{ $svc->wo_code }}</b></a></td>
                                            <td class="align-middle">
                                                {{ $svc->wo_title }} <br>
                                                <small class="text-muted">{{ $svc->notes }}</small>
                                            </td>
                                            <td class="align-middle">{{ \Carbon\Carbon::parse($svc->start_date)->format('d/m/Y') }}</td>
                                            
                                            {{-- Thời hạn --}}
                                            <td class="align-middle">{{ $svc->warranty_months }} tháng</td>

                                            {{-- Ngày hết hạn (HIỆN RÕ) --}}
                                            <td class="align-middle">
                                                <strong class="text-danger" style="font-size: 1.1em;">
                                                    {{ $svc->expiration_date ? \Carbon\Carbon::parse($svc->expiration_date)->format('d/m/Y') : '---' }}
                                                </strong>
                                            </td>

                                            {{-- Trạng thái --}}
                                            <td class="align-middle text-center">
                                                @if($svc->expiration_date && \Carbon\Carbon::now()->gt($svc->expiration_date))
                                                    <span class="badge badge-secondary">Hết hạn</span>
                                                @else
                                                    <span class="badge badge-success">Còn hạn</span>
                                                @endif
                                            </td>

                                            {{-- Nút sửa --}}
                                            <td class="align-middle text-center">
                                                <a href="{{ route('admin.warranty.create', $svc->work_order_id) }}" class="btn btn-xs btn-outline-primary" title="Sửa bảo hành">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="text-center text-muted small">Chưa có gói bảo hành dịch vụ nào.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- B. Bảo hành Thiết bị (Lẻ) --}}
                        <h6 class="font-weight-bold text-success mb-2"><i class="fas fa-microchip mr-1"></i> Thiết bị & Linh kiện (Theo Serial)</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Tên thiết bị</th>
                                        <th>Serial Number</th>
                                        <th>Ngày kích hoạt</th>
                                        <th>Ngày hết hạn</th>
                                        <th>Trạng thái</th>
                                        <th>Nguồn gốc</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($warranties as $device)
                                        <tr>
                                            <td class="font-weight-bold align-middle">{{ $device->device_name }}</td>
                                            <td class="text-muted font-monospace align-middle">{{ $device->serial_number }}</td>
                                            
                                            <td class="align-middle">
                                                 {{ \Carbon\Carbon::parse($device->start_date)->format('d/m/Y') }}
                                                 <br><small class="text-muted">({{ $device->warranty_months }} tháng)</small>
                                            </td>

                                            <td class="align-middle">
                                                <strong class="text-danger">
                                                    {{ \Carbon\Carbon::parse($device->expiration_date)->format('d/m/Y') }}
                                                </strong>
                                            </td>
                                            
                                            <td class="align-middle">
                                                @if(\Carbon\Carbon::now()->gt($device->expiration_date))
                                                    <span class="badge badge-secondary">Hết hạn</span>
                                                @else
                                                    <span class="badge badge-success">Còn bảo hành</span>
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                                <a href="{{ route('admin.work-orders.show', $device->work_order_id) }}" class="text-xs">
                                                    Job #{{ $device->wo_code }}
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center text-muted">Chưa có thiết bị nào được ghi nhận bảo hành.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @endif

                    {{-- TAB 3: THÔNG TIN LIÊN HỆ --}}
                    @if($activeTab == 'info')
                        <div class="row">
                            <div class="col-md-6">
                                <strong><i class="fas fa-user mr-1"></i> Tên khách hàng</strong>
                                <p class="text-muted">{{ $customer->name }}</p>
                                <hr>
                                <strong><i class="far fa-file-alt mr-1"></i> Ghi chú nội bộ</strong>
                                <p class="text-muted">{{ $customer->notes ?? 'Không có ghi chú.' }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong><i class="fas fa-address-book mr-1"></i> Danh sách liên hệ</strong>
                                <ul class="list-group list-group-flush mt-2">
                                    @foreach($customer->contacts as $contact)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>
                                                @if($contact->type == 'phone') <i class="fas fa-phone text-success mr-2"></i>
                                                @else <i class="fas fa-map-marker-alt text-danger mr-2"></i> @endif
                                                {{ $contact->value }}
                                            </span>
                                            @if($contact->is_primary) <span class="badge badge-info">Chính</span> @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>