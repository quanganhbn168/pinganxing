{{-- resources/views/admin/projects/edit.blade.php --}}
@extends('layouts.admin')

@section('title','Chỉnh sửa dự án')
@section('content_header_title','Chỉnh sửa dự án')

@section('content')
<form action="{{ route('admin.projects.update', $project->id) }}" method="POST">
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

    {{-- Thông tin chính (1 cột) --}}
    <div class="card shadow mb-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Thông tin dự án</h6>
        </div>
        <div class="card-body">

            {{-- Tên dự án --}}
            <x-form.input
                name="name"
                id="name"
                label="Tên dự án"
                :value="old('name', $project->name)"
                required
            />

            {{-- Slug (tạo từ tên + check unique nếu có route chung) --}}
            <x-form.slug
                name="slug"
                label="Đường dẫn (slug)"
                :value="old('slug', $project->slug)"
                source="#name"
                table="projects"
                field="slug"
                :current-id="$project->id"
            />

            {{-- Danh mục dự án --}}
            <x-form.select
                name="project_category_id"
                label="Danh mục dự án"
                :options="$categories ?? []"
                :selected="old('project_category_id', $project->project_category_id)"
                placeholder="— Chọn danh mục —"
                required
            />

            {{-- Thông tin chi tiết --}}
            <div class="row">
                <div class="col-md-6">
                    <x-form.input
                        name="investor"
                        label="Chủ đầu tư"
                        :value="old('investor', $project->investor)"
                    />
                </div>
                <div class="col-md-6">
                    <x-form.input
                        name="address"
                        label="Địa chỉ"
                        :value="old('address', $project->address)"
                    />
                </div>
                <div class="col-md-6">
                    <x-form.input
                        name="year"
                        label="Năm thực hiện"
                        type="number"
                        :value="old('year', $project->year)"
                    />
                </div>
                <div class="col-md-6">
                    {{-- Giá trị gói thầu (tiền) --}}
                    <x-form.money-input
                        name="value"
                        label="Giá trị gói thầu"
                        :value="old('value', $project->value)"
                    />
                </div>
            </div>

            {{-- Mô tả & Nội dung --}}
            <x-form.textarea
                name="description"
                label="Mô tả ngắn"
                :value="old('description', $project->description)"
            />
            <x-form.ckeditor
                name="content"
                label="Nội dung chi tiết"
                :value="old('content', $project->content)"
            />
        </div>
    </div>

    {{-- Hình ảnh & hiển thị (1 cột) --}}
    <div class="card shadow mb-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Hình ảnh & Hiển thị</h6>
        </div>
        <div class="card-body">
            {{-- Trạng thái --}}
            <x-form.switch
                name="status"
                label="Hiển thị"
                :checked="old('status', (bool) $project->status)"
            />

            <hr>

            {{-- Ảnh đại diện (single) --}}
            <x-admin.form.media-input
                name="image_original_path"
                label="Ảnh đại diện"
                :multiple="false"
                :value="old('image_original_path', optional($project->mainImage())->original_path)"
                help="Chọn 1 ảnh làm đại diện. (Media picker → tab Ảnh có sẵn)"
            />

            {{-- Ảnh banner (single) --}}
            <x-admin.form.media-input
                name="banner_original_path"
                label="Ảnh banner (nếu có)"
                :multiple="false"
                :value="old('banner_original_path', optional($project->bannerImage())->original_path)"
            />

            {{-- Gallery (multiple → JSON) --}}
            <x-admin.form.media-input
                name="gallery_original_paths"
                label="Thư viện ảnh (Gallery)"
                :multiple="true"
                :value="$project->gallery?->pluck('original_path')"
                help="Chọn nhiều ảnh, trường sẽ lưu JSON mảng đường dẫn."
            />
        </div>
    </div>

    {{-- Footer --}}
    <div class="card shadow">
        <div class="card-footer text-right">
            <button type="submit" name="save" class="btn btn-primary">
                <i class="fas fa-save mr-1"></i> Lưu
            </button>
            <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Quay lại
            </a>
        </div>
    </div>
</form>
@endsection
