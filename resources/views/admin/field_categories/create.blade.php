@extends('layouts.admin')

@section('title', 'Thêm mới Danh mục Lĩnh vực')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">@yield('title')</h1>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            Vui lòng kiểm tra lại các trường dữ liệu.
        </div>
    @endif

    <form action="{{ route('admin.field-categories.store') }}" method="POST" enctype="multipart/form-data">
        
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

                <x-form.slug
                    name="slug"
                    label="Đường dẫn (slug)"
                    :value="old('slug')"
                    source="#name"
                    table="field_categories"
                    field="slug"
                />
                
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
                
                <hr>
                
                <x-form.input name="order" label="Thứ tự" type="number" :value="0" />

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
            </div>
        </div>
    </div>
</div>

        <div class="mb-4 text-right">
            <button type="submit" class="btn btn-primary">Tạo mới</button>
            <a href="{{ route('admin.field-categories.index') }}" class="btn btn-secondary">Hủy</a>
        </div>
    </form>
</div>
@endsection