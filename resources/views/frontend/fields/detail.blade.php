@extends('layouts.master')
@section('title', $pageTitle)
@section('meta_description', $field->meta_description ?? '')
@section('meta_image', optional($field->mainImage())->url() ?? '')
@push('css')
<style>
    /* ========================================================
       CSS RESPONSIVE CHO CONTENT (CKEDITOR)
       ======================================================== */
    
    .post-content {
        color: #333;
        font-size: 16px;
        line-height: 1.6;
        font-family: Arial, sans-serif;
        overflow-wrap: break-word; /* Ngắt dòng dài để ko vỡ layout */
    }

    /* 1. ẢNH & VIDEO: Luôn vừa khung màn hình */
    .post-content img, 
    .post-content iframe,
    .post-content video {
        max-width: 100% !important;
        height: auto !important;
        display: block;
        margin: 15px auto; /* Căn giữa */
        border-radius: 4px;
    }

    /* 2. HEADINGS: Cấu hình kích thước chữ (Desktop) */
    .post-content h1, 
    .post-content h2, 
    .post-content h3, 
    .post-content h4 {
        color: #222;
        font-weight: 700;
        margin-top: 30px;
        margin-bottom: 15px;
        line-height: 1.3;
        /* Giữ khoảng cách khi bấm TOC để không bị header che */
        scroll-margin-top: 100px; 
    }

    /* Kích thước chuẩn trên máy tính */
    .post-content h2 { font-size: 26px; }
    .post-content h3 { font-size: 22px; }
    .post-content h4 { font-size: 18px; }

    /* 3. BẢNG (TABLE): Kẻ khung và padding */
    .post-content table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
    }
    .post-content table th, 
    .post-content table td {
        border: 1px solid #ddd;
        padding: 8px 12px;
    }

    /* 4. LIST (ul, ol) */
    .post-content ul, .post-content ol {
        padding-left: 25px;
        margin-bottom: 20px;
    }

    /* ========================================================
       RESPONSIVE MOBILE (Màn hình nhỏ hơn 768px)
       Phần này xử lý việc chữ to mà anh đang gặp
       ======================================================== */
    @media (max-width: 768px) {
        
        /* Giảm size Heading xuống cho vừa mắt điện thoại */
        .post-content h2 { font-size: 20px !important; } /* Desktop 26 -> Mobile 20 */
        .post-content h3 { font-size: 18px !important; } /* Desktop 22 -> Mobile 18 */
        .post-content h4 { font-size: 16px !important; }
        
        /* Text thường canh trái để dễ đọc hơn canh đều */
        .post-content p { 
            text-align: left !important; 
            font-size: 15px; 
        }

        /* Bảng: Cho phép trượt ngang nếu bảng to quá khổ */
        .post-content table {
            display: block;
            width: 100%;
            overflow-x: auto;
            white-space: nowrap;
        }

        /* Padding cho ảnh đỡ bị sát lề quá */
        .post-content img {
            margin: 10px auto;
        }
    }
</style>
@endpush
{{-- 1. XỬ LÝ DỮ LIỆU TOC TRƯỚC KHI RENDER --}}
@php
    $processedData = \App\Helpers\TocHelper::process($field->content);
    $contentHtml = $processedData['html']; // Nội dung có ID
    $tocList     = $processedData['toc'];  // Mảng mục lục
@endphp

@section('content')
<div class="banner">
    <img src="{{ optional($field->bannerImage())->url() ?? asset($setting->banner) }}" alt="{{ $field->name }}" class="w-100">
</div>

<div class="container py-4">
    <div class="row">
        
        {{-- CỘT TRÁI: NỘI DUNG (Col-8) --}}
        <div class="col-12 col-lg-8">
            <article class="post-detail">
                {{-- Tiêu đề --}}
                <h1 class="mb-2">{{ $field->name }}</h1>

                {{-- Meta data --}}
                <p class="text-muted mb-3">
                    <i class="fa-regular fa-calendar"></i> {{ $field->updated_at->format('d/m/Y') }}
                </p>

                {{-- [NEW] NÚT CHIA SẺ MXH --}}
                <x-social-share :title="$field->name" />

                <hr class="d-lg-none">

                {{-- Nội dung bài viết --}}
                <div class="post-content mt-4 text-justify">
                    {!! $contentHtml !!}
                </div>
                
                {{-- Chia sẻ lại ở cuối bài --}}
                <div class="mt-5">
                    <p class="font-weight-bold">Bạn thấy bài viết hữu ích? Chia sẻ ngay:</p>
                    <x-social-share :title="$field->name" />
                </div>

            </article>
        </div>

        {{-- CỘT PHẢI: SIDEBAR (Col-4) --}}
        <div class="col-lg-4">
            {{-- Component TOC: Tự xử lý Sticky Desktop và Mobile Fab --}}
            <x-toc :list="$tocList" />
        </div>

    </div>
</div>
@endsection