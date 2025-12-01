@extends('layouts.admin')
@section('title', 'Chỉnh sửa dịch vụ')
@section('content_header', 'Chỉnh sửa dịch vụ')

@section('content')
<div class="card">
    <form action="{{ route('admin.services.update', $service) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="card-body">
            <x-form.input type="text" name="name" label="Tên dịch vụ" :value="old('name', $service->name)" required />
            <x-form.select-best name="service_category_id" label="Danh mục" :collection="$categories" :selected="old('service_category_id', $service->service_category_id)" placeholder="-- Chọn danh mục --" required />
            <x-form.image-input name="image" label="Ảnh đại diện" :value="$service->image" />
            <x-form.image-multi-input
                name="gallery"
                label="Ảnh chi tiết"
                :images="$images"
            />

            <x-form.image-input name="banner" label="Banner (tuỳ chọn)" :value="$service->banner" />
            <x-form.ckeditor name="description" label="Mô tả ngắn" :value="old('description', $service->description)" />
            <x-form.ckeditor name="content" label="Nội dung chi tiết" :value="old('content', $service->content)" />
            <x-form.switch name="status" label="Trạng thái" :checked="old('status', $service->status)" />
        </div>
        <div class="card-footer">
            <button type="submit" name="action" value="update" class="btn btn-primary">Cập nhật</button>
            <a href="{{ route('admin.services.index') }}" class="btn btn-outline-dark">Quay lại</a>
        </div>
    </form>
</div>
@endsection
