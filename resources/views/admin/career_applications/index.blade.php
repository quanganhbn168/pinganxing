@extends('layouts.admin')

@section('title', 'Hồ sơ ứng tuyển')
@section('content_header', 'Hồ sơ ứng tuyển')

@push('css')
<style>
    .custom-checkbox { width: 18px; height: 18px; cursor: pointer; vertical-align: middle; }
</style>
@endpush

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">×</button>
        </div>
    @endif

    {{-- BỘ LỌC --}}
    <div class="card collapsed-card">
        <div class="card-header">
            <h3 class="card-title">Bộ lọc tìm kiếm</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
            </div>
        </div>
        <div class="card-body" style="display: none;">
            <form method="GET" action="{{ route('admin.career-applications.index') }}" class="row">
                <div class="col-md-3">
                    <x-form.input name="keyword" label="Từ khóa" :value="request('keyword')" placeholder="Tên, SĐT, Email..." />
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Vị trí ứng tuyển</label>
                        <select name="career_id" class="form-control">
                            <option value="">-- Tất cả --</option>
                            @foreach($careers as $c)
                                <option value="{{ $c->id }}" {{ request('career_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Trạng thái</label>
                        <select name="status" class="form-control">
                            <option value="">-- Tất cả --</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                            <option value="reviewed" {{ request('status') == 'reviewed' ? 'selected' : '' }}>Đã xem</option>
                            <option value="contacted" {{ request('status') == 'contacted' ? 'selected' : '' }}>Đã liên hệ</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Từ chối</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-end mb-3">
                    <button class="btn btn-secondary btn-block"><i class="fas fa-search"></i> Lọc</button>
                </div>
            </form>
        </div>
    </div>

    {{-- DANH SÁCH --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Danh sách hồ sơ</h3>
            <div class="card-tools d-flex align-items-center">
                {{-- Component Bulk Action --}}
                <x-admin.bulk-action-bar model="career_application" />
            </div>
        </div>

        <div class="card-body p-0 table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead>
                <tr>
                    <th class="text-center" width="40">
                        <input type="checkbox" id="checkAll" class="custom-checkbox">
                    </th>
                    <th>Ứng viên</th>
                    <th>Vị trí ứng tuyển</th>
                    <th>CV</th>
                    <th>Ngày nộp</th>
                    <th class="text-center">Trạng thái</th>
                    <th class="text-center">Thao tác</th>
                </tr>
                </thead>
                <tbody>
                @forelse($applications as $item)
                    <tr>
                        <td class="text-center">
                            <input type="checkbox" class="custom-checkbox check-item" value="{{ $item->id }}">
                        </td>
                        <td>
                            <div class="font-weight-bold">{{ $item->name }}</div>
                            <div class="small text-muted">
                                <i class="fas fa-phone fa-xs mr-1"></i> {{ $item->phone }}
                                @if($item->email) <br><i class="fas fa-envelope fa-xs mr-1"></i> {{ $item->email }} @endif
                            </div>
                        </td>
                        <td>
                            @if($item->career)
                                <span class="badge badge-light border">{{ $item->career->name }}</span>
                            @else
                                <span class="text-danger small">(Tin đã xóa)</span>
                            @endif
                        </td>
                        <td>
                            @if($item->cv_path)
                                <a href="{{ asset('storage/' . $item->cv_path) }}" target="_blank" class="btn btn-xs btn-outline-primary">
                                    <i class="fas fa-file-pdf"></i> Xem CV
                                </a>
                            @else
                                <span class="text-muted small">Không có file</span>
                            @endif
                        </td>
                        <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-center">
                            @switch($item->status)
                                @case('pending') <span class="badge badge-warning">Chờ duyệt</span> @break
                                @case('reviewed') <span class="badge badge-info">Đã xem</span> @break
                                @case('contacted') <span class="badge badge-success">Đã liên hệ</span> @break
                                @case('rejected') <span class="badge badge-secondary">Từ chối</span> @break
                                @default <span class="badge badge-light">{{ $item->status }}</span>
                            @endswitch
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.career-applications.show', $item->id) }}" class="btn btn-sm btn-primary" title="Chi tiết">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-danger" 
                                    onclick="UniversalBulk.confirmOne({{ $item->id }}, 'delete')" title="Xóa">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">Chưa có hồ sơ nào.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($applications->hasPages())
            <div class="card-footer clearfix">{{ $applications->links() }}</div>
        @endif
    </div>
@endsection