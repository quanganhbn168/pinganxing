@extends('layouts.admin')
@section('title', 'Thêm lĩnh vực')
@section('content_header', 'Thêm lĩnh vực')

@section('content')
<form action="{{ route('admin.fields.store') }}" method="POST">
    @csrf
    <div class="card">
        <div class="card-body">
            <x-form.input name="name" label="Tên lĩnh vực" required />
            
            <x-form.slug
                name="slug"
                label="Đường dẫn (slug)"
                :value="old('slug')"
                source="#name"
                table="fields"
                field="slug"
                :current-id="null"
            />

            <x-category-select
                name="field_category_id" 
                label="Danh mục lĩnh vực" 
                :categories="$categories"
                placeholder="Danh mục gốc"
                :selected="old('category_id')"
            />

            <x-admin.form.media-input
                name="image_original_path"
                label="Ảnh đại diện"
                :multiple="false"
                :value="old('image_original_path')"
            />
            
            <x-form.textarea name="summary" label="Tóm tắt" />
            <x-form.ckeditor name="content" label="Nội dung" />
            
            <x-form.switch name="status" label="Hiển thị" :checked="true" />
            <x-form.switch name="is_featured" label="Nổi bật" :checked="false" />
            
            <x-form.input name="meta_title" label="Meta Title" />
            <x-form.textarea name="meta_description" label="Meta Description" />
            <x-form.input name="meta_keywords" label="Meta Keywords" />
        </div>
        <div class="card-footer">
            <button type="submit" name="save" class="btn btn-primary">Lưu</button>
            <button type="submit" name="save_new" class="btn btn-success">Lưu & Thêm mới</button>
            <a href="{{ route('admin.fields.index') }}" class="btn btn-secondary">Quay lại</a>
        </div>
    </div>
</form>
@endsection