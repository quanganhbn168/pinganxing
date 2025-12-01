@extends('layouts.admin')

@section('title', 'Chỉnh sửa lĩnh vực')
@section('content_header_title', 'Chỉnh sửa lĩnh vực')

@section('content')
<form action="{{ route('admin.fields.update', $field) }}" method="POST" enctype="multipart/form-data">
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

    <div class="card">
        <div class="card-body">
            
            {{-- Tên lĩnh vực --}}
            <x-form.input
                name="name"
                id="name"
                label="Tên lĩnh vực"
                :value="old('name', $field->name)"
                required
            />

            {{-- Slug --}}
            <x-form.slug
                name="slug"
                label="Đường dẫn (slug)"
                :value="old('slug', $field->slug)"
                source="#name"
                table="fields"
                field="slug"
                :current-id="$field->id"
            />

            {{-- Danh mục --}}
            <x-category-select
                name="field_category_id" 
                label="Danh mục lĩnh vực" 
                :categories="$categories"
                placeholder="Danh mục gốc"
                :selected="old('category_id', $field->field_category_id)"
            />

            {{-- Ảnh đại diện --}}
            <x-admin.form.media-input
                name="image_original_path"
                label="Ảnh đại diện"
                :multiple="false"
                :value="old('image_original_path', optional($field->mainImage())->original_path)"
            />

            {{-- Tóm tắt --}}
            <x-form.textarea
                name="summary"
                label="Tóm tắt"
                :value="old('summary', $field->summary)"
            />

            {{-- Nội dung --}}
            <x-form.ckeditor
                name="content"
                label="Nội dung"
                :value="old('content', $field->content)"
            />

            {{-- Trạng thái --}}
            <x-form.switch
                name="status"
                label="Hiển thị"
                :checked="old('status', (bool) $field->status)"
            />

            {{-- Nổi bật --}}
            <x-form.switch
                name="is_featured"
                label="Nổi bật"
                :checked="old('is_featured', (bool) $field->is_featured)"
            />

            {{-- SEO --}}
            <x-form.input
                name="meta_title"
                label="Meta Title"
                :value="old('meta_title', $field->meta_title)"
            />

            <x-form.textarea
                name="meta_description"
                label="Meta Description"
                :value="old('meta_description', $field->meta_description)"
            />

            <x-form.input
                name="meta_keywords"
                label="Meta Keywords"
                :value="old('meta_keywords', $field->meta_keywords)"
            />

        </div>

        {{-- Footer --}}
        <div class="card-footer text-right">
            <button type="submit" name="save" class="btn btn-primary">
                <i class="fas fa-save mr-1"></i> Lưu
            </button>
            <a href="{{ route('admin.fields.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Quay lại
            </a>
        </div>
    </div>
</form>
@endsection
