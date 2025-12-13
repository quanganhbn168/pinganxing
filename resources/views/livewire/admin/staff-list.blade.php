<div>
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
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" wire:model.live.debounce.300ms="search" class="form-control float-right" placeholder="Tìm tên, SĐT, Email...">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
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
                                <tr>
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
                                            <input type="checkbox" class="custom-control-input" id="status_{{ $staff->id }}" 
                                                   wire:click="toggleStatus({{ $staff->id }})" 
                                                   {{ $staff->status ? 'checked' : '' }}
                                                   {{ $staff->id == auth('admin')->id() ? 'disabled' : '' }}>
                                            <label class="custom-control-label" for="status_{{ $staff->id }}">
                                                {{ $staff->status ? 'Active' : 'Blocked' }}
                                            </label>
                                        </div>
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('admin.staff.performance', $staff->id) }}" class="btn btn-sm btn-outline-success" title="Hiệu suất">
                                            <i class="fas fa-chart-line"></i>
                                        </a>
                                        <a href="{{ route('admin.staff.edit', $staff->id) }}" class="btn btn-sm btn-info" title="Sửa">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        @if($staff->id != auth('admin')->id())
                                            <button wire:confirm="Xóa nhân viên này sẽ mất các lịch sử giao việc liên quan. Bạn chắc chứ?" 
                                                    wire:click="delete({{ $staff->id }})" 
                                                    class="btn btn-sm btn-danger" title="Xóa">
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
                <div class="card-footer clearfix">
                    {{ $staffs->links() }}
                </div>
            </div>
        </div>
    </section>
</div>