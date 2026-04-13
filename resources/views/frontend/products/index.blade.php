@extends('layouts.master')
@section('title', 'Tất cả sản phẩm')
@section('meta_description', 'Danh sách tất cả sản phẩm')
@section('meta_image', asset('images/setting/cover01.jpg'))

@push('css')
<style>
    .category-product-slider .swiper-slide {
        height: auto;
        display: flex;
    }
</style>
@endpush

@section('content')
{{-- Hero Banner --}}
<div class="relative w-full h-[25vh] md:h-[35vh] overflow-hidden">
    <img src="{{ asset('images/setting/cover01.jpg') }}" alt="Sản phẩm" class="w-full h-full object-cover">
    <div class="absolute inset-0 bg-gray-900/60 flex flex-col items-center justify-center">
        <h1 class="text-3xl md:text-5xl font-bold text-white uppercase tracking-wider mb-4">Sản phẩm</h1>
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="/" class="inline-flex items-center text-sm font-medium text-gray-200 hover:text-white transition-colors">
                        <i class="fas fa-home mr-2"></i> Trang chủ
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2 text-sm"></i>
                        <span class="text-sm font-medium text-gray-100">Sản phẩm</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>
</div>

<div class="bg-white dark:bg-gray-900 py-12">
    <div class="max-w-screen-xl mx-auto px-4">
        
        {{-- Toolbar Lọc / Tìm kiếm --}}
        <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-xl border border-gray-100 dark:border-gray-700 mb-10 flex flex-col md:flex-row gap-4 justify-between items-center">
            <div class="w-full md:w-1/3">
                <select name="category" id="category-select" class="bg-white border text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 dark:bg-gray-700 border-gray-300 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500">
                    <option value="">-- Chọn danh mục sản phẩm --</option>
                    @foreach ($allCategoryAndProduct as $item)
                        <option value="{{ $item->category->slug_url }}">
                            {{ $item->category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="w-full md:w-1/3">
                <form action="{{ route('frontend.products.search') }}" method="GET" class="relative w-full">
                    <input type="text" name="q" value="{{ request('q') }}" required placeholder="Tìm kiếm sản phẩm..."
                           class="bg-white border text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 pr-10 dark:bg-gray-700 border-gray-300 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                    <button type="submit" class="absolute inset-y-0 right-0 p-2.5 flex items-center text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-500 transition-colors">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>

        {{-- DANH SÁCH SẢN PHẨM THEO TỪNG CATEGORY --}}
        <div class="space-y-16">
            @foreach ($allCategoryAndProduct as $item)
                <section class="category-section">
                    <div class="flex flex-wrap items-end justify-between border-b-2 border-gray-100 dark:border-gray-700 mb-6 pb-2">
                        <h3 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white uppercase relative inline-block">
                            <a href="{{ $item->category->slug_url }}" class="hover:text-blue-600 transition-colors">
                                {{ $item->category->name }}
                            </a>
                            <div class="absolute -bottom-[4px] left-0 w-16 h-1 bg-blue-600 rounded-r-full"></div>
                        </h3>

                        @if ($item->products->count() > 0)
                            <a href="{{ $item->category->slug_url }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium inline-flex items-center group">
                                Xem tất cả <i class="fas fa-arrow-right ml-1 group-hover:translate-x-1 transition-transform"></i>
                            </a>
                        @endif
                    </div>

                    @if ($item->products->isEmpty())
                        <div class="flex items-center justify-center p-8 bg-gray-50 dark:bg-gray-800 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
                            <p class="text-gray-500 font-medium"><i class="fas fa-box-open mr-2"></i> Chưa có sản phẩm nào trong danh mục này.</p>
                        </div>
                    @else
                        <div class="relative px-0 md:px-10">
                            {{-- Swiper Container --}}
                            <div class="swiper category-product-slider overflow-hidden py-4 -my-4">
                                <div class="swiper-wrapper">
                                    @foreach ($item->products as $product)
                                        <div class="swiper-slide">
                                            <div class="w-full h-full">
                                                @include('partials.frontend.product_item', ['product' => $product])
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            {{-- Navigation Buttons Outisde/Absolute --}}
                            <div class="category-custom-prev absolute top-1/2 -translate-y-1/2 -left-5 z-10 w-10 h-10 bg-white border border-gray-200 rounded-full shadow-md hover:bg-blue-600 focus:outline-none hover:text-white text-blue-600 transition-colors hidden md:flex items-center justify-center cursor-pointer">
                                <i class="fas fa-chevron-left text-sm"></i>
                            </div>
                            <div class="category-custom-next absolute top-1/2 -translate-y-1/2 -right-5 z-10 w-10 h-10 bg-white border border-gray-200 rounded-full shadow-md hover:bg-blue-600 focus:outline-none hover:text-white text-blue-600 transition-colors hidden md:flex items-center justify-center cursor-pointer">
                                <i class="fas fa-chevron-right text-sm"></i>
                            </div>
                        </div>
                    @endif
                </section>
            @endforeach
        </div>

    </div>
</div>
@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Redirect khi đổi danh mục
        const categorySelect = document.getElementById('category-select');
        if(categorySelect) {
            categorySelect.addEventListener('change', function() {
                if (this.value) window.location.href = this.value; 
            });
        }

        // Khởi tạo Swiper cho từng danh mục
        const categorySections = document.querySelectorAll('.category-section');
        categorySections.forEach(section => {
            const slider = section.querySelector('.category-product-slider');
            const nextBtn = section.querySelector('.category-custom-next');
            const prevBtn = section.querySelector('.category-custom-prev');
            
            if(slider) {
                new Swiper(slider, {
                    slidesPerView: 2,
                    spaceBetween: 16,
                    navigation: {
                        nextEl: nextBtn,
                        prevEl: prevBtn,
                    },
                    breakpoints: {
                        640:  { slidesPerView: 2, spaceBetween: 20 },
                        768:  { slidesPerView: 3, spaceBetween: 24 },
                        1024: { slidesPerView: 4, spaceBetween: 24 },
                        1280: { slidesPerView: 5, spaceBetween: 24 }
                    }
                });
            }
        });
    });
</script>
@endpush
