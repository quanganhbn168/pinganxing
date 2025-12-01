@extends('layouts.admin')

@section('title', 'Cập nhật thương hiệu')
@section('content_header', 'Cập nhật thương hiệu')

@section('content')
<form action="{{ route('admin.brands.update', $brand) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="card">
        <div class="card-body">
            <x-form.input name="name" label="Tên thương hiệu" :value="$brand->name" required />
            <x-form.slug
                name="slug"
                label="Đường dẫn (slug)"
                :value="old('slug')"
                source="#name"              {{-- tự sinh từ input #title --}}
                table="brands"                {{-- bảng chính cần check --}}
                field="slug"                 {{-- cột slug của bảng --}}
                :current-id="$brand->id"           {{-- tạo mới: null --}}
            />
            <x-admin.form.media-input
                name="image_original_path"
                label="Ảnh đại diện"
                :multiple="false"
                :value="optional($brand->mainImage())->original_path"
            />
            <x-form.switch name="status" label="Hiển thị" :checked="$brand->status" />
        </div>
    </div>
    <div class="card-footer">
        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
        <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary">Quay lại</a>
    </div>
</form>
@endsection
