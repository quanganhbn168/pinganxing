@extends('layouts.master')
@section('title','Trang chủ - '.$setting->name)
@section('meta_description',$setting->meta_description)
@section('meta_keywords',$setting->meta_keywords)
@push('jsonld')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Store",
  "name": "{{$setting->name}}",
  "alternateName": "{{$setting->name}}",
  "url": "{{ url()->current() }}",
  "logo": "{{asset($setting->logo)}}",
  "description": "{{$setting->meta_description}}",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "{{$setting->address}}",
    "addressLocality": "Thành phố Bắc Ninh",
    "addressRegion": "Bắc Ninh",
    "postalCode": "220000",
    "addressCountry": "VN"
  },
  "telephone": "{{$setting->phone}}",
  "email": "{{$setting->email}}",
  "openingHoursSpecification": [
    {
      "@type": "OpeningHoursSpecification",
      "dayOfWeek": [
        "Monday",
        "Tuesday",
        "Wednesday",
        "Thursday",
        "Friday"
      ],
      "opens": "08:00",
      "closes": "17:30"
    },
    {
      "@type": "OpeningHoursSpecification",
      "dayOfWeek": "Saturday",
      "opens": "08:00",
      "closes": "12:00"
    }
  ],
  "sameAs": [
    "{{$setting->facebook}}",
    "{{$setting->youtube}}",
    "{{$setting->zalo}}"
  ]
}
</script>
@endpush
@push('css')
<link rel="stylesheet" href="{{asset('vendor/glightbox/css/glightbox.min.css')}}?{{time()}}">
@endpush
@section("content")
<section id="slider">
    @include("partials.frontend.slide")
</section>
<section class="section-index section_category">
    <div class="container">
        <div class="section-title side-left has-control">
            <h2>Sản phẩm bạn đang tìm kiếm</h2>
            <div class="slider-controls">
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>
        </div>
        <div class="swiper_category swiper">
            <div class="swiper-wrapper">
                @foreach($allCategoriesHome as $key => $allCategoyHome)
                <div class="swiper-slide">
                    <a href="{{route('products.by_category',$allCategoyHome->slug)}}" title="{{$allCategoyHome->name}}" class="cate-item">
                        <div class="bg-thumb">
                            <div class="thumb">
                                <img src="{{ $allCategoyHome->icon ? asset($allCategoyHome->icon) : asset('images/setting/no-image.png') }}" alt="{{ $allCategoyHome->name }}" loading="lazy">
                            </div>
                        </div>
                        <div class="cate-content">
                            <h3 class="line-clamp-2-new">{{$allCategoyHome->name}}</h3>
                            <div class="status">
                                <span class="total-product">{{$allCategoyHome->products()->count()}} sản phẩm</span>
                                <span class="view-more">Xem chi tiết</span>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach                
            </div>
        </div>
    </div>
</section>
@foreach ($categoriesWithProducts as $category)
<section class="section-index section_product">
    <div class="container-fluid">
        <div class="row">
            <div class="block-title col-lg-3 col-xl-2">
                <div class="section-title side-left has-control">
                    <h2>
                        <a href="{{route('products.by_category',$category->slug)}}">{{ $category->name }}</a>
                    </h2>
                    <div class="slider-controls">
                        <div class="swiper-button-prev"></div>
                        <div class="swiper-button-next"></div>
                    </div>
                </div>
                <a href="{{route('products.by_category',$category->slug)}}" title="Xem tất cả" class="btn btn-primary d-none d-sm-block">
                    <span>Xem tất cả</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                    </svg>
                </a>
            </div>
            <div class="block-product-list col-lg-9 col-xl-10">
                <div class="product-slider swiper">
                    <div class="swiper-wrapper">
                        @foreach($category->products as $product)
                            <div class="swiper-slide">
                                @include('partials.frontend.product_item', ['product' => $product])
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="block-see-more text-center d-block d-md-none mt-3">
                    <a href="{{route('products.by_category',$category->slug)}}" title="Xem tất cả" class="btn btn-primary">
                        <span>Xem tất cả</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endforeach
@php
    // Chỉ hiển thị section này khi có đủ 4 danh mục nổi bật
    // Vì layout này được thiết kế cứng cho 4 mục
