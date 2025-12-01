@extends('layouts.admin')

@section('title', 'Quản lý Bài viết')
@section('content_header', 'Quản lý Bài viết')

@push('css')
<style>
    /* Checkbox to dễ bấm */
    .custom-checkbox { width: 18px; height: 18px; cursor: pointer; vertical-align: middle; }
    
    /* Tinh chỉnh ảnh thumbnail */
    .table-hover .thumb { width: 48px; height: 48px; object-fit: cover; border-radius: 4px; border: 1px solid #dee2e6; }
    
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
            <h3 class="card-title">Danh sách bài viết</h3>
            
            <div class="card-tools d-flex align-items-center">
                {{-- [MỚI] Nút Bulk Delete --}}
                <button type="button" id="btnBulkDelete" class="btn btn-danger btn-sm mr-2" style="display: none;" onclick="PostManager.submitBulk('delete')">
                    <i class="fas fa-trash mr-1"></i> Xóa <span id="bulkCount" class="font-weight-bold"></span>
                </button>

                <a href="{{ route('admin.posts.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus mr-1"></i> Thêm bài viết
                </a>
            </div>
        </div>

        <div class="card-body">
            {{-- Bộ lọc --}}
            <div class="row mb-3">
                <div class="col-md-12">
                    <form method="GET" action="{{ route('admin.posts.index') }}" class="form-inline">
                        <div class="input-group input-group-sm" style="width: 300px;">
                            <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control" placeholder="Tìm bài viết...">
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
                            <th>Tiêu đề</th>
                            <th>Danh mục</th>
                            <th class="text-center">Nổi bật</th>
                            <th class="text-center">Trạng thái</th>
                            <th style="width: 120px" class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($posts as $key => $post)
                        <tr>
                            {{-- [MỚI] Checkbox Item --}}
                            <td class="text-center">
                                <input type="checkbox" class="custom-checkbox check-item" value="{{ $post->id }}">
                            </td>
                            <td>{{ $key + 1 }}</td>
                            <td>
                                @if($post->mainImage())
                                    <img src="{{ Storage::url($post->mainImage()->main_path) }}" alt="{{ $post->title }}" class="thumb">
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $post->title }}</strong>
                                @if($post->slug)
                                    <br><small class="text-muted">{{ $post->slug }}</small>
                                @endif
                            </td>
                            <td>
                                @if($post->category)
                                    <span class="badge badge-info">{{ $post->category->name }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            
                            {{-- Các toggle (Sửa model thành Post) --}}
                            <td class="text-center">
                                <x-boolean-toggle model="Post" :record="$post" field="is_featured" />
                            </td>
                            <td class="text-center">
                                <x-boolean-toggle model="Post" :record="$post" field="status" />
                            </td>

                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('admin.posts.edit', $post) }}" 
                                       class="btn btn-sm btn-warning" title="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    {{-- Sửa model duplicate thành posts --}}
                                    <x-admin.duplicate-button 
                                        model="posts" 
                                        :id="$post->id"
                                        label="" 
                                        icon="fas fa-copy" 
                                        confirm="Nhân bản bài viết này?" 
                                        class="btn btn-sm btn-info" 
                                    />

                                    {{-- Nút xóa dùng JS chung --}}
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            onclick="PostManager.deleteSingle({{ $post->id }})" title="Xóa">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-file-alt fa-2x mb-2"></i><br>
                                Chưa có bài viết nào
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Phân trang --}}
            @if($posts->hasPages())
            <div class="card-footer clearfix">
                {{ $posts->links() }}
            </div>
            @endif
        </div>
    </div>

    {{-- FORM ẨN ĐỂ GỬI REQUEST BULK ACTION --}}
    <form id="actionForm" action="{{ route('admin.posts.bulk_action') }}" method="POST" class="d-none">
        @csrf
        <input type="hidden" name="action" id="formAction">
        <div id="formIds"></div>
    </form>
@endsection

@push('js')
<script>
    const PostManager = {
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
                title: 'Xóa bài viết này?',
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
                    text: `Bạn đang chọn xóa ${ids.length} bài viết.`,
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
        PostManager.init();
    });
</script>
@endpush