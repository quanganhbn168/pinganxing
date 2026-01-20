@extends('layouts.admin')

@section('title', 'Giới thiệu (Intro)')
@section('content_header_title', 'Quản lý Intro')

@push('css')
<style>
    .table-intros .thumb{width:56px;height:56px;object-fit:cover;border-radius:6px}
    .table-intros .td-title{max-width:420px}
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
            <ul class="mb-0">
                @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
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
            <form method="GET" action="{{ route('admin.intros.index') }}" class="row">
                <div class="col-md-5">
                    <x-form.input name="keyword" label="Từ khóa" :value="request('keyword')" placeholder="Tiêu đề/Slug..." />
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
            <h3 class="card-title">Danh sách Intro</h3>
            <div class="card-tools d-flex align-items-center">
                {{-- [MỚI] Nút Bulk Delete --}}
                <button type="button" id="btnBulkDelete" class="btn btn-danger btn-sm mr-2" style="display: none;" onclick="IntroManager.submitBulk('delete')">
                    <i class="fas fa-trash mr-1"></i> Xóa <span id="bulkCount" class="font-weight-bold"></span>
                </button>

                <a href="{{ route('admin.intros.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus-circle"></i> Thêm Intro
                </a>
            </div>
        </div>

        <div class="card-body p-0 table-responsive">
            <table class="table table-hover table-striped mb-0 table-intros">
                <thead>
                <tr>
                    {{-- [MỚI] Checkbox All --}}
                    <th class="text-center" style="width: 40px">
                        <input type="checkbox" id="checkAll" class="custom-checkbox">
                    </th>
                    <th style="width:60px">#</th>
                    <th style="width:72px">Ảnh</th>
                    <th>Tiêu đề</th>
                    <th>Slug</th>
                    <th style="width:120px">Trạng thái</th>
                    <th style="width:160px">Tạo lúc</th>
                    <th style="width:120px" class="text-right">Thao tác</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($intros as $index => $intro)
                    @php
                        $row = ($intros->currentPage() - 1) * $intros->perPage() + $index + 1;
                        $thumbUrl = $intro->image ? asset($intro->image) : null;
                        if (!$thumbUrl && method_exists($intro,'mainImage') && $intro->mainImage()) {
                             $img = $intro->mainImage();
                             $thumbUrl = $img->url('thumbnail') ?: $img->url();
                        }
                        $thumbUrl ??= asset('images/setting/no-image.png');
                        
                        // Kiểm tra Intro mặc định (ID=1)
                        $isDefault = ($intro->id == 1);
                    @endphp
                    <tr>
                        {{-- [MỚI] Checkbox Item: Không hiện checkbox nếu là Intro mặc định --}}
                        <td class="text-center">
                            @if(!$isDefault)
                                <input type="checkbox" class="custom-checkbox check-item" value="{{ $intro->id }}">
                            @endif
                        </td>
                        <td>{{ $row }}</td>
                        <td><img class="thumb" src="{{ $thumbUrl }}" alt="{{ $intro->title }}"></td>
                        <td class="td-title">
                            <a href="{{ route('admin.intros.edit', $intro->id) }}" class="font-weight-bold">{{ $intro->title }}</a>
                        </td>
                        <td class="small text-muted">{{ $intro->slug }}</td>
                        <td>
                            <x-boolean-toggle model="Intro" :record="$intro" field="status" />
                        </td>
                        <td>{{ optional($intro->created_at)->format('d/m/Y H:i') }}</td>
                        <td class="text-right">
                            <div class="btn-group">
                                <a href="{{ route('admin.intros.edit', $intro->id) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                
                                {{-- Nút xóa lẻ: Chỉ hiện nếu không phải mặc định --}}
                                @if(!$isDefault)
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            onclick="IntroManager.deleteSingle({{ $intro->id }})">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted">Chưa có Intro nào.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if ($intros instanceof \Illuminate\Pagination\AbstractPaginator)
            <div class="card-footer clearfix">
                {{ $intros->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    {{-- FORM ẨN BULK ACTION --}}
    <form id="actionForm" action="{{ route('admin.intros.bulk_action') }}" method="POST" class="d-none">
        @csrf
        <input type="hidden" name="action" id="formAction">
        <div id="formIds"></div>
    </form>
@endsection

@push('js')
<script>
    const IntroManager = {
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

            $(document).on('change', '.check-item', () => {
                const allChecked = $('.check-item').length > 0 && $('.check-item').length === $('.check-item:checked').length;
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
                title: 'Xóa Intro này?',
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
                    text: `Bạn đang chọn xóa ${ids.length} mục.`,
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
        IntroManager.init();
    });
</script>
@endpush