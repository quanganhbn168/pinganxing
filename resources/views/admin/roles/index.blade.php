@extends('layouts.admin')
@section('title', 'Quản lý Vai trò')
@section('content_header', 'Vai trò & Phân quyền')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Danh sách Vai trò</h3>
        <div class="card-tools">
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Thêm Vai trò
            </a>
        </div>
    </div>
    <div class="card-body p-0 table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th style="width: 50px">#</th>
                    <th>Tên Vai trò</th>
                    <th>Quyền hạn</th>
                    <th style="width: 150px" class="text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($roles as $role)
                <tr>
                    <td>{{ $role->id }}</td>
                    <td>
                        <strong>{{ $role->name }}</strong>
                        @if($role->name === 'Super Admin')
                            <span class="badge badge-danger ml-2">System</span>
                        @endif
                    </td>
                    <td>
                        @if($role->name === 'Super Admin')
                            <span class="text-success"><i class="fas fa-check-circle"></i> Toàn quyền hệ thống</span>
                        @else
                            <span class="badge badge-info">{{ $role->permissions->count() }} quyền</span>
                        @endif
                    </td>
                    <td class="text-right">
                        @if($role->name !== 'Super Admin')
                            <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa vai trò này?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        @else
                            <button class="btn btn-sm btn-secondary" disabled><i class="fas fa-lock"></i></button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection