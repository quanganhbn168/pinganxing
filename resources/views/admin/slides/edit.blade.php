{{-- resources/views/admin/slides/edit.blade.php --}}
@extends('layouts.admin')

@section('title','Chỉnh sửa slide')
@section('content_header_title','Chỉnh sửa slide')

@section('content')
<form action="{{ route('admin.slides.update', $slide->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    {{-- Lỗi tổng --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Đã có lỗi xảy ra:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Thông tin slide (1 cột) --}}
    <div class="card shadow mb-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Thông tin slide</h6>
        </div>
        <div class="card-body">
            
            {{-- [MỚI] Chọn Loại Slide --}}
            <div class="form-group">
                <label for="type">Vị trí hiển thị <span class="text-danger">*</span></label>
                <select name="type" id="type" class="form-control @error('type') is-invalid @enderror">
                    @foreach($types as $type)
                        {{-- Logic kiểm tra: Ưu tiên old(), nếu không có thì lấy từ DB ($slide->type->value) --}}
                        <option value="{{ $type->value }}" 
                            {{ (old('type') ?? $slide->type->value) == $type->value ? 'selected' : '' }}>
                            {{ $type->label() }}
                        </option>
                    @endforeach
                </select>
                @error('type')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            {{-- Tiêu đề --}}
            <x-form.input
                type="text"
                name="title"
                label="Tiêu đề slide"
                :value="old('title', $slide->title)"
            />

            {{-- Link --}}
            <x-form.input
                type="text"
                name="link"
                label="Link bài / Link chuyển hướng"
                :value="old('link', $slide->link)"
            />

            {{-- Thứ tự hiển thị --}}
            <x-form.input
                type="number"
                name="position"
                label="Thứ tự hiển thị"
                :value="old('position', $slide->position ?? 0)"
            />

            {{-- Trạng thái --}}
            <x-form.switch
                name="status"
                label="Hiển thị"
                :checked="old('status', (bool) $slide->status)"
            />

            <hr>

            {{-- Ảnh slide --}}
            <x-admin.form.media-input
                name="image_original_path"
                label="Ảnh slide (chuẩn: 1920×600px)"
                :multiple="false"
                :value="old('image_original_path', optional($slide->mainImage())->original_path)"
            />
        </div>
    </div>

    {{-- Footer --}}
    <div class="card shadow">
        <div class="card-footer text-right">
            <button type="submit" name="save" class="btn btn-primary">
                <i class="fas fa-save mr-1"></i> Lưu
            </button>
            <a href="{{ route('admin.slides.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Quay lại
            </a>
        </div>
    </div>
</form>
@endsection