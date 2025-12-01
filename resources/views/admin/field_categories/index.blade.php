@extends('layouts.admin')

@section('title', 'Danh mục Lĩnh vực')

@push('css')
<style>
    /* Checkbox to dễ bấm */
    .custom-checkbox { width: 18px; height: 18px; cursor: pointer; vertical-align: middle; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">@yield('title')</h1>
        
        <div class="d-flex align-items-center">
            {{-- [MỚI] Nút Bulk Delete --}}
            <button type="button" id="btnBulkDelete" class="btn btn-danger shadow-sm mr-3" style="display: none;" onclick="FieldCategoryManager.submitBulk('delete')">
                <i class="fas fa-trash fa-sm text-white-50"></i> Xóa <span id="bulkCount" class="font-weight-bold"></span>
            </button>

            <a href="{{ route('admin.field-categories.create') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> Thêm mới
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            {{-- [MỚI] Checkbox All --}}
                            <th class="text-center" width="5%">
                                <input type="checkbox" id="checkAll" class="custom-checkbox">
                            </th>
                            <th width="5%">STT</th>
                            <th>Tên danh mục</th>
                            <th>Danh mục cha</th>
                            <th width="15%">Trạng thái</th>
                            <th width="10%">Thứ tự</th>
                            <th width="15%">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $index => $category)
                            <tr>
                                {{-- [MỚI] Checkbox Item --}}
                                <td class="text-center">
                                    <input type="checkbox" class="custom-checkbox check-item" value="{{ $category->id }}">
                                </td>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    @if($category->parent_id)
                                        <span class="text-muted">&mdash;</span> 
                                    @endif
                                    <strong>{{ $category->name }}</strong>
                                </td>
                                <td>
                                    {{ $category->parent->name ?? '—' }}
                                </td>
                                <td>
                                    <x-boolean-toggle
                                        model="field_category"
                                        :record="$category"
                                        field="status"
                                        onText="Hoạt động"
                                        offText="Tạm ẩn"
                                    />
                                </td>
                                <td>{{ $category->order ?? 0 }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.field-categories.edit', $category) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <button type="button" class="btn btn-danger btn-sm" 
                                                onclick="FieldCategoryManager.deleteSingle({{ $category->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Chưa có danh mục nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- FORM ẨN BULK ACTION --}}
<form id="actionForm" action="{{ route('admin.field-categories.bulk_action') }}" method="POST" class="d-none">
    @csrf
    <input type="hidden" name="action" id="formAction">
    <div id="formIds"></div>
</form>
@endsection

@push('js')
<script>
    const FieldCategoryManager = {
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
        FieldCategoryManager.init();
    });
</script>
@endpush