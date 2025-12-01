@extends('layouts.master')
@section('title', $pageTitle)

@push('css')
    @push('css')
    {{-- 1. Link CSS của Swiper (Bắt buộc) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    {{-- 2. Custom CSS cho trang dự án --}}
    <style>
        /* --- CẤU HÌNH CHUNG --- */
        :root {
            --primary-color: #0d6efd; /* Màu chủ đạo, đổi theo brand của bạn */
            --text-color: #333;
            --bg-light: #f8f9fa;
        }

        #project-wrapper {
            padding-bottom: 50px;
        }

        /* --- BANNER DỰ ÁN --- */
        .project-banner {
            position: relative;
            width: 100%;
            height: 300px; /* Chiều cao cố định banner */
            overflow: hidden;
        }

        .project-banner img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }

        .project-banner_overlay {
            position: absolute;
            top: 0; left: 0; bottom: 0; right: 0;
            background: rgba(0, 0, 0, 0.2); /* Lớp phủ tối nhẹ để ảnh có chiều sâu */
        }

        /* --- THÔNG TIN DỰ ÁN (CỘT TRÁI) --- */
        .project-info h1.project-name {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--primary-color);
            line-height: 1.3;
        }

        .project-description {
            margin-bottom: 1.5rem;
            color: #555;
            text-align: justify;
        }

        .project-info ul {
            list-style: none;
            padding: 0;
            margin: 0;
            background: #fff;
            border: 1px solid #eee;
            border-radius: 8px;
            overflow: hidden;
        }

        .project-info ul li {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            font-size: 0.95rem;
            display: flex;
            justify-content: space-between; /* Tên bên trái, Giá trị bên phải */
        }

        .project-info ul li:last-child {
            border-bottom: none;
        }

        .project-info ul li strong {
            color: #000;
            min-width: 130px; /* Cố định độ rộng nhãn */
        }

        /* --- ẢNH ĐẠI DIỆN (CỘT PHẢI) --- */
        .project-image img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            object-fit: cover;
            max-height: 400px; /* Giới hạn chiều cao ảnh đại diện */
        }

        /* --- GALLERY SLIDER (SWIPER) --- */
        .project-gallery {
            margin-top: 3rem;
        }

        /* Slider lớn ở trên */
        .gallery-top {
            height: 450px; /* Chiều cao cố định cho khung ảnh lớn */
            width: 100%;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 10px;
            background: #000; /* Nền đen để ảnh nổi bật */
        }

        .gallery-top .swiper-slide {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .gallery-top .swiper-slide img {
            width: 100%;
            height: 100%;
            object-fit: contain; /* Hiển thị trọn ảnh, không bị cắt */
        }

        /* Nút điều hướng gallery */
        .gallery-top .swiper-button-next,
        .gallery-top .swiper-button-prev {
            color: #fff;
            background: rgba(0,0,0,0.3);
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }
        .gallery-top .swiper-button-next:after,
        .gallery-top .swiper-button-prev:after {
            font-size: 18px;
            font-weight: bold;
        }

        /* Slider thumbnails ở dưới */
        .gallery-thumbs {
            height: 100px;
            box-sizing: border-box;
            padding: 10px 0;
        }

        .gallery-thumbs .swiper-slide {
            width: 25%;
            height: 100%;
            opacity: 0.4;
            cursor: pointer;
            border-radius: 4px;
            overflow: hidden;
            transition: opacity 0.3s;
        }

        .gallery-thumbs .swiper-slide-thumb-active {
            opacity: 1;
            border: 2px solid var(--primary-color); /* Viền màu active */
        }

        .gallery-thumbs .swiper-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* --- NỘI DUNG CHI TIẾT --- */
        .project-content {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid #eee;
            line-height: 1.8;
            font-size: 1rem;
            color: #333;
        }
        /* CSS cho ảnh trong bài viết tự responsive */
        .project-content img {
            max-width: 100%;
            height: auto !important;
            display: block;
            margin: 15px auto;
            border-radius: 4px;
        }

        /* --- DỰ ÁN KHÁC --- */
        .otherProject {
            margin-top: 4rem;
            padding-top: 2rem;
            border-top: 1px solid #eee;
        }

        .project-card {
            display: block;
            text-decoration: none;
            color: #333;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            background: #fff;
        }

        .project-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
            color: var(--primary-color);
        }

        .project-card img {
            width: 100%;
            aspect-ratio: 4/3; /* Tỷ lệ ảnh dự án liên quan */
            object-fit: cover;
        }

        .project-card .project-name {
            display: block;
            padding: 15px;
            font-weight: 600;
            font-size: 1.1rem;
            text-align: center;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* --- RESPONSIVE MOBILE --- */
        @media (max-width: 768px) {
            .project-banner {
                height: 200px; /* Banner thấp hơn trên mobile */
            }
            
            .project-info h1.project-name {
                font-size: 1.5rem;
                margin-top: 20px;
            }

            .project-info ul li {
                flex-direction: column; /* Nhãn trên, giá trị dưới */
                gap: 5px;
            }

            .project-image {
                margin-top: 20px;
            }

            .gallery-top {
                height: 300px; /* Gallery thấp hơn trên mobile */
            }
        }
    </style>
@endpush
@endpush

@section('content')
<div id="project-wrapper">
    <div class="project-banner mb-4">
        <img src="{{ optional($project->bannerImage())->url() }}"
             alt="{{ $project->name }}" width="1920" height="300" loading="eager">
        <div class="project-banner_overlay"></div>
    </div>

    <div class="container my-5">
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="project-info">
                    <h1 class="project-name">{{$project->name}}</h1>
                    <div class="project-description">{!! $project->description !!}</div>
                    <ul>
                        <li><strong>Tên dự án:</strong> {{$project->name}}</li>
                        <li><strong>Chủ đầu tư:</strong> {{$project->investor}}</li>
                        <li><strong>Địa chỉ:</strong> {{$project->address}}</li>
                        <li><strong>Năm thực hiện:</strong> {{$project->year}}</li>
                        <li><strong>Giá trị gói thầu:</strong> {{number_format($project->value,0,',','.')}}</li>
                    </ul>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="project-image">
                    <img src="{{ optional($project->mainImage())->url() }}" alt="{{$project->name}}">
                </div>
            </div>
        </div>

        {{-- ============ GALLERY (Chuẩn HasImages + Blade thuần) ============ --}}
        @if($project->gallery && $project->gallery->isNotEmpty())
        <section class="project-gallery mt-5" aria-label="Thư viện hình ảnh dự án">
            <h2 class="custom-section-title">Hình ảnh dự án</h2>

            <!-- Slider lớn -->
            <div class="swiper gallery-top">
                <div class="swiper-wrapper">
                    @foreach($project->gallery as $img)
                        <div class="swiper-slide">
                            <img
                                class="swiper-lazy"
                                src="{{ optional($img)->url() }}"
                                data-src="{{ optional($img)->url() }}"
                                alt="{{ $img->alt ?? $project->name }}"
                                loading="lazy"
                            >
                            <div class="swiper-lazy-preloader"></div>
                        </div>
                    @endforeach
                </div>

                <button class="swiper-button-prev"></button>
                <button class="swiper-button-next"></button>
            </div>

            <!-- Thumbnails -->
            <div class="swiper gallery-thumbs mt-2">
                <div class="swiper-wrapper">
                    @foreach($project->gallery as $img)
                        <div class="swiper-slide">
                            <img
                                src="{{ optional($img)->url() }}"
                                alt="Thumbnail {{ $project->name }}"
                                loading="lazy"
                            >
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif


        {{-- ====================================================== --}}
        {{-- PHẦN NỘI DUNG CHI TIẾT --}}
        {{-- ====================================================== --}}
        <div class="project-content">
            {!!$project->content!!}
        </div>

        {{-- ====================================================== --}}
        {{-- PHẦN DỰ ÁN TIÊU BIỂU KHÁC --}}
        {{-- ====================================================== --}}
        @if($relatedProjects && $relatedProjects->count() > 0)
        <div class="otherProject">
            <h2 class="custom-section-title">Dự án tiêu biểu khác</h2>
            <div class="swiper other-projects-slider">
                <div class="swiper-wrapper">
                    @foreach($relatedProjects as $other)
                    <div class="swiper-slide">
                        <a href="{{ route('frontend.slug.handle', $other->slug) }}" class="project-card">
                            <img src="{{ optional($other->mainImage())->url() }}" alt="{{ $other->name }}">
                            <span class="project-name">{{ $other->name }}</span>
                        </a>
                    </div>
                    @endforeach
                </div>
                 <div class="swiper-pagination"></div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('js')
    {{-- Link JS của Swiper Slider (BẮT BUỘC) --}}
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- KHỞI TẠO SLIDER THƯ VIỆN ẢNH ---
        // Kiểm tra xem element có tồn tại không trước khi khởi tạo
        if (document.querySelector('.gallery-thumbs') && document.querySelector('.gallery-top')) {
            const galleryThumbs = new Swiper('.gallery-thumbs', {
                spaceBetween: 10,
                slidesPerView: 4,
                freeMode: true,
                watchSlidesProgress: true,
            });

            const galleryTop = new Swiper('.gallery-top', {
                spaceBetween: 10,
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                lazy: { loadPrevNext: true, loadOnTransitionStart: true },
                preloadImages: false,
                watchSlidesProgress: true,
                thumbs: {
                    swiper: galleryThumbs,
                },
                observer: true,
                observeParents: true,
            });
        }
        
        // --- KHỞI TẠO SLIDER DỰ ÁN KHÁC ---
        // Kiểm tra xem element có tồn tại không trước khi khởi tạo
        if (document.querySelector('.other-projects-slider')) {
            const otherProjectsSlider = new Swiper('.other-projects-slider', {
                loop: true,
                spaceBetween: 30,
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                breakpoints: {
                    576: { slidesPerView: 1 },
                    768: { slidesPerView: 2 },
                    992: { slidesPerView: 3 },
                }
            });
        }
    });
    </script>
@endpush