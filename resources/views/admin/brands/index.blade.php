@extends('layouts.admin')

@section('title', 'Danh sách thương hiệu')
@section('content_header', 'Danh sách thương hiệu')

@push('css')
<style>
    .table-brands .thumb{width:56px;height:56px;object-fit:cover;border-radius:6px;border:1px solid #dee2e6;}
    /* Checkbox to dễ bấm */
    .custom-checkbox { width: 18px; height: 18px; cursor: pointer; vertical-align: middle; }
</style>
@endpush

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Đã có lỗi xảy ra:</strong>
            <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
        </div>
    @endif

    {{-- FILTER --}}
    <div class="card collapsed-card">
        <div class="card-header">
            <h3 class="card-title">Bộ lọc</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
            </div>
        </div>
        <div class="card-body" style="display: none;">
            <form method="GET" action="{{ route('admin.brands.index') }}" class="row">
                <div class="col-md-5">
                    <x-form.input name="keyword" label="Từ khóa" :value="request('keyword')" placeholder="Tên thương hiệu/Slug..." />
                </div>
                <div class="col-md-3">
                    <x-form.select
                        name="status"
                        label="Trạng thái"
                        :options="['1' => 'Hiển thị', '0' => 'Ẩn']"
                        :selected="request('status')"
                        placeholder="-- Tất cả --" />
                </div>
                <div class="col-md-2">
                    <label class="d-block">&nbsp;</label>
                    <button class="btn btn-secondary btn-block"><i class="fas fa-search"></i> Lọc</button>
                </div>
            </form>
        </div>
    </div>

    {{-- LIST --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Danh sách thương hiệu</h3>
            <div class="card-tools d-flex align-items-center">
                {{-- [MỚI] Nút Bulk Delete --}}
                <button type="button" id="btnBulkDelete" class="btn btn-danger btn-sm mr-2" style="display: none;" onclick="BrandManager.submitBulk('delete')">
                    <i class="fas fa-trash mr-1"></i> Xóa <span id="bulkCount" class="font-weight-bold"></span>
                </button>

                <a href="{{ route('admin.brands.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus-circle"></i> Thêm thương hiệu
                </a>
            </div>
        </div>

        <div class="card-body p-0 table-responsive">
            <table class="table table-hover table-striped mb-0 table-brands">
                <thead>
                <tr>
                    {{-- [MỚI] Checkbox All --}}
                    <th class="text-center" style="width: 40px">
                        <input type="checkbox" id="checkAll" class="custom-checkbox">
                    </th>
                    <th style="width:60px">#</th>
                    <th style="width:80px">Ảnh</th>
                    <th>Tên thương hiệu</th>
                    <th>Slug</th>
                    <th style="width:120px" class="text-center">Trạng thái</th>
                    <th style="width:150px">Ngày tạo</th>
                    <th style="width:120px" class="text-right">Thao tác</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($brands as $index => $item)
                    @php
                        $row = ($brands->currentPage() - 1) * $brands->perPage() + $index + 1;
                        
                        // Xử lý ảnh: Ưu tiên cột image mới -> Fallback Media Manager -> Ảnh mặc định
                        $thumbUrl = $item->image ? asset($item->image) : null;
                        if (!$thumbUrl && method_exists($item, 'mainImage') && $item->mainImage()) {
                             $img = $item->mainImage();
                             $thumbUrl = $img->url('thumbnail') ?: $img->url();
                        }
                        $thumbUrl ??= asset('images/setting/no-image.png');
                    @endphp
                    <tr>
                        {{-- [MỚI] Checkbox Item --}}
                        <td class="text-center">
                            <input type="checkbox" class="custom-checkbox check-item" value="{{ $item->id }}">
                        </td>
                        <td>{{ $row }}</td>
                        <td>
                            <img src="{{ $thumbUrl }}" alt="{{ $item->name }}" class="thumb">
                        </td>
                        <td>
                            <a href="{{ route('admin.brands.edit', $item->id) }}" class="font-weight-bold text-dark">
                                {{ $item->name }}
                            </a>
                        </td>
                        <td class="text-muted small">{{ $item->slug }}</td>
                        <td class="text-center">
                            <x-boolean-toggle model="Brand" :record="$item" field="status" />
                        </td>
                        <td>{{ $item->created_at->format('d/m/Y') }}</td>
                        <td class="text-right">
                            <div class="btn-group">
                                <a href="{{ route('admin.brands.edit', $item->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                {{-- Nút nhân bản (Optional) --}}
                                <x-admin.duplicate-button 
                                    model="brands" 
                                    :id="$item->id"
                                    label="" 
                                    icon="fas fa-copy" 
                                    confirm="Nhân bản thương hiệu này?" 
                                    class="btn btn-sm btn-info" 
                                />

                                <button type="button" class="btn btn-sm btn-danger" 
                                        onclick="BrandManager.deleteSingle({{ $item->id }})">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">Chưa có thương hiệu nào.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer clearfix">
            {{ $brands->appends(request()->query())->links() }}
        </div>
    </div>

    {{-- FORM ẨN BULK ACTION --}}
    <form id="actionForm" action="{{ route('admin.brands.bulk_action') }}" method="POST" class="d-none">
        @csrf
        <input type="hidden" name="action" id="formAction">
        <div id="formIds"></div>
    </form>
@endsection

@push('js')
<script>
    const BrandManager = {
        init() {
            this.cacheDom();
            this.bindEvents();
        },

        cacheDom() {
            this.$checkAll = $('#checkAll');
            this.$checks = $('.check-item');
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

        toggleBulkButton() {
            const count = $('.check-item:checked').length;
            if (count > 0) {
                this.$bulkCount.text(`(${count})`);
                this.$btnBulkDelete.show();
            } else {
                this.$btnBulkDelete.hide();
            }
        },

        deleteSingle(id) {
            Swal.fire({
                title: 'Xóa thương hiệu này?',
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
                    title: 'Xác nhận xóa hàng loạt?',
                    text: `Bạn đang chọn xóa ${ids.length} thương hiệu.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Đồng ý xóa',
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
        BrandManager.init();
    });
</script>
@endpush