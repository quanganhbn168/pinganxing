@extends('layouts.admin')

@section('title', 'Quản lý Nhân sự')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <h1><i class="fas fa-users-cog"></i> Quản lý Nhân sự</h1>
            <a href="{{ route('admin.staff.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm Nhân Viên
            </a>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header border-0">
                <h3 class="card-title">Danh sách tài khoản nội bộ</h3>
                <div class="card-tools">
                    {{-- Search Form --}}
                    <form action="{{ route('admin.staff.index') }}" method="GET" class="d-inline-flex">
                        <div class="input-group input-group-sm" style="width: 280px;">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   class="form-control" placeholder="Tìm tên, SĐT, Email...">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default">
                                    <i class="fas fa-search"></i>
                                </button>
                                @if(request('search'))
                                    <a href="{{ route('admin.staff.index') }}" class="btn btn-default" title="Xóa bộ lọc">
                                        <i class="fas fa-times"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-valign-middle">
                    <thead>
                        <tr>
                            <th>Nhân viên</th>
                            <th>Liên hệ</th>
                            <th>Vai trò (Role)</th>
                            <th class="text-center">Trạng thái</th>
                            <th class="text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staffs as $staff)
                            <tr id="staff-row-{{ $staff->id }}">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-info d-flex justify-content-center align-items-center mr-2 text-white font-weight-bold" 
                                             style="width: 35px; height: 35px;">
                                            {{ substr($staff->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <span class="font-weight-bold">{{ $staff->name }}</span>
                                            <br>
                                            <small class="text-muted">ID: {{ $staff->id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="small">
                                        <i class="fas fa-phone fa-fw text-muted"></i> {{ $staff->phone ?? '---' }} <br>
                                        <i class="fas fa-envelope fa-fw text-muted"></i> {{ $staff->email }}
                                    </div>
                                </td>
                                <td>
                                    @foreach($staff->roles as $role)
                                        <span class="badge {{ $role->name == 'super_admin' ? 'badge-danger' : 'badge-primary' }}">
                                            {{ $role->name == 'super_admin' ? 'Quản trị viên' : ($role->name == 'staff' ? 'Kỹ thuật viên' : $role->name) }}
                                        </span>
                                    @endforeach
                                </td>
                                <td class="text-center">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" 
                                               class="custom-control-input toggle-status" 
                                               id="status_{{ $staff->id }}"
                                               data-id="{{ $staff->id }}"
                                               {{ $staff->status ? 'checked' : '' }}
                                               {{ $staff->id == auth('admin')->id() ? 'disabled' : '' }}>
                                        <label class="custom-control-label status-label" for="status_{{ $staff->id }}">
                                            {{ $staff->status ? 'Active' : 'Blocked' }}
                                        </label>
                                    </div>
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('admin.staff.performance', $staff->id) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="Xem hiệu suất">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.staff.edit', $staff->id) }}" class="btn btn-sm btn-info" title="Sửa">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    @if($staff->id != auth('admin')->id())
                                        <button type="button" 
                                                class="btn btn-sm btn-danger btn-delete" 
                                                data-id="{{ $staff->id }}"
                                                data-name="{{ $staff->name }}"
                                                title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Chưa có nhân viên nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($staffs->hasPages())
                <div class="card-footer clearfix">
                    {{ $staffs->links() }}
                </div>
            @endif
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
$(function() {
    // Toggle Status via AJAX
    $('.toggle-status').on('change', function() {
        const $this = $(this);
        const id = $this.data('id');
        const $label = $this.siblings('.status-label');
        
        $.ajax({
            url: `/admin/staff/${id}/toggle`,
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(res) {
                if (res.success) {
                    $label.text(res.status ? 'Active' : 'Blocked');
                    toastr.success(res.message);
                } else {
                    $this.prop('checked', !$this.prop('checked'));
                    toastr.error(res.message);
                }
            },
            error: function(xhr) {
                $this.prop('checked', !$this.prop('checked'));
                toastr.error(xhr.responseJSON?.message || 'Có lỗi xảy ra.');
            }
        });
    });

    // Delete with SweetAlert2
    $('.btn-delete').on('click', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');

        Swal.fire({
            title: 'Xác nhận xóa?',
            html: `Bạn có chắc muốn xóa nhân viên <strong>${name}</strong>?<br><small class="text-danger">Lịch sử giao việc liên quan sẽ bị ảnh hưởng.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash"></i> Xóa',
            cancelButtonText: 'Hủy',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/staff/${id}`,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(res) {
                        if (res.success) {
                            $(`#staff-row-${id}`).fadeOut(300, function() { $(this).remove(); });
                            Swal.fire({
                                icon: 'success',
                                title: 'Đã xóa!',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Lỗi!', xhr.responseJSON?.message || 'Không thể xóa.', 'error');
                    }
                });
            }
        });
    });
});
</script>
@endpush
