@extends('layouts.admin')

@section('title', 'Thêm bài viết')

@section('content_header', 'Thêm bài viết')

@section('content')

<form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">

    @csrf

    <div class="card">

        <div class="card-body">

            <x-form.input name="title" label="Tiêu đề" required />
            <x-form.slug

                name="slug"

                label="Đường dẫn (slug)"

                :value="old('slug')"

                source="#title"

                table="posts"

                field="slug"

            />
            <x-form.select 

                name="post_category_id" 

                label="Danh mục bài viết" 

                :options="$categories" 

                required 

            />

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

            <x-form.textarea name="description" label="Mô tả ngắn" />

            <x-form.ckeditor name="content" label="Nội dung" />

            <x-form.switch name="is_featured" label="Nổi bật" :checked="false" />

            <x-form.switch name="status" label="Hiển thị" :checked="true" />

        </div>

        <div class="card-footer">

            <button type="submit" name="save" class="btn btn-primary">Lưu</button>

            <button type="submit" name="save_new" class="btn btn-success">Lưu & Thêm mới</button>

            <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary">Quay lại</a>

        </div>

    </div>

</form>

@endsection