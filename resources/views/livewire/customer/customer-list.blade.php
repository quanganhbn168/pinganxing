<div>
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="fas fa-address-book"></i> Quản lý Khách hàng</h1>
                    <small class="text-muted">Danh sách đối tác & khách hàng cá nhân</small>
                </div>
                <a href="{{ route('admin.customers.create') }}" class="btn btn-success">
                    <i class="fas fa-user-plus"></i> Thêm Khách Mới
                </a>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        
                        {{-- KHU VỰC HÀNH ĐỘNG HÀNG LOẠT (Chỉ hiện khi có chọn) --}}
                        <div class="d-flex align-items-center" style="min-height: 38px;">
                            @if(count($selected) > 0)
                                <div class="btn-group fade-in">
                                    <button type="button" class="btn btn-default btn-sm disabled">
                                        Đã chọn <b>{{ count($selected) }}</b> khách
                                    </button>
                                    <button wire:click="deleteSelected" 
                                            wire:confirm="Bạn có chắc chắn muốn xóa {{ count($selected) }} khách hàng đã chọn không?"
                                            class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i> Xóa tất cả
                                    </button>
                                </div>
                            @else
                                <h3 class="card-title text-muted">Danh sách khách hàng</h3>
                            @endif
                        </div>

                        {{-- KHU VỰC TÌM KIẾM --}}
                        <div class="card-tools">
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light"><i class="fas fa-search"></i></span>
                                </div>
                                <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Tên, SĐT, Mã số thuế...">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-valign-middle">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 40px;" class="text-center">
                                    {{-- CHECK ALL --}}
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" id="checkAll" wire:model.live="selectAll">
                                        <label for="checkAll" class="custom-control-label"></label>
                                    </div>
                                </th>
                                <th>Thông tin Khách hàng</th>
                                <th>Liên hệ</th>
                                <th class="text-center">Hoạt động</th>
                                <th class="text-right">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customers as $cus)
                                <tr class="{{ in_array($cus->id, $selected) ? 'bg-light' : '' }}">
                                    <td class="text-center">
                                        {{-- CHECKBOX ITEM --}}
                                        <div class="custom-control custom-checkbox">
                                            <input class="custom-control-input" type="checkbox" id="cus_{{ $cus->id }}" value="{{ $cus->id }}" wire:model.live="selected">
                                            <label for="cus_{{ $cus->id }}" class="custom-control-label"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            {{-- Avatar giả lập (Lấy chữ cái đầu) --}}
                                            <div class="rounded-circle bg-secondary d-flex justify-content-center align-items-center mr-3 text-white font-weight-bold" 
                                                 style="width: 40px; height: 40px; font-size: 18px;">
                                                {{ substr($cus->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <a href="{{ route('admin.customers.show', $cus->id) }}" class="text-dark font-weight-bold" style="font-size: 16px;">
    {{ $cus->name }}
</a>
                                                <br>
                                                {{-- Logic giả: Nếu tên dài hoặc có chữ Cty -> Doanh nghiệp --}}
                                                @if(Str::contains(Str::lower($cus->name), ['công ty', 'cty', 'doanh nghiệp', 'tnhh']))
                                                    <span class="badge badge-info text-xs">Doanh nghiệp</span>
                                                @else
                                                    <span class="badge badge-light border text-xs">Cá nhân</span>
                                                @endif

                                                @if($cus->notes)
                                                    <span class="text-muted text-xs ml-1"><i class="fas fa-sticky-note"></i> {{ Str::limit($cus->notes, 40) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <ul class="list-unstyled mb-0 text-sm">
                                            @foreach($cus->contacts as $contact)
                                                <li class="mb-1">
                                                    @if($contact->type == 'phone')
                                                        <i class="fas fa-phone-alt text-success fa-fw"></i> 
                                                        <a href="tel:{{ $contact->value }}" class="text-dark font-weight-bold">{{ $contact->value }}</a>
                                                        <span class="text-muted text-xs">({{ $contact->label ?? 'Di động' }})</span>
                                                    @else
                                                        <i class="fas fa-map-marker-alt text-danger fa-fw"></i> {{ Str::limit($contact->value, 30) }}
                                                    @endif
                                                </li>
                                            @endforeach
                                            @if($cus->contacts->isEmpty())
                                                <li class="text-muted italic small">Chưa có liên hệ</li>
                                            @endif
                                        </ul>
                                    </td>
                                    <td class="text-center">
    <div class="text-muted text-xs">Số đơn hàng</div>
    <div class="font-weight-bold text-primary">{{ $cus->work_orders_count }} Job</div>
    
    {{-- Hiển thị tổng chi tiêu --}}
    @if($cus->total_spent > 0)
        <div class="text-success small mt-1">
            <i class="fas fa-dollar-sign"></i> {{ number_format($cus->total_spent) }} đ
        </div>
    @else
        <div class="text-muted small mt-1">-</div>
    @endif
</td>
                                    <td class="text-right">
                                        <a href="{{ route('admin.customers.edit', $cus->id) }}" class="btn btn-sm btn-tool" title="Xem chi tiết / Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        {{-- Chỉ hiện nút xóa nếu chưa có Job --}}
                                        @if($cus->work_orders_count == 0)
                                            <button wire:confirm="Xóa khách này?" wire:click="delete({{ $cus->id }})" class="btn btn-sm btn-tool text-danger" title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @else
                                            <span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="Khách đã có giao dịch, không thể xóa">
                                                <button class="btn btn-sm btn-tool text-muted disabled" style="pointer-events: none;" type="button" disabled>
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="80" class="mb-3 opacity-50">
                                        <p class="text-muted">Chưa có dữ liệu khách hàng.</p>
                                        <a href="{{ route('admin.customers.create') }}" class="btn btn-primary btn-sm">Thêm mới ngay</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- Pagination --}}
                @if($customers->hasPages())
                    <div class="card-footer clearfix">
                        {{ $customers->links() }}
                    </div>
                @endif
            </div>
        </div>
    </section>
    
    {{-- Style riêng cho hiệu ứng fade --}}
    <style>
        .fade-in { animation: fadeIn 0.3s; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</div>