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
@php
    $productsTitle = $pageSettings->products_title ?: 'Sản phẩm';
@endphp

<x-frontend.leaderboard
    :image="$pageSettings->products_banner ?: ($setting->banner ?? 'images/setting/cover01.jpg')"
    :title="$productsTitle"
    :subline="$pageSettings->products_leaderboard_subline"
    :description="$pageSettings->products_leaderboard_description ?: ($pageSettings->products_headline ?: $pageSettings->products_description)"
    :breadcrumb="[['label' => $productsTitle]]"
    :actions="$pageSettings->products_leaderboard_actions"
    :stats="$pageSettings->products_leaderboard_stats"
/>

<div class="bg-white dark:bg-gray-900 py-12">
    <div class="max-w-screen-xl mx-auto px-4">

        @include('frontend.products.partials.filter-bar', [
            'allCategories' => $allCategories,
            'allBrands' => $allBrands,
            'action' => route('products.index'),
        ])

        @if($hasFilters ?? false)
            <div class="mb-5 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-950 dark:text-white">Kết quả lọc</h2>
                <span class="text-sm font-semibold text-gray-500">{{ $products->total() }} sản phẩm</span>
            </div>

            @if($products->isEmpty())
                <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 p-10 text-center dark:border-gray-700 dark:bg-gray-800">
                    <p class="font-semibold text-gray-600 dark:text-gray-300">Không tìm thấy sản phẩm phù hợp.</p>
                </div>
            @else
                <div class="grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                    @foreach($products as $product)
                        @include('partials.frontend.product_item', ['product' => $product])
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $products->links('frontend.products.partials.pagination') }}
                </div>
            @endif
        @else
        {{-- DANH SÁCH SẢN PHẨM THEO TỪNG CATEGORY --}}
        <div class="space-y-16">
            @foreach ($allCategoryAndProduct as $item)
                <section class="category-section">
                    <div class="flex flex-wrap items-end justify-between border-b-2 border-gray-100 dark:border-gray-700 mb-6 pb-2">
                        <h3 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white uppercase relative inline-block">
                            <a href="{{ $item->category->slug_url }}" class="hover:text-blue-600 transition-colors">
                                {{ $item->category->name }}
                            </a>
                            <div class="absolute -bottom-1 left-0 w-16 h-1 bg-blue-600 rounded-r-full"></div>
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
        @endif

    </div>
</div>

<x-frontend.page-cta
    :title="$pageSettings->products_cta_title"
    :description="$pageSettings->products_cta_description"
    :link="$pageSettings->products_cta_link"
/>

@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
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
