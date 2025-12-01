@extends('layouts.admin')

@section('title', 'Danh mục bài viết')
@section('content_header', 'Danh mục bài viết')

@push('css')
<style>
    /* Checkbox to dễ bấm */
    .custom-checkbox { width: 18px; height: 18px; cursor: pointer; vertical-align: middle; }
    
    /* Tinh chỉnh ảnh thumbnail */
    .table-hover .thumb { width: 40px; height: 40px; object-fit: cover; border-radius: 4px; border: 1px solid #dee2e6; }
    
    /* Căn giữa nội dung bảng */
    .table-hover td { vertical-align: middle !important; }
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

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Danh sách danh mục</h3>
            <div class="card-tools d-flex align-items-center">
                {{-- [MỚI] Nút Bulk Delete --}}
                <button type="button" id="btnBulkDelete" class="btn btn-danger btn-sm mr-2" style="display: none;" onclick="PostCategoryManager.submitBulk('delete')">
                    <i class="fas fa-trash mr-1"></i> Xóa <span id="bulkCount" class="font-weight-bold"></span>
                </button>

                <a href="{{ route('admin.post-categories.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus mr-1"></i> Thêm danh mục
                </a>
            </div>
        </div>

        <div class="card-body">
            {{-- Bộ lọc --}}
            <div class="row mb-3">
                <div class="col-md-12">
                    <form method="GET" action="{{ route('admin.post-categories.index') }}" class="form-inline">
                        <div class="input-group input-group-sm" style="width: 300px;">
                            <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control" placeholder="Tìm kiếm...">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Bảng danh sách --}}
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            {{-- [MỚI] Checkbox All --}}
                            <th class="text-center" style="width: 40px">
                                <input type="checkbox" id="checkAll" class="custom-checkbox">
                            </th>
                            <th style="width: 50px">#</th>
                            <th>Ảnh</th>
                            <th>Tên danh mục</th>
                            <th>Danh mục cha</th>
                            <th class="text-center">Home</th>
                            <th class="text-center">Trạng thái</th>
                            <th style="width: 120px" class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $key => $category)
                        <tr>
                            {{-- [MỚI] Checkbox Item --}}
                            <td class="text-center">
                                <input type="checkbox" class="custom-checkbox check-item" value="{{ $category->id }}">
                            </td>
                            <td>{{ $key + 1 }}</td>
                            <td>
                                @if($category->mainImage())
                                    <img src="{{ Storage::url($category->mainImage()->main_path) }}" alt="{{ $category->name }}" class="thumb">
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $category->name }}</strong>
                                @if($category->slug)
                                    <br><small class="text-muted">{{ $category->slug }}</small>
                                @endif
                            </td>
                            <td>{{ $category->parent->name ?? '—' }}</td>
                            
                            {{-- Các toggle --}}
                            <td class="text-center">
                                <x-boolean-toggle model="PostCategory" :record="$category" field="is_home" />
                            </td>
                            <td class="text-center">
                                <x-boolean-toggle model="PostCategory" :record="$category" field="status" />
                            </td>

                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('admin.post-categories.edit', $category) }}" 
                                       class="btn btn-sm btn-warning" title="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <x-admin.duplicate-button 
                                        model="post_categories" 
                                        :id="$category->id"
                                        label="" 
                                        icon="fas fa-copy" 
                                        confirm="Nhân bản danh mục này?" 
                                        class="btn btn-sm btn-info" 
                                    />

                                    {{-- Nút xóa dùng JS chung --}}
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            onclick="PostCategoryManager.deleteSingle({{ $category->id }})" title="Xóa">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-folder-open fa-2x mb-2"></i><br>
                                Chưa có danh mục nào
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Phân trang --}}
            @if($categories->hasPages())
            <div class="card-footer clearfix">
                {{ $categories->links() }}
            </div>
            @endif
        </div>
    </div>

    {{-- FORM ẨN ĐỂ GỬI REQUEST BULK ACTION --}}
    <form id="actionForm" action="{{ route('admin.post-categories.bulk_action') }}" method="POST" class="d-none">
        @csrf
        <input type="hidden" name="action" id="formAction">
        <div id="formIds"></div>
    </form>
@endsection

@push('js')
<script>
    const PostCategoryManager = {
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
                title: 'Xóa danh mục này?',
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
                    text: `Bạn đang chọn xóa ${ids.length} danh mục.`,
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
        PostCategoryManager.init();
    });
</script>
@endpush