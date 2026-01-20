@extends('layouts.admin')
@section('title', 'Thêm danh mục sản phẩm')
@section('content_header', 'Thêm danh mục sản phẩm')
@section('content')
<form action="{{ route('admin.categories.store') }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin chính</h6>
                </div>
                <div class="card-body">
                    <x-form.input name="name" label="Tên danh mục" required />
                    <x-form.slug
                        name="slug"
                        label="Đường dẫn (slug)"
                        :value="old('slug')"
                        source="#name"
                        table="categories"
                        field="slug"
                    />
                    <x-category-select 
                        :categories="$categories" 
                        name="parent_id" 
                        label="Danh mục gốc"
                        :disable-parents="false"
                        placeholder="-- Danh mục gốc --"
                    />
                    <x-form.textarea name="description" label="Mô tả" :value="old('description')" />
                    <x-form.ckeditor name="content" label="Nội dung" :value="old('content')" />
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin phụ</h6>
                </div>
                <div class="card-body">
                    <x-form.switch name="status" label="Trạng thái" :checked="true" />
                    <x-form.switch name="is_home" label="Hiển thị trang chủ" :checked="false" />
                    <x-form.switch name="is_menu" label="Hiển thị menu" :checked="false" />
                    <x-form.switch name="is_footer" label="Hiển thị footer" :checked="false" />
                    <hr>
                    <x-form.input name="position" label="Vị trí" type="number" :value="0" />
                    <hr>
                    <x-form.image-picker
                        name="image_original_path"
                        label="Ảnh đại diện"
                        :multiple="false"
                        :value="old('image_original_path')"
                    />
                    <x-form.image-picker
                        name="banner_original_path"
                        label="Banner"
                        :multiple="false"
                        :value="old('banner_original_path')"
                    />
                    <hr>
                    <x-form.textarea name="meta_description" label="Meta Description" />
                    <x-form.input name="meta_keywords" label="Meta Keywords" />
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-footer text-right">
                    <button type="submit" name="save" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Lưu
                    </button>
                    <button type="submit" name="save_new" class="btn btn-success">
                        <i class="fas fa-plus mr-1"></i> Lưu & Thêm mới
                    </button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection