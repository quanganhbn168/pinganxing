@extends('layouts.admin')

@section('title', 'Chi tiết hồ sơ: ' . $application->name)
@section('content_header', 'Chi tiết hồ sơ')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title mb-0">Thông tin ứng viên</h3>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong class="text-muted">Họ và tên:</strong>
                        <p class="h5 mt-1">{{ $application->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong class="text-muted">Vị trí ứng tuyển:</strong>
                        <p class="h5 mt-1 text-primary">
                            {{ $application->career->name ?? 'Vị trí không tồn tại' }}
                        </p>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong class="text-muted">Số điện thoại:</strong>
                        <p>{{ $application->phone }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong class="text-muted">Email:</strong>
                        <p>{{ $application->email ?? 'Không cung cấp' }}</p>
                    </div>
                </div>

                <div class="form-group">
                    <strong class="text-muted">Lời nhắn:</strong>
                    <div class="p-3 bg-light rounded mt-2">
                        {{ $application->message ?? 'Không có lời nhắn.' }}
                    </div>
                </div>

                <div class="form-group mt-4">
                    <strong class="text-muted d-block mb-2">File CV đính kèm:</strong>
                    @if($application->cv_path)
                        <div class="embed-responsive embed-responsive-16by9" style="height: 500px; border: 1px solid #ddd;">
                            <iframe class="embed-responsive-item" src="{{ asset('storage/' . $application->cv_path) }}"></iframe>
                        </div>
                        <div class="mt-2 text-right">
                            <a href="{{ asset('storage/' . $application->cv_path) }}" target="_blank" class="btn btn-info btn-sm">
                                <i class="fas fa-download mr-1"></i> Tải về CV
                            </a>
                        </div>
                    @else
                        <p class="text-danger">Không tìm thấy file CV.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Hành động</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.career-applications.update', $application->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label>Cập nhật trạng thái</label>
                        <select name="status" class="form-control">
                            <option value="pending" {{ $application->status == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                            <option value="reviewed" {{ $application->status == 'reviewed' ? 'selected' : '' }}>Đã xem</option>
                            <option value="contacted" {{ $application->status == 'contacted' ? 'selected' : '' }}>Đã liên hệ</option>
                            <option value="rejected" {{ $application->status == 'rejected' ? 'selected' : '' }}>Từ chối</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success btn-block">
                        <i class="fas fa-save mr-1"></i> Cập nhật
                    </button>
                </form>

                <hr>
                <div class="text-muted small">
                    <p><i class="far fa-clock"></i> Nộp lúc: {{ $application->created_at->format('H:i d/m/Y') }}</p>
                </div>

                <form action="{{ route('admin.career-applications.destroy', $application->id) }}" method="POST" class="mt-3" onsubmit="return confirm('Xóa vĩnh viễn hồ sơ này?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger btn-block">
                        <i class="fas fa-trash-alt mr-1"></i> Xóa hồ sơ
                    </button>
                </form>
            </div>
        </div>
        <a href="{{ route('admin.career-applications.index') }}" class="btn btn-default btn-block mt-3">
            <i class="fas fa-arrow-left mr-1"></i> Quay lại danh sách
        </a>
    </div>
</div>
@endsection