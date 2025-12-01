@extends('layouts.admin')

@section('title', 'Chỉnh sửa: ' . $projectCategory->name)
@section('content_header', 'Chỉnh sửa: ' . $projectCategory->name)

@section('content')
<form action="{{ route('admin.project-categories.update', $projectCategory) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="row">
        {{-- Cột Nội dung chính --}}
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin chính</h6>
                </div>
                <div class="card-body">
                    <x-form.input name="name" label="Tên danh mục" :value="old('name', $projectCategory->name)" required />
                    <x-form.slug
                        name="slug"
                        label="Đường dẫn (slug)"
                        :value="old('slug', $projectCategory->slug)"
                        source="#name"
                        table="project_categories"
                        field="slug"
                        :current-id="$projectCategory->id"
                    />
                    <div class="form-group">
                        <label for="parent_id">Danh mục cha</label>
                        <select id="parent_id" name="parent_id" class="form-control @error('parent_id') is-invalid @enderror">
                            <option value="">— Là danh mục gốc —</option>
                            <x-form.category-tree 
                                :categories="$parentCategories"
                                :selectedId="old('parent_id', $projectCategory->parent_id)"
                                :excludeId="$projectCategory->id"
                            />
                        </select>
                        @error('parent_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    
                    
                    <x-form.textarea name="description" label="Mô tả" :value="old('description', $projectCategory->description)" rows="5" />

                    <x-form.ckeditor name="content" label="Nội dung chi tiết" :value="old('content', $projectCategory->content)" />
                </div>
            </div>
        </div>

        {{-- Cột Thông tin phụ --}}
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin phụ</h6>
                </div>
                <div class="card-body">
                    <x-form.switch name="status" label="Trạng thái" :checked="old('status', $projectCategory->status)" />
                    <x-form.switch name="is_home" label="Hiển thị trang chủ" :checked="old('is_home', $projectCategory->is_home)" />
                    <x-form.switch name="is_menu" label="Hiển thị menu" :checked="old('is_menu', $projectCategory->is_menu)" />
                    <x-form.switch name="is_footer" label="Hiển thị footer" :checked="old('is_footer', $projectCategory->is_footer)" />
                    
                    <hr>
                    
                    <x-form.input name="position" label="Vị trí" type="number" :value="old('position', $projectCategory->position)" />

                    <hr>

                    <x-admin.form.media-input
                        name="image_original_path"
                        label="Ảnh đại diện"
                        :multiple="false"
                        :value="$projectCategory->mainImage() ? $projectCategory->mainImage()->original_path : old('image_original_path')"
                    />

                    <x-admin.form.media-input
                        name="banner_original_path"
                        label="Banner"
                        :multiple="false"
                        :value="$projectCategory->bannerImage() ? $projectCategory->bannerImage()->original_path : old('banner_original_path')"
                    />

                    <hr>

                    <x-form.input name="meta_description" label="Meta Description" :value="old('meta_description', $projectCategory->meta_description)" />
                    <x-form.input name="meta_keywords" label="Meta Keywords" :value="old('meta_keywords', $projectCategory->meta_keywords)" />
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
                    <a href="{{ route('admin.project-categories.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection