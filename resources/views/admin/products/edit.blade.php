{{-- resources/views/admin/products/edit.blade.php --}}
@extends('layouts.admin')

@section('title','Cập nhật sản phẩm')
@section('content_header_title','Cập nhật: ' . $product->name)

@section('content')
<form action="{{ route('admin.products.update', $product->id) }}" method="POST">
    @csrf
    @method('PUT')

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
        {{-- ================= CỘT TRÁI ================= --}}
        <div class="col-lg-8">
            <div class="card card-primary card-outline shadow mb-4">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">Thông tin chung</h3>
                </div>
                <div class="card-body">
                    <x-form.input
                        name="name"
                        label="Tên sản phẩm"
                        id="name"
                        :value="old('name', $product->name)"
                        required
                    />
                    
                    <div class="row">
                        <div class="col-md-6">
                             <x-auto-code
                                name="code"
                                label="Mã sản phẩm (SKU)"
                                :value="old('code', $product->code)"
                                source="#name"
                                :check-url="route('admin.products.validate_uniqueness')"
                                :current-id="$product->id"
                            />
                        </div>
                        <div class="col-md-6">
                             <x-form.slug
                                name="slug"
                                label="Đường dẫn (Slug)"
                                :value="old('slug', $product->slug)"
                                source="#name"
                                table="products"
                                field="slug"
                                :current-id="$product->id"
                            />
                        </div>
                    </div>

                    <x-form.textarea
                        name="description"
                        label="Mô tả ngắn"
                        rows="3"
                        :value="old('description', $product->description)"
                    />
                </div>
            </div>

            <div class="card card-outline card-primary shadow mb-4">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">Chi tiết sản phẩm</h3>
                </div>
                <div class="card-body">
                    <x-form.ckeditor
                        name="content"
                        label="Nội dung bài viết"
                        :value="old('content', $product->content)"
                    />
                    <div class="mt-4"></div>
                    <x-form.ckeditor
                        name="specifications"
                        label="Thông số kỹ thuật"
                        :value="old('specifications', $product->specifications)"
                    />
                </div>
            </div>
        </div>

        {{-- ================= CỘT PHẢI ================= --}}
        <div class="col-lg-4">
            {{-- Action --}}
            <div class="card shadow mb-4">
                <div class="card-header py-2">
                    <h6 class="m-0 font-weight-bold text-success">Xuất bản</h6>
                </div>
                <div class="card-body">
                    <x-form.switch
                        name="status"
                        label="Hiển thị sản phẩm"
                        :checked="old('status', (bool) $product->status)"
                    />
                    <hr>
                    <div class="d-flex flex-column">
                        <button type="submit" name="save" class="btn btn-primary mb-2">
                            <i class="fas fa-save mr-1"></i> Cập nhật
                        </button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-default">
                            <i class="fas fa-arrow-left mr-1"></i> Quay lại
                        </a>
                    </div>
                </div>
            </div>

            {{-- Category --}}
            <div class="card shadow mb-4">
                <div class="card-header py-2">
                    <h6 class="m-0 font-weight-bold text-primary">Phân loại</h6>
                </div>
                <div class="card-body">
                    <x-category-select
                        name="category_id"
                        label="Danh mục chính"
                        :categories="$categories"
                        :selected="old('category_id', $product->category_id)"
                        required
                    />
                </div>
            </div>

            {{-- Pricing --}}
            <div class="card shadow mb-4">
                <div class="card-header py-2">
                    <h6 class="m-0 font-weight-bold text-primary">Giá bán</h6>
                </div>
                <div class="card-body">
                    <x-form.money-input
                        name="price"
                        label="Giá niêm yết"
                        :value="old('price', $product->price)"
                        class="mb-3"
                    />
                    <x-form.money-input
                        name="price_discount"
                        label="Giá khuyến mãi"
                        :value="old('price_discount', $product->price_discount)"
                    />
                </div>
            </div>

            {{-- Media --}}
            <div class="card shadow mb-4">
                <div class="card-header py-2">
                    <h6 class="m-0 font-weight-bold text-primary">Hình ảnh</h6>
                </div>
                <div class="card-body">
                    <x-form.image-picker
                        name="image_original_path"
                        label="Ảnh đại diện"
                        :multiple="false"
                        :value="old('image_original_path', $product->image ?? optional($product->mainImage())->original_path)"
                    />
                    <hr>
                    <x-form.image-picker
                        name="gallery_original_paths"
                        label="Album ảnh (Gallery)"
                        :multiple="true"
                        :value="old('gallery_original_paths', $product->gallery->pluck('original_path'))"
                    />
                </div>
            </div>
        </div>
    </div>
</form>
@endsection