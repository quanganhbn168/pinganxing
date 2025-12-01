@extends('layouts.master')
@section('title', $product->name)
@section('meta_description', $product->descrition)
@section('meta_image', optional($product->mainImage())->url())
@push('css')
    <link rel="stylesheet" href="{{ asset('css/product.css') }}">
@endpush
@section('content')
    <div id="product-detail">
        <div class="banner" style="background: #f4f4f4; padding: 20px 0;">
             <div class="container-custom">
                 <span class="text-muted">Trang chủ / {{ $product->category->name }} / {{ $product->name }}</span>
             </div>
        </div>

        <div class="container-custom mt-4">
            <section class="section section-product__detail">
                <div class="row">
                    {{-- CỘT TRÁI: ẢNH --}}
                    <div class="col-lg-6 product-images">
                        <div class="gallery-container">
                            <div class="swiper main-slider">
                                <div class="swiper-wrapper">
                                    @php
                                        // 1. Ưu tiên lấy Gallery
                                        $images = $product->gallery && $product->gallery->isNotEmpty() ? $product->gallery : collect([]);
                                        
                                        // 2. Nếu Gallery rỗng, thử lấy Main Image hoặc Banner
                                        if($images->isEmpty()){
                                            if($product->mainImage()) $images->push($product->mainImage());
                                            elseif($product->bannerImage()) $images->push($product->bannerImage());
                                        }
                                    @endphp

                                    @if($images->isEmpty())
                                        {{-- TRƯỜNG HỢP 1: Không có bất kỳ ảnh nào -> Hiển thị ảnh mặc định --}}
                                        <div class="swiper-slide">
                                            <img src="{{ asset('images/setting/no-image.png') }}" 
                                                 alt="No Image" 
                                                 loading="lazy"
                                                 style="object-fit: contain;"> </div>
                                    @else
                                        {{-- TRƯỜNG HỢP 2: Có ảnh -> Chạy vòng lặp --}}
                                        @foreach ($images as $image)
                                            <div class="swiper-slide">
                                                {{-- Logic: Nếu url() null thì lấy ảnh mặc định. Nếu link chết (404) thì onerror kích hoạt --}}
                                                <img src="{{ optional($image)->url() ?? asset('images/setting/no-image.png') }}" 
                                                     alt="{{ $image->alt ?? $product->name }}" 
                                                     loading="lazy"
                                                     onerror="this.onerror=null;this.src='{{ asset('images/setting/no-image.png') }}';">
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                <div class="swiper-button-next"></div>
                                <div class="swiper-button-prev"></div>
                            </div>
                        </div>

                        {{-- Chỉ hiển thị Slide nhỏ (Thumb) khi có NHIỀU HƠN 1 ảnh --}}
                        @if($images->count() > 1)
                        <div class="swiper gallery-thumbs">
                            <div class="swiper-wrapper">
                                @foreach ($images as $image)
                                    <div class="swiper-slide">
                                        <img src="{{ optional($image)->url() ?? asset('images/setting/no-image.png') }}" 
                                             alt="Thumb"
                                             onerror="this.onerror=null;this.src='{{ asset('images/setting/no-image.png') }}';">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- CỘT PHẢI: THÔNG TIN --}}
                    <div class="col-lg-6">
                        <div class="details-pro">
                            <h1 class="title-product">{{ $product->name }}</h1>
                            
                            <div class="sku-product">
                                Mã sản phẩm: <strong>{{ $product->code ?? 'Đang cập nhật' }}</strong>
                            </div>

                            <div class="description-short text-muted mb-4">
                                {!! Str::limit(strip_tags($product->description), 200) !!}
                            </div>

                            <div class="contact-actions">
                                <a href="lien-he" class="btn btn-primary btn-crossover text-white text-decoration-none">
                                    <span>Liên hệ báo giá ngay</span>
                                    <i class="fa-solid fa-arrow-right-long"></i>
                                </a>
                            </div>

                            <div class="contact-methods">
                                <a href="{{ $setting->zalo }}" class="contact-box">
                                    <img src="{{ asset('images/setting/Icon_of_Zalo.svg') }}" alt="Zalo" width="30">
                                    <span>Chat qua Zalo</span>
                                </a>
                                <a href="tel:{{ $setting->phone }}" class="contact-box">
                                    <img src="{{ asset('images/setting/phone.svg') }}" alt="Phone" width="30">
                                    <span>{{ $setting->phone }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- PHẦN TABS: BOOTSTRAP 4 STYLE --}}
                <div class="row product-tabs">
                    <div class="col-12">
                        <ul class="nav nav-tabs" id="productTab" role="tablist">
                            <li class="nav-item">
                                {{-- BS4 dùng thẻ a, data-toggle="tab", href="#id" --}}
                                <a class="nav-link active" id="desc-tab" data-toggle="tab" href="#desc" role="tab" aria-controls="desc" aria-selected="true">
                                    Mô tả chi tiết
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="specs-tab" data-toggle="tab" href="#specs" role="tab" aria-controls="specs" aria-selected="false">
                                    Thông số kỹ thuật
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content" id="productTabContent">
                            {{-- Tab Mô tả --}}
                            <div class="tab-pane fade show active" id="desc" role="tabpanel" aria-labelledby="desc-tab">
                                <div class="product-detail-info">
                                    @if($product->content) {!! $product->content !!} @else <p class="text-muted">Đang cập nhật...</p> @endif
                                </div>
                            </div>
                            {{-- Tab Thông số --}}
                            <div class="tab-pane fade" id="specs" role="tabpanel" aria-labelledby="specs-tab">
                                <div class="product-detail-info">
                                    @if($product->specifications) {!! $product->specifications !!} @else <p class="text-muted">Đang cập nhật...</p> @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        {{-- RELATED PRODUCTS --}}
        @if(isset($relatedProducts) && $relatedProducts->count() > 0)
        <section class="related-product">
            <div class="container-custom">
                <h2 class="custom-section-title mb-4 text-center">Sản phẩm liên quan</h2>
                {{-- ĐÃ SỬA: đổi class category-slider thành related-product-slider --}}
                <div class="related-product-slider swiper">
                    <div class="swiper-wrapper">
                        @foreach ($relatedProducts as $product)
                            <div class="swiper-slide">
                                <div class="product-card-wrapper">
                                    @include('partials.frontend.product_item', ['product' => $product])
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                </div>
            </div>
        </section>
        @endif
        
        {{-- Footer Info --}}
        <div class="container-custom mt-4">
             <div class="thankyou-info text-center p-4 bg-white border">
                <p class="fw-bold mb-2">TRÂN TRỌNG CẢM ƠN QUÝ KHÁCH ĐÃ QUAN TÂM</p>
                <div class="small text-muted">
                    <span class="mx-2"><i class="fa fa-map-marker"></i> {{ $setting->address }}</span> |
                    <span class="mx-2"><i class="fa fa-phone"></i> {{ $setting->phone }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Slider Thumbs
            var thumbnailSlider = new Swiper(".gallery-thumbs", {
                spaceBetween: 10,
                slidesPerView: 4,
                freeMode: true,
                watchSlidesProgress: true,
            });

            // 2. Slider Main
            var mainSlider = new Swiper(".main-slider", {
                spaceBetween: 10,
                navigation: {
                    nextEl: ".swiper-button-next",
                    prevEl: ".swiper-button-prev"
                },
                thumbs: { swiper: thumbnailSlider },
            });

        // 3. Slider Sản phẩm liên quan (FIX LẠI: Chỉ khởi tạo đúng 1 lần)
        // Kiểm tra xem class này có tồn tại không rồi mới chạy để tránh lỗi console
        if (document.querySelector('.related-product-slider')) {
            new Swiper(".related-product-slider", {
                loop: false,
                slidesPerView: 2,
                spaceBetween: 20,
                // Định nghĩa nút bấm nằm cụ thể trong class .related-product-slider
                navigation: {
                    nextEl: ".related-product-slider .swiper-button-next",
                    prevEl: ".related-product-slider .swiper-button-prev",
                },
                breakpoints: {
                    576: { slidesPerView: 2, spaceBetween: 20 },
                    768: { slidesPerView: 3, spaceBetween: 20 },
                    992: { slidesPerView: 4, spaceBetween: 30 }
                }
            });
        }
        });
    </script>
@endpush