@endphp
@if(isset($featuredCategories) && $featuredCategories->count() >= 4)
<section class="section-index section_group_banner">
    <div class="container-fluid"> 
        <div class="grid">
            <div class="col-left">
                <div class="banner_1 banner-box">
                    @php $cat = $featuredCategories[0]; @endphp
                    <a href="{{ route('products.by_category', $cat->slug) }}" title="{{ $cat->name }}">
                        <img src="{{ asset($cat->image ?? 'images/setting/no-image.png') }}" alt="{{ $cat->name }}" loading="lazy" class="lazyload duration-300">
                        <div class="box-title">
                            <h3>{{ $cat->name }}</h3>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-right">
                <div class="grid-sub">
                    <div class="banner_2 banner-box">
                        @php $cat = $featuredCategories[1]; @endphp
                        <a href="{{ route('products.by_category', $cat->slug) }}" title="{{ $cat->name }}">
                            <img src="{{ asset($cat->image ?? 'images/setting/no-image.png') }}" alt="{{ $cat->name }}" loading="lazy" class="lazyload duration-300">
                            <div class="box-title">
                                <h3>{{ $cat->name }}</h3>
                            </div>
                        </a>
                    </div>
                    <div class="banner_3 banner-box">
                        @php $cat = $featuredCategories[2]; @endphp
                        <a href="{{ route('products.by_category', $cat->slug) }}" title="{{ $cat->name }}">
                            <img src="{{ asset($cat->image ?? 'images/setting/no-image.png') }}" alt="{{ $cat->name }}" loading="lazy" class="lazyload duration-300">
                            <div class="box-title">
                                <h3>{{ $cat->name }}</h3>
                            </div>
                        </a>
                    </div>
                    <div class="banner_4 banner-box">
                        @php $cat = $featuredCategories[3]; @endphp
                        <a href="{{ route('products.by_category', $cat->slug) }}" title="{{ $cat->name }}">
                            <img src="{{ asset($cat->image ?? 'images/setting/no-image.png') }}" alt="{{ $cat->name }}" loading="lazy" class="lazyload duration-300">
                            <div class="box-title">
                                <h3>{{ $cat->name }}</h3>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endif
@if(isset($saleProducts) && $saleProducts->count() > 0)
<section class="section-index section_product sale_product">
    <div class="container-fluid">
        <div class="row">
            <div class="block-title col-lg-3 col-xl-2">
                <div class="section-title side-left has-control">
                    <h2>
                        <a href="{{route('products.by_category',$category->slug)}}">{{ $category->name }}</a>
                    </h2>
                    <div class="slider-controls">
                        <div class="swiper-button-prev"></div>
                        <div class="swiper-button-next"></div>
                    </div>
                </div>
                <a href="#" title="Xem tất cả" class="btn btn-primary d-none d-sm-block">
                    <span>Xem tất cả</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                    </svg>
                </a>
            </div>
            <div class="block-product-list col-lg-9 col-xl-10">
                <div class="product-slider swiper">
                    <div class="swiper-wrapper">
                        @foreach($saleProducts as $product)
                            <div class="swiper-slide">
                                @include('partials.frontend.product_item', ['product' => $product])
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="block-see-more text-center d-block d-md-none mt-3">
                    <a href="#" title="Xem tất cả" class="btn btn-primary">
                        <span>Xem tất cả</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endif
<section class="section-index section_feedback">
    <div class="bg-section">
        <div class="container">
            <div class="section-title side-left has-control">
                <h2>
                    Khách hàng nói về chúng tôi
                    <div class="desc">
                        Hơn +50,000 khách hàng đang sử dụng cảm nhận như thế nào về chúng tôi
                    </div>
                </h2>
                <div class="slider-controls">
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                </div>
            </div>
            <div class="swiper_feedback swiper-container control-top">
                <div class="swiper-wrapper">
                    @foreach($testimonials as $testimonial)
                        <div class="swiper-slide">
                            @include('partials.frontend.feedback_item', ['feedback' => $testimonial])
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
<section class="section-index section-blog">
    <div class="container">
        <div class="section-title side-left has-control">
            <h2>
                <a href="">Tin tức mới nhất</a>
            </h2>
            <div class="slider-controls">
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>
            <div class="swiper_blog swiper">
            </div>
        </div>
    </div>
