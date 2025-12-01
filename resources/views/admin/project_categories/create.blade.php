@extends('layouts.admin')

@section('title', 'Thêm danh mục dự án')
@section('content_header', 'Thêm danh mục dự án')

@section('content')
<form action="{{ route('admin.project-categories.store') }}" method="POST">
    @csrf
    <div class="row">
        {{-- Cột Nội dung chính --}}
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
                        table="project_categories"
                        field="slug"
                    />
                    <div class="form-group">
                        <label for="parent_id">Danh mục cha</label>
                        <select id="parent_id" name="parent_id" class="form-control @error('parent_id') is-invalid @enderror">
                            <option value="">— Là danh mục gốc —</option>
                            <x-form.category-tree 
                                :categories="$parentCategories"
                                :selectedId="old('parent_id')"
                            />
                        </select>
                        @error('parent_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <x-form.textarea name="description" label="Mô tả" rows="5" />

                    <x-form.ckeditor name="content" label="Nội dung chi tiết" />
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
                    <x-form.switch name="status" label="Trạng thái" :checked="true" />
                    <x-form.switch name="is_home" label="Hiển thị trang chủ" :checked="false" />
                    <x-form.switch name="is_menu" label="Hiển thị menu" :checked="false" />
                    <x-form.switch name="is_footer" label="Hiển thị footer" :checked="false" />
                    
                    <hr>
                    
                    <x-form.input name="position" label="Vị trí" type="number" :value="0" />

                    <hr>

                    <x-admin.form.media-input
                        name="image_original_path"
                        label="Ảnh đại diện"
                        :multiple="false"
                        :value="old('image_original_path')"
                    />

                    <x-admin.form.media-input
                        name="banner_original_path"
                        label="Banner"
                        :multiple="false"
                        :value="old('banner_original_path')"
                    />

                    <hr>

                    <x-form.input name="meta_description" label="Meta Description" />
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
                    <a href="{{ route('admin.project-categories.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection