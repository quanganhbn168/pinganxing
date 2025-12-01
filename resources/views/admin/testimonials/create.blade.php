@extends('layouts.admin')

@section('title','Thêm testimonial')
@section('content_header_title','Thêm testimonial')

@section('content')
<form action="{{ route('admin.testimonials.store') }}" method="POST">
    @csrf

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Đã có lỗi xảy ra:</strong>
            <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- Thông tin (1 cột) --}}
    <div class="card shadow mb-4">
        <div class="card-header"><h6 class="m-0 font-weight-bold text-primary">Thông tin testimonial</h6></div>
        <div class="card-body">
            <x-form.input name="name" label="Tên khách hàng" :value="old('name')" required />

            <x-form.input name="position" label="Thứ tự" type="number" :value="old('position', 0)" />

            <x-form.ckeditor name="content" label="Nội dung" :value="old('content')" />

            <x-form.switch name="status" label="Hiển thị" :checked="old('status', true)" />
        </div>
    </div>

    {{-- Ảnh (1 cột) --}}
    <div class="card shadow mb-4">
        <div class="card-header"><h6 class="m-0 font-weight-bold text-primary">Ảnh đại diện</h6></div>
        <div class="card-body">
            <x-admin.form.media-input
                name="image_original_path"
                label="Ảnh khách hàng"
                :multiple="false"
                :value="old('image_original_path')"
                help="Kích thước gợi ý 500×500px. Media sẽ được convert WebP + thumbnail."
            />
        </div>
    </div>

    {{-- Footer --}}
    <div class="card shadow">
        <div class="card-footer text-right">
            <button type="submit" name="save" class="btn btn-primary">
                <i class="fas fa-save mr-1"></i> Lưu
            </button>
            <button type="submit" name="save_new" class="btn btn-success">
                <i class="fas fa-plus mr-1"></i> Lưu & Thêm mới
            </button>
            <a href="{{ route('admin.testimonials.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Quay lại
            </a>
        </div>
    </div>
</form>
@endsection
