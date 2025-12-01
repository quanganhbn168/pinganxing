@extends('layouts.admin')

@section('title', 'Dự án')
@section('content_header_title', 'Quản lý dự án')

@push('css')
<style>
    .table-projects .thumb{width:56px;height:56px;object-fit:cover;border-radius:6px}
    .table-projects .td-name{max-width:360px}
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
            <form method="GET" action="{{ route('admin.projects.index') }}" class="row">
                <div class="col-md-4">
                    <x-form.input name="keyword" label="Từ khóa" :value="request('keyword')" placeholder="Tên/Slug..." />
                </div>
                <div class="col-md-3">
                    <x-form.select
                        name="project_category_id"
                        label="Danh mục"
                        :options="$filterCategories ?? []"
                        :selected="request('project_category_id')"
                        placeholder="-- Tất cả danh mục --" />
                </div>
                <div class="col-md-2">
                    <x-form.select
                        name="status"
                        label="Trạng thái"
                        :options="['1' => 'Hiển thị', '0' => 'Ẩn']"
                        :selected="request('status')"
                        placeholder="-- Tất cả --" />
                </div>
                <div class="col-md-2">
                    <x-form.select
                        name="is_home"
                        label="Trang chủ"
                        :options="['1' => 'Có', '0' => 'Không']"
                        :selected="request('is_home')"
                        placeholder="-- Tất cả --" />
                </div>
                <div class="col-md-1">
                    <label class="d-block">&nbsp;</label>
                    <button class="btn btn-secondary btn-block"><i class="fas fa-search"></i> Lọc</button>
                </div>
            </form>
        </div>
    </div>

    {{-- LIST --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Danh sách dự án</h3>
            <div class="card-tools d-flex align-items-center">
                {{-- [MỚI] Nút Bulk Delete --}}
                <button type="button" id="btnBulkDelete" class="btn btn-danger btn-sm mr-2" style="display: none;" onclick="ProjectManager.submitBulk('delete')">
                    <i class="fas fa-trash mr-1"></i> Xóa <span id="bulkCount" class="font-weight-bold"></span>
                </button>

                <a href="{{ route('admin.projects.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus-circle"></i> Thêm dự án
                </a>
            </div>
        </div>

        <div class="card-body p-0 table-responsive">
            <table class="table table-hover table-striped mb-0 table-projects">
                <thead>
                <tr>
                    {{-- [MỚI] Checkbox All --}}
                    <th class="text-center" style="width: 40px">
                        <input type="checkbox" id="checkAll" class="custom-checkbox">
                    </th>
                    <th style="width:60px">#</th>
                    <th style="width:72px">Ảnh</th>
                    <th>Tên dự án</th>
                    <th>Danh mục</th>
                    <th style="width:120px">Trạng thái</th>
                    <th style="width:110px">Trang chủ</th>
                    <th style="width:150px">Tạo lúc</th>
                    <th style="width:120px" class="text-right">Thao tác</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($projects as $index => $project)
                    @php
                        $row = ($projects->currentPage() - 1) * $projects->perPage() + $index + 1;
                        $img = method_exists($project,'mainImage') ? $project->mainImage() : null;
                        $thumbUrl = $img ? ($img->url('thumbnail') ?: $img->url()) : null;
                        $thumbUrl ??= $project->image ? asset($project->image) : asset('images/setting/no-image.png');
                    @endphp
                    <tr>
                        {{-- [MỚI] Checkbox Item --}}
                        <td class="text-center">
                            <input type="checkbox" class="custom-checkbox check-item" value="{{ $project->id }}">
                        </td>
                        <td>{{ $row }}</td>
                        <td><img class="thumb" src="{{ $thumbUrl }}" alt="{{ $project->name }}"></td>
                        <td class="td-name">
                            <a href="{{ route('admin.projects.edit', $project->id) }}" class="font-weight-bold">{{ $project->name }}</a>
                            <div class="small text-muted">Slug: {{ $project->slug }}</div>
                        </td>
                        <td>{{ optional($project->category)->name ?? '—' }}</td>
                        <td>
                            <x-boolean-toggle model="Project" :record="$project" field="status" />
                        </td>
                        <td>
                            <x-boolean-toggle model="Project" :record="$project" field="is_home" />
                        </td>
                        <td>{{ optional($project->created_at)->format('d/m/Y H:i') }}</td>
                        <td class="text-right">
                            <div class="btn-group">
                                <a href="{{ route('admin.projects.edit', $project->id) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                
                                {{-- Nút duplicate (nếu cần) --}}
                                <x-admin.duplicate-button 
                                    model="projects" 
                                    :id="$project->id"
                                    label="" 
                                    icon="fas fa-copy" 
                                    confirm="Nhân bản dự án này?" 
                                    class="btn btn-sm btn-info" 
                                />

                                <button type="button" class="btn btn-sm btn-danger" 
                                        onclick="ProjectManager.deleteSingle({{ $project->id }})">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center text-muted">Chưa có dự án nào.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if ($projects instanceof \Illuminate\Pagination\AbstractPaginator)
            <div class="card-footer clearfix">
                {{ $projects->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    {{-- FORM ẨN BULK ACTION --}}
    <form id="actionForm" action="{{ route('admin.projects.bulk_action') }}" method="POST" class="d-none">
        @csrf
        <input type="hidden" name="action" id="formAction">
        <div id="formIds"></div>
    </form>
@endsection

@push('js')
<script>
    const ProjectManager = {
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
                title: 'Xóa dự án này?',
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
                    text: `Bạn đang chọn xóa ${ids.length} dự án.`,
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
        ProjectManager.init();
    });
</script>
@endpush