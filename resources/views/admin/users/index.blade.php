@extends('layouts.admin')

@section('title', 'Quản lý Người dùng')

@push('css')
<style>
    .custom-checkbox { width: 18px; height: 18px; cursor: pointer; vertical-align: middle; }
    .table td { vertical-align: middle !important; }
</style>
@endpush

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Danh sách Người dùng</h3>
            <div class="card-tools d-flex align-items-center">
                
                {{-- [WOW] Component Bulk Action --}}
                <x-admin.bulk-action-bar model="user" />

                <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus-circle mr-1"></i> Thêm mới
                </a>
            </div>
        </div>

        <div class="card-body">
            {{-- Search Form --}}
            <form action="{{ route('admin.users.index') }}" method="GET" class="mb-3">
                <div class="input-group" style="max-width: 400px;">
                    <input type="text" class="form-control" name="search" placeholder="Tìm tên, email, sđt..." value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th class="text-center" width="40">
                                <input type="checkbox" id="checkAll" class="custom-checkbox">
                            </th>
                            <th width="50">#</th>
                            <th>Họ và tên</th>
                            <th>Liên hệ</th>
                            <th>Vai trò</th>
                            <th width="150">Ngày tham gia</th>
                            <th width="100" class="text-right">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $key => $user)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" class="custom-checkbox check-item" value="{{ $user->id }}">
                                </td>
                                <td>{{ $users->firstItem() + $key }}</td>
                                <td>
                                    <div class="font-weight-bold">{{ $user->name }}</div>
                                    <small class="text-muted">ID: {{ $user->id }}</small>
                                </td>
                                <td>
                                    <div><i class="fas fa-phone fa-xs mr-1 text-muted"></i> {{ $user->phone }}</div>
                                    @if($user->email)
                                        <div class="small text-muted"><i class="fas fa-envelope fa-xs mr-1"></i> {{ $user->email }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if($user->roles->isNotEmpty())
                                        @foreach($user->roles as $role)
                                            <span class="badge badge-info">{{ $role->name }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted small">--</span>
                                    @endif
                                </td>
                                <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                <td class="text-right">
                                    <div class="btn-group">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                onclick="UniversalBulk.confirmOne({{ $user->id }}, 'delete')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Không tìm thấy người dùng nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $users->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@endsection