</section>
<section class="section-index section-brand">
    <div class="container">
        <div class="section-title side-left has-control">
            <h2>
                <a href="">Thương hiệu nổi bật</a>
            </h2>
            <div class="slider-controls">
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>
            <div class="swiper_brand swiper">
                <div class="swiper-wrapper">
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@push('js')
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
<script>
        $.validator.addMethod("phoneVN", function (value, element) {
            return this.optional(element) || /^(0[3|5|7|8|9])[0-9]{8}$|^\+84[3|5|7|8|9][0-9]{8}$/.test(value);
        }, "Số điện thoại không hợp lệ");
        $(document).ready(function () {
            $('#contact-form').validate({
                rules: {
                    name: {
                        required: true,
                        minlength: 2
                    },
                    phone: {
                        required: true,
                        phoneVN: true
                    },
                    email: {
                        email: true
                    },
                    message: {
                        maxlength: 1000
                    }
                },
                messages: {
                    name: {
                        required: "Vui lòng nhập họ và tên",
                        minlength: "Tên quá ngắn"
                    },
                    phone: {
                        required: "Vui lòng nhập số điện thoại",
                        phoneVN: "Số điện thoại không hợp lệ (ví dụ: 098xxxxxxx)"
                    },
                    email: {
                        email: "Email không hợp lệ"
                    },
                    message: {
                        maxlength: "Ý kiến không vượt quá 1000 ký tự"
                    }
                },
                errorElement: 'small',
                errorClass: 'text-danger',
                highlight: function (element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function (element) {
                    $(element).removeClass('is-invalid');
                }
            });
        });
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- 1. KHỞI TẠO SLIDER CHÍNH (LUÔN CHẠY) ---
    const mainSliderEl = document.querySelector('.main-slider');
    if (mainSliderEl) {
        const mainSlider = new Swiper(mainSliderEl, {
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            lazy: {
                loadPrevNext: true,
            },
            // QUAN TRỌNG: Chỉ tìm nút điều hướng bên trong chính slider này
            pagination: {
                el: mainSliderEl.querySelector('.swiper-pagination'),
                clickable: true,
            },
            navigation: {
                nextEl: mainSliderEl.querySelector('.swiper-button-next'),
                prevEl: mainSliderEl.querySelector('.swiper-button-prev'),
            },
        });
    }
    // Tìm đến section cha chứa cả slider và các nút điều khiển
    const feedbackSection = document.querySelector('.section_feedback');
    if (feedbackSection) {
        // Tìm đến element của slider bên trong section đó
        const swiperFeedbackEl = feedbackSection.querySelector('.swiper_feedback');
        if (swiperFeedbackEl) {
            const swiperFeedback = new Swiper(swiperFeedbackEl, {
                // Hiển thị 3 slide trên màn hình lớn (desktop) làm mặc định
                slidesPerView: 3,
                // Khoảng cách giữa các slide là 20px
                spaceBetween: 20,
                loop: true,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                lazy: {
                    loadPrevNext: true,
                },
                // Sửa lỗi: Tìm pagination và navigation bên trong section hiện tại
                pagination: {
                    el: feedbackSection.querySelector('.swiper-pagination'),
                    clickable: true,
                },
                navigation: {
                    nextEl: feedbackSection.querySelector('.swiper-button-next'),
                    prevEl: feedbackSection.querySelector('.swiper-button-prev'),
                },
                // Responsive: Tùy chỉnh số lượng slide theo kích thước màn hình
                breakpoints: {
                    // Dưới 768px (Mobile)
                    0: {
                        slidesPerView: 1,
                        spaceBetween: 10,
                    },
                    // Từ 768px đến 1199px (Tablet)
                    768: {
                        slidesPerView: 2,
                        spaceBetween: 20,
                    },
                    // Từ 1200px trở lên (Desktop)
                    1200: {
                        slidesPerView: 3,
                        spaceBetween: 20,
                    },
                },
            });
        }
    }
    // --- 2. HÀM KHỞI TẠO CÁC SLIDER RESPONSIVE (chỉ chạy trên màn hình lớn) ---
    // Hàm này đã được viết tốt, giữ nguyên
    const setupResponsiveSwiper = (sectionElement, swiperSelector, options, breakpointWidth = 992) => {
        if (!sectionElement) return;
        let swiperInstance = null;
        const breakpoint = window.matchMedia(`(min-width: ${breakpointWidth}px)`);
        const initializeSwiper = () => {
            if (breakpoint.matches === true && swiperInstance === null) {
                const swiperEl = sectionElement.querySelector(swiperSelector);
                const nextEl = sectionElement.querySelector('.swiper-button-next');
                const prevEl = sectionElement.querySelector('.swiper-button-prev');
                const paginationEl = sectionElement.querySelector('.swiper-pagination');
                const finalOptions = {
                    ...options,
                    navigation: { nextEl, prevEl },
                    pagination: { el: paginationEl, clickable: true },
                };
                if (swiperEl) {
                    swiperInstance = new Swiper(swiperEl, finalOptions);
                }
            } else if (breakpoint.matches === false && swiperInstance !== null) {
                swiperInstance.destroy(true, true);
                swiperInstance = null;
            }
        };
        initializeSwiper();
        window.addEventListener('resize', initializeSwiper);
    };
    // --- 3. ÁP DỤNG HÀM RESPONSIVE CHO CÁC SECTION TƯƠNG ỨNG ---
    const categorySection = document.querySelector('.section_category');
    if (categorySection) {
        setupResponsiveSwiper(
            categorySection,
            '.swiper_category',
            {
                spaceBetween: 20,
                slidesPerView: 2,
                breakpoints: {
                    1200: { slidesPerView: 8 },
                    992: { slidesPerView: 5 },
                    768: { slidesPerView: 4 }
                }
            },
            768
        );
    }
    const productSections = document.querySelectorAll('.section_product');
    productSections.forEach(section => {
        setupResponsiveSwiper(
            section, 
            '.product-slider',
            {
                spaceBetween: 20,
                breakpoints: {
                    992: { slidesPerView: 3 },
                    1200: { slidesPerView: 4 }
                }
            },
            992
        );
    });
});
</script>
@endpush
