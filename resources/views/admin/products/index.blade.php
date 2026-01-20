@extends('layouts.admin')

@section('title', 'Quản lý Sản phẩm')
@section('content_header_title', 'Sản phẩm')

@push('css')
<style>
    /* Tinh chỉnh bảng */
    .table-products .thumb { width: 48px; height: 48px; object-fit: cover; border-radius: 4px; border: 1px solid #dee2e6; }
    .table-products td { vertical-align: middle !important; }
    .table-products .td-name { max-width: 300px; }
    .table-products .td-name a { color: #333; display: block; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    
    /* Checkbox to dễ bấm */
    .custom-checkbox { width: 18px; height: 18px; cursor: pointer; }
    
    /* Giá */
    .price-group .old { font-size: 0.85rem; text-decoration: line-through; color: #999; }
    .price-group .new { font-weight: 700; color: #dc3545; }
</style>
@endpush

@section('content')
    {{-- Thông báo --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">×</button>
        </div>
    @endif

    {{-- BỘ LỌC --}}
    <div class="card collapsed-card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-filter mr-1"></i> Bộ lọc tìm kiếm</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
            </div>
        </div>
        <div class="card-body" style="display: none;">
            <form method="GET" action="{{ route('admin.products.index') }}">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <label class="small text-muted">Từ khóa</label>
                        <input type="text" name="keyword" class="form-control form-control-sm" value="{{ request('keyword') }}" placeholder="Tên, SKU...">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="small text-muted">Danh mục</label>
                        <select name="category_id" class="form-control form-control-sm">
                            <option value="">-- Tất cả --</option>
                            @foreach($filterCategories ?? [] as $id => $name)
                                <option value="{{ $id }}" {{ request('category_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="small text-muted">Trạng thái</label>
                        <select name="status" class="form-control form-control-sm">
                            <option value="">-- Tất cả --</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Hiển thị</option>
                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Ẩn</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-2 d-flex align-items-end">
                        <button class="btn btn-primary btn-sm btn-block"><i class="fas fa-search"></i> Tìm kiếm</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- DANH SÁCH --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title font-weight-bold">Danh sách</h3>

            {{-- Khu vực Tools bên phải --}}
            <div class="card-tools d-flex align-items-center">
                
                {{-- [MỚI] Nút Bulk Delete nằm ngay đây --}}
                <button type="button" id="btnBulkDelete" class="btn btn-danger btn-sm mr-2" style="display: none;" onclick="ProductManager.submitBulk('delete')">
                    <i class="fas fa-trash mr-1"></i> Xóa <span id="bulkCount" class="font-weight-bold"></span>
                </button>

                <a href="{{ route('admin.products.create') }}" class="btn btn-success btn-sm mr-2">
                    <i class="fas fa-plus"></i> <span class="d-none d-md-inline">Thêm mới</span>
                </a>
                
                <form method="GET" class="d-inline-block">
                    @foreach(request()->except('per_page', 'page') as $k => $v)
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endforeach
                    <select name="per_page" class="custom-select custom-select-sm" onchange="this.form.submit()" style="width: auto;">
                        @foreach([20, 50, 100] as $pp)
                            <option value="{{ $pp }}" {{ request('per_page') == $pp ? 'selected' : '' }}>{{ $pp }} dòng</option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        <div class="card-body p-0 table-responsive">
            <table class="table table-hover table-striped table-products mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="text-center" style="width: 40px;">
                            <input type="checkbox" id="checkAll" class="custom-checkbox">
                        </th>
                        <th style="width: 60px;">Ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Giá bán</th>
                        <th class="text-center" style="width: 100px;">Trạng thái</th>
                        <th class="text-center" style="width: 100px;">Home</th>
                        <th class="text-right" style="width: 100px;">#</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($products as $product)
                    @php
                        $img = $product->image ? asset($product->image) : ($product->mainImage() ? $product->mainImage()->url() : asset('images/no-image.png'));
                        $price = (float)$product->price;
                        $discount = (float)$product->price_discount;
                        $hasDiscount = $price > 0 && $discount > 0 && $discount < $price;
                    @endphp
                    <tr>
                        <td class="text-center">
                            <input type="checkbox" class="custom-checkbox check-item" value="{{ $product->id }}">
                        </td>
                        <td>
                            <img src="{{ $img }}" class="thumb" loading="lazy">
                        </td>
                        <td class="td-name">
                            <a href="{{ route('admin.products.edit', $product->id) }}" title="{{ $product->name }}">
                                {{ $product->name }}
                            </a>
                            <small class="text-muted d-block mt-1">
                                <i class="fas fa-tag fa-xs mr-1"></i> {{ optional($product->category)->name ?? 'N/A' }}
                            </small>
                        </td>
                        <td>
                            <div class="price-group">
                                @if ($hasDiscount)
                                    <div class="old">{{ number_format($price) }}đ</div>
                                    <div class="new">{{ number_format($discount) }}đ</div>
                                @else
                                    <div class="new">{{ $price > 0 ? number_format($price).'đ' : 'Liên hệ' }}</div>
                                @endif
                            </div>
                        </td>
                        <td class="text-center">
                            <x-boolean-toggle model="Product" :record="$product" field="status" />
                        </td>
                        <td class="text-center">
                            <x-boolean-toggle model="Product" :record="$product" field="is_home" />
                        </td>
                        <td class="text-right">
                            <div class="btn-group">
                                <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger" onclick="ProductManager.deleteSingle({{ $product->id }})">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i><br>
                            Không tìm thấy dữ liệu
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if ($products->hasPages())
            <div class="card-footer py-2">
                {{ $products->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    {{-- FORM ẨN ĐỂ GỬI REQUEST --}}
    <form id="actionForm" action="{{ route('admin.products.bulk_action') }}" method="POST" class="d-none">
        @csrf
        <input type="hidden" name="action" id="formAction">
        <div id="formIds"></div>
    </form>
@endsection

@push('js')
<script>
    const ProductManager = {
        init() {
            this.cacheDom();
            this.bindEvents();
        },

        cacheDom() {
            this.$checkAll = $('#checkAll');
            this.$checks = $('.check-item');
            // Các nút mới
            this.$btnBulkDelete = $('#btnBulkDelete');
            this.$bulkCount = $('#bulkCount');
            
            this.$form = $('#actionForm');
            this.$formAction = $('#formAction');
            this.$formIds = $('#formIds');
        },

        bindEvents() {
            this.$checkAll.on('change', (e) => {
                this.$checks.prop('checked', e.target.checked);
                this.toggleBulkButton();
            });

            this.$checks.on('change', () => {
                const allChecked = this.$checks.length === $('.check-item:checked').length;
                this.$checkAll.prop('checked', allChecked);
                this.toggleBulkButton();
            });
        },

        // [MỚI] Logic nút Xóa nằm trong card-tools
        toggleBulkButton() {
            const count = $('.check-item:checked').length;
            
            if (count > 0) {
                this.$bulkCount.text(`(${count})`); // Cập nhật số: (5)
                this.$btnBulkDelete.show();
            } else {
                this.$btnBulkDelete.hide();
            }
        },

        // Giữ nguyên logic xóa lẻ
        deleteSingle(id) {
            Swal.fire({
                title: 'Xóa sản phẩm này?',
                text: "Hành động này không thể hoàn tác!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Xóa ngay',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submitBulk('delete', [id]);
                }
            });
        },

        // Giữ nguyên logic gửi form
        submitBulk(action, specificIds = null) {
            let ids = [];
            
            if (specificIds) {
                ids = specificIds;
            } else {
                $('.check-item:checked').each(function() {
                    ids.push($(this).val());
                });
            }

            if (ids.length === 0) return;

            if (action === 'delete' && !specificIds) {
                Swal.fire({
                    title: 'Xác nhận xóa?',
                    text: `Bạn đang chọn xóa ${ids.length} sản phẩm.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Xóa ngay',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.processSubmit(action, ids);
                    }
                });
            } else {
                this.processSubmit(action, ids);
            }
        },

        processSubmit(action, ids) {
            this.$formAction.val(action);
            this.$formIds.empty();
            ids.forEach(id => {
                this.$formIds.append(`<input type="hidden" name="ids[]" value="${id}">`);
            });
            this.$form.submit();
        }
    };

    $(document).ready(function() {
        ProductManager.init();
    });
</script>
@endpush