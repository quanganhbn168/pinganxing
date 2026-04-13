@extends('layouts.master')
@section('title','Tất cả sản phẩm')
@push('css')
<link rel="stylesheet" href="{{asset('css/product.css')}}">

@endpush
@section("content")
{{-- Banner đầu trang --}}
<div class="collection-banner">
    <img src="https://placehold.co/1920x300/1a1a1a/c30000?text=Sản+Phẩm+KPTech" alt="Tất cả sản phẩm">
</div>

<div class="container mt-4">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="breadcrumb-wrapper">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Trang chủ</a></li>
            <li class="breadcrumb-item active" aria-current="page">Sản phẩm</li>
        </ol>
    </nav>

    <div class="row">
        {{-- ======================================================= --}}
        {{-- CỘT BÊN TRÁI: BỘ LỌC (SIDEBAR) --}}
        {{-- ======================================================= --}}
        <aside class="col-lg-3 d-none d-lg-block">
            <div class="sidebar-filter">
                <div class="sidebar-widget">
                    <h3 class="widget-title">
                        Danh mục sản phẩm
                    </h3>
                    <div class="widget-content">
                        <ul class="category-list">
                            {{-- 
                              Lặp qua biến $productCategories để hiển thị danh mục.
                              Cấu trúc này hỗ trợ menu đa cấp (cha-con).
                            --}}
                            @foreach($productCategories as $category)
                                <li>
                                    <a href="{{ route('products.by_category', $category->slug) }}" class="{{-- request()->is('danh-muc/' . $category->slug) ? 'active' : '' --}}">
                                        {{ $category->name }}
                                    </a>
                                    @if($category->children->isNotEmpty())
                                        <ul class="subcategory-list">
                                            @foreach($category->children as $childCategory)
                                                <li>
                                                    <a href="{{ route('products.by_category', $childCategory->slug) }}" class="{{-- request()->is('danh-muc/' . $childCategory->slug) ? 'active' : '' --}}">
                                                        {{ $childCategory->name }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </aside>

        {{-- ======================================================= --}}
        {{-- CỘT BÊN PHẢI: HIỂN THỊ SẢN PHẨM --}}
        {{-- ======================================================= --}}
        <div class="col-lg-9 col-md-12">
            <div class="collection-header">
                <h1 class="collection-title">Tất cả sản phẩm</h1>
                <div class="collection-sort d-flex align-items-center">
                    <label for="sort-options" class="me-2">Sắp xếp theo:</label>
                    <select class="form-select" id="sort-options">
                        <option selected>Mới nhất</option>
                        <option value="price-asc">Giá: Tăng dần</option>
                        <option value="price-desc">Giá: Giảm dần</option>
                        <option value="name-asc">Tên: A-Z</option>
                        <option value="name-desc">Tên: Z-A</option>
                    </select>
                </div>
                 {{-- Nút lọc cho mobile, sẽ được xử lý bằng JS sau --}}
                <button class="btn btn-outline-dark d-lg-none" id="js-mobile-filter-toggle">
                    <i class="fas fa-filter"></i> Lọc
                </button>
            </div>

            {{-- Lưới sản phẩm --}}
            <div class="row product-grid">
                {{-- 
                  Lặp qua biến $products (đã được phân trang trong Controller).
                  Mỗi trang sẽ hiển thị 12 sản phẩm.
                --}}
                @forelse($products as $product)
                    <div class="col-6 col-md-4 mb-4">
                        {{-- Component item sản phẩm có thể tái sử dụng từ trang chủ --}}
                        <div class="item_product_main">
                            <div class="product-action">
                                <div class="product-thumbnail">
                                    <a href="{{ route('frontend.product.show', $product->slug) }}">
                                        <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="image_thumb">
                                    </a>
                                </div>
                                <div class="product-info">
                                    <h3 class="product-name">
                                        <a href="{{ route('frontend.product.show', $product->slug) }}">{{ $product->name }}</a>
                                    </h3>
                                </div>
                                <div class="product-bottom">
                                    <div class="price-box">
                                        <span class="price">{{ number_format($product->price) }}đ</span>
                                        @if($product->compare_price)
                                            <span class="compare-price">{{ number_format($product->compare_price) }}đ</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <p class="text-center">Không tìm thấy sản phẩm nào.</p>
                    </div>
                @endforelse
            </div>

            {{-- Phân trang --}}
            <div class="pagination-wrapper mt-4 d-flex justify-content-center">
                {{-- Laravel sẽ tự động render phần phân trang ở đây --}}
                {{ $products->links() }}
            </div>

        </div>
    </div>          
</div>
@endsection
@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const openBtn = document.getElementById('js-mobile-filter-toggle');
    const closeBtn = document.getElementById('js-close-filter-sidebar');
    const sidebar = document.getElementById('js-mobile-filter-sidebar');
    const overlay = document.getElementById('js-sidebar-overlay');

    function openSidebar() {
        if (sidebar && overlay) {
            sidebar.classList.add('open');
            overlay.classList.add('open');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeSidebar() {
        if (sidebar && overlay) {
            sidebar.classList.remove('open');
            overlay.classList.remove('open');
            document.body.style.overflow = '';
        }
    }

    if (openBtn) {
        openBtn.addEventListener('click', openSidebar);
    }
    if (closeBtn) {
        closeBtn.addEventListener('click', closeSidebar);
    }
    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }
});
</script>
@endpush
