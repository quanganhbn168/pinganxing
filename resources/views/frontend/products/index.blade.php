@extends('layouts.master')
@section('title', 'Tất cả sản phẩm')
@section('meta_description', $page->description)
@section('meta_image', optional($page->mainImage())->url())
@push('css')
    <style>
    /* CSS cho Slider danh mục */
    .category-product-slider {
        padding: 5px 5px 30px 5px; /* Padding bottom để tránh bóng đổ bị cắt */
    }

    .category-product-slider .swiper-slide {
        height: auto; /* Quan trọng: Để các thẻ flexbox bên trong tự căng chiều cao */
        display: flex;
    }

    /* Đảm bảo thẻ sản phẩm con chiếm hết chiều cao */
    .product-card-wrapper {
        width: 100%;
        display: flex;
        flex-direction: column;
    }

    /* Tinh chỉnh nút Next/Prev nhỏ gọn hơn mặc định */
    .category-product-slider .swiper-button-next,
    .category-product-slider .swiper-button-prev {
        width: 35px;
        height: 35px;
        background-color: rgba(255, 255, 255, 0.8);
        border-radius: 50%;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .category-product-slider .swiper-button-next::after,
    .category-product-slider .swiper-button-prev::after {
        font-size: 14px;
        font-weight: bold;
        color: #333;
    }
</style>
@endpush

@section('content')
    <div class="banner">
        <img src="{{ optional($page->bannerImage())->url() }}" alt="{{ $page->title }}">
    </div>

    <div class="container py-4">
        <div class="row mb-4"> <div class="col-md-6 mb-3 mb-md-0">
                {{-- SELECT BOX: Thêm onchange để gọi hàm JS --}}
                <select name="category" id="category-select" class="form-control category-select">
                    <option value="">-- Chọn danh mục sản phẩm --</option>
                    @foreach ($allCategoryAndProduct as $item)
                        <option value="{{ route('frontend.slug.handle', $item->category->slugValue) }}">
                            {{ $item->category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                {{-- FORM TÌM KIẾM: Giữ nguyên, logic nằm ở Controller --}}
                <form action="{{ route('frontend.products.search') }}" method="GET">
                    <div class="input-group">
                        {{-- value="{{ request('q') }}" để giữ lại từ khóa sau khi tìm --}}
                        <input type="text" class="form-control" placeholder="Tìm kiếm sản phẩm..."
                            aria-label="Tìm kiếm sản phẩm" name="q" value="{{ request('q') }}" required>
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            @foreach($allCategories as $item)
                {{-- Fix lại class column và margin để không vỡ layout --}}
                <div class="col-6 col-md-4 category-card">
                    <a href="{{ route('frontend.slug.handle', $item->slug) }}" class="d-block text-center text-decoration-none text-dark font-weight-bold">
                        <div class="item-banner mb-2 border">
                            <img src="{{ optional($item->bannerImage())->url() }}" alt="{{ $item->name }}">
                        </div>
                        {{ $item->name }}
                    </a>
                </div>
            @endforeach
        </div>

        {{-- DANH SÁCH SẢN PHẨM THEO CATEGORY --}}
        @foreach ($allCategoryAndProduct as $item)
            <section class="mb-5">
                {{-- TIÊU ĐỀ DANH MỤC --}}
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                    <h3 class="mb-0 custom-section-title h4">
                        <a href="{{ route('frontend.slug.handle', $item->category->slugValue) }}" class="text-dark text-decoration-none">
                            {{ $item->category->name }}
                        </a>
                    </h3>

                    @if ($item->products->count() > 0)
                        <a href="{{ route('frontend.slug.handle', $item->category->slugValue) }}" class="btn btn-sm btn-outline-primary">
                            Xem tất cả
                        </a>
                    @endif
                </div>

                {{-- DANH SÁCH SẢN PHẨM --}}
                @if ($item->products->isEmpty())
                    <div class="alert alert-light text-center w-100">
                        Chưa có sản phẩm nào trong danh mục này.
                    </div>
                @else
                    {{-- [SỬA LẠI ĐOẠN NÀY]: Thay Row/Col bằng cấu trúc Swiper --}}
                    <div class="category-product-slider swiper">
                        <div class="swiper-wrapper">
                            @foreach ($item->products as $product)
                                <div class="swiper-slide">
                                    {{-- Thêm div wrapper để chiều cao bằng nhau --}}
                                    <div class="product-card-wrapper h-100"> 
                                        @include('partials.frontend.product_item', ['product' => $product])
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        {{-- Thêm nút điều hướng --}}
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                    </div>
                @endif
            </section>
        @endforeach

    </div>
@endsection

@push('js')
<script>
    // Đoạn script xử lý sự kiện khi chọn danh mục
    document.addEventListener('DOMContentLoaded', function() {
        const categorySelect = document.getElementById('category-select');
        
        if(categorySelect) {
            categorySelect.addEventListener('change', function() {
                const url = this.value;
                if (url) {
                    window.location.href = url; 
                }
            });
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tìm tất cả các slider danh mục trên trang
        const categorySliders = document.querySelectorAll('.category-product-slider');

        categorySliders.forEach(sliderElement => {
            // Tìm nút next/prev nằm CỤ THỂ bên trong slider đó
            const nextBtn = sliderElement.querySelector('.swiper-button-next');
            const prevBtn = sliderElement.querySelector('.swiper-button-prev');

            new Swiper(sliderElement, {
                loop: false, // Không lặp lại để người dùng biết điểm cuối
                slidesPerView: 2, // Mặc định mobile 2 sản phẩm
                spaceBetween: 15,
                
                // Cấu hình nút bấm scoped (chỉ tác dụng lên slider này)
                navigation: {
                    nextEl: nextBtn,
                    prevEl: prevBtn,
                },

                // Responsive (Mobile First)
                breakpoints: {
                    576: {
                        slidesPerView: 2,
                        spaceBetween: 20,
                    },
                    768: {
                        slidesPerView: 3, 
                        spaceBetween: 20,
                    },
                    1024: {
                        slidesPerView: 4, 
                        spaceBetween: 25,
                    },
                    1200: {
                        slidesPerView: 5, // Màn hình to hẳn thì hiện 5 cái cho đẹp
                        spaceBetween: 30,
                    }
                }
            });
        });
    });
</script>
@endpush