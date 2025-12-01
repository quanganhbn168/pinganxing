@extends('layouts.admin')

@section('title', 'Cập nhật bài viết')

@section('content_header', 'Cập nhật bài viết')



@section('content')

<form action="{{ route('admin.posts.update', $post) }}" method="POST" enctype="multipart/form-data">

    @csrf

    @method('PUT')

    <div class="card">

        <div class="card-body">

            <x-form.input name="title" label="Tiêu đề" :value="$post->title" required />

            <x-form.slug

                name="slug"

                        label="Đường dẫn (slug)"

                        :value="old('slug', $post->slug)"

                        source="#title"

                        table="posts"

                        field="slug"

                        :current-id="$post->id"

            />

            <x-form.select 

                name="post_category_id" 

                label="Danh mục bài viết" 

                :options="$categories"

                :selected="$post->post_category_id"

                :required="true"

            />

            <x-admin.form.media-input

                name="image_original_path"

                label="Ảnh đại diện"

                :multiple="false"

                :value="optional($post->mainImage())->original_path"

            />

            <x-admin.form.media-input

                name="banner_original_path"

                label="Banner"

                :multiple="false"

                :value="optional($post->bannerImage())->original_path"

            />

            

            <x-form.textarea name="description" label="Mô tả ngắn" :value="$post->description" />

            <x-form.ckeditor name="content" label="Nội dung" :value="$post->content" />



            <x-form.switch name="is_featured" label="Nổi bật" :checked="$post->is_featured" />

            <x-form.switch name="status" label="Hiển thị" :checked="$post->status" />

        </div>

        <div class="card-footer">

            <button type="submit" name="action" value="update" class="btn btn-primary">Cập nhật</button>

            <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary">Quay lại</a>

        </div>

    </div>

</form>

@endsection

