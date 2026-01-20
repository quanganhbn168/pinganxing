{{-- resources/views/admin/products/create.blade.php --}}
@extends('layouts.admin')

@section('title','Thêm sản phẩm mới')
@section('content_header_title','Thêm sản phẩm')

@section('content')
<form action="{{ route('admin.products.store') }}" method="POST">
    @csrf

    {{-- Hiển thị lỗi validate chung --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Vui lòng kiểm tra lại dữ liệu:</strong>
            <ul class="mb-0 pl-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        {{-- ================= CỘT TRÁI: NỘI DUNG CHÍNH (70%) ================= --}}
        <div class="col-lg-8">
            {{-- 1. Thông tin cơ bản --}}
            <div class="card card-primary card-outline shadow mb-4">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">Thông tin chung</h3>
                </div>
                <div class="card-body">
                    <x-form.input
                        name="name"
                        label="Tên sản phẩm"
                        id="name"
                        :value="old('name')"
                        placeholder="Nhập tên sản phẩm..."
                        required
                    />
                    
                    <div class="row">
                        <div class="col-md-6">
                             <x-auto-code
                                name="code"
                                label="Mã sản phẩm (SKU)"
                                :value="old('code')"
                                source="#name"
                                :check-url="route('admin.products.validate_uniqueness')"
                                :current-id="null"
                            />
                        </div>
                        <div class="col-md-6">
                             <x-form.slug
                                name="slug"
                                label="Đường dẫn (Slug)"
                                :value="old('slug')"
                                source="#name"
                                table="products"
                                field="slug"
                            />
                        </div>
                    </div>

                    <x-form.textarea
                        name="description"
                        label="Mô tả ngắn"
                        rows="3"
                        :value="old('description')"
                        placeholder="Mô tả ngắn gọn về sản phẩm (SEO)..."
                    />
                </div>
            </div>

            {{-- 2. Nội dung chi tiết --}}
            <div class="card card-outline card-primary shadow mb-4">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">Chi tiết sản phẩm</h3>
                </div>
                <div class="card-body">
                    <x-form.ckeditor
                        name="content"
                        label="Nội dung bài viết"
                        :value="old('content')"
                    />
                    <div class="mt-4"></div>
                    <x-form.ckeditor
                        name="specifications"
                        label="Thông số kỹ thuật"
                        :value="old('specifications')"
                    />
                </div>
            </div>
        </div>

        {{-- ================= CỘT PHẢI: THIẾT LẬP & MEDIA (30%) ================= --}}
        <div class="col-lg-4">
            
            {{-- 1. Hành động & Trạng thái (Đưa lên đầu để dễ bấm) --}}
            <div class="card shadow mb-4">
                <div class="card-header py-2">
                    <h6 class="m-0 font-weight-bold text-success">Xuất bản</h6>
                </div>
                <div class="card-body">
                    <x-form.switch
                        name="status"
                        label="Hiển thị sản phẩm"
                        :checked="true"
                    />
                    <hr>
                    <div class="d-flex flex-column">
                        <button type="submit" name="save" class="btn btn-primary mb-2">
                            <i class="fas fa-save mr-1"></i> Lưu sản phẩm
                        </button>
                        <button type="submit" name="save_new" class="btn btn-success mb-2">
                            <i class="fas fa-plus mr-1"></i> Lưu & Thêm mới
                        </button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-default">
                            <i class="fas fa-arrow-left mr-1"></i> Quay lại
                        </a>
                    </div>
                </div>
            </div>

            {{-- 2. Phân loại (Dùng Component xịn vừa làm) --}}
            <div class="card shadow mb-4">
                <div class="card-header py-2">
                    <h6 class="m-0 font-weight-bold text-primary">Phân loại</h6>
                </div>
                <div class="card-body">
                    <x-category-select
                        name="category_id"
                        label="Danh mục chính"
                        :categories="$categories"
                        :selected="old('category_id')"
                        required
                    />
                </div>
            </div>

            {{-- 3. Giá bán --}}
            <div class="card shadow mb-4">
                <div class="card-header py-2">
                    <h6 class="m-0 font-weight-bold text-primary">Giá bán</h6>
                </div>
                <div class="card-body">
                    <x-form.money-input
                        name="price"
                        label="Giá niêm yết"
                        :value="old('price')"
                        class="mb-3"
                    />
                    <x-form.money-input
                        name="price_discount"
                        label="Giá khuyến mãi"
                        :value="old('price_discount')"
                        help="Để trống nếu không giảm giá"
                    />
                </div>
            </div>

            {{-- 4. Hình ảnh --}}
            <div class="card shadow mb-4">
                <div class="card-header py-2">
                    <h6 class="m-0 font-weight-bold text-primary">Hình ảnh</h6>
                </div>
                <div class="card-body">
                    <x-form.image-picker
                        name="image_original_path"
                        label="Ảnh đại diện"
                        :multiple="false"
                        :value="old('image_original_path')"
                    />
                    <hr>
                    <x-form.image-picker
                        name="gallery_original_paths"
                        label="Album ảnh (Gallery)"
                        :multiple="true"
                        :value="old('gallery_original_paths')"
                    />
                </div>
            </div>
        </div>
    </div>
</form>
@endsection