@extends('layouts.admin')
@section('title', 'Chỉnh sửa: ' . $category->name)
@section('content_header', 'Chỉnh sửa: ' . $category->name)
@section('content')
<form action="{{ route('admin.categories.update', $category) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin chính</h6>
                </div>
                <div class="card-body">
                    <x-form.input name="name" label="Tên danh mục" :value="old('name', $category->name)" required />
                    <x-form.slug
                        name="slug"
                        label="Đường dẫn (slug)"
                        :value="old('slug', $category->slug)"
                        source="#name"
                        table="categories"
                        field="slug"
                        :current-id="$category->id"
                    />
                    <x-category-select 
                        :categories="$categories" 
                        :selected="$category->parent_id" 
                        name="parent_id" 
                        label="Danh mục cha"
                        :disable-parents="false"
                        :ignore-id="$category->id"
                    />
                    <x-form.textarea name="description" label="Mô tả" :value="old('description', $category->description)" />
                    <x-form.ckeditor name="content" label="Nội dung" :value="old('content', $category->content)" />
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin phụ</h6>
                </div>
                <div class="card-body">
                    <x-form.switch name="status" label="Trạng thái" :checked="old('status', $category->status)" />
                    <x-form.switch name="is_home" label="Hiển thị trang chủ" :checked="old('is_home', $category->is_home)" />
                    <x-form.switch name="is_menu" label="Hiển thị menu" :checked="old('is_menu', $category->is_menu)" />
                    <x-form.switch name="is_footer" label="Hiển thị footer" :checked="old('is_footer', $category->is_footer)" />
                    <hr>
                    <x-form.input name="position" label="Vị trí" type="number" :value="old('position', $category->position)" />
                    <hr>
                    <x-form.image-picker
                        name="image_original_path"
                        label="Ảnh đại diện"
                        :multiple="false"
                        :value="old('image_original_path', $category->image ?? optional($category->mainImage())->original_path)"
                    />
                    <x-form.image-picker
                        name="banner_original_path"
                        label="Banner"
                        :multiple="false"
                        :value="old('banner_original_path', $category->banner ?? optional($category->bannerImage())->original_path)"
                    />
                    <hr>
                    <x-form.textarea name="meta_description" label="Meta Description" :value="old('meta_description', $category->meta_description)" />
                    <x-form.input name="meta_keywords" label="Meta Keywords" :value="old('meta_keywords', $category->meta_keywords)" />
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Cập nhật
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