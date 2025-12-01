{{-- resources/views/admin/service_categories/create.blade.php --}}

@extends('layouts.admin')
@section('title', 'Thêm danh mục dịch vụ')
@section('content_header', 'Thêm danh mục dịch vụ')
@section('content')
<div class="card">
    <form action="{{ route('admin.service_categories.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <x-form.input name="name" label="Tên danh mục" :value="old('name')" />
            <x-form.textarea name="description" label="Mô tả" :value="old('description')" />
            <x-form.ckeditor name="content" label="Nội dung" :value="old('content')" />
            <x-form.select-best
                name="parent_id"
                label="Danh mục"
                :collection="$categories"
                :selected="old('parent_id', 0)"
            />

            <x-form.image-input name="image" label="Ảnh đại diện" />
            <x-form.image-input name="banner" label="Banner (tuỳ chọn)" />
            <x-form.switch name="status" label="Trạng thái" :checked="old('status', true)" />
            <x-form.switch name="is_menu" label="Menu" :checked="old('is_menu', true)" />
            <x-form.switch name="is_footer" label="Footer" :checked="old('is_footer', true)" />
            <x-form.switch name="is_home" label="Hiện trang chủ" :checked="old('is_home', true)" />
        </div>
        <div class="card-footer">
            <button type="submit" name="action" value="save" class="btn btn-primary">Lưu</button>
            <button type="submit" name="action" value="save_new" class="btn btn-secondary">Lưu và thêm mới</button>
            <a href="{{ route('admin.service_categories.index') }}" class="btn btn-outline-dark">Quay lại</a>
        </div>
    </form>
</div>
@endsection

