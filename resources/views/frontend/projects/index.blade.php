@extends('layouts.master')

@section('title', 'Dự án')

@push('css')
<link rel="stylesheet" href="{{ asset('css/product.css') }}">
@endpush

@section('content')
@php
    // Ảnh fallback chung
    $fallbackImage = asset('images/setting/no-image.png');

    // Chuẩn hoá biến $projectFeature (có thể null/không được truyền)
    $pf = isset($projectFeature) ? $projectFeature : null;

    // Lấy URL ảnh đại diện an toàn
    $pfImage = optional(optional($pf)->mainImage())->url() ?: $fallbackImage;

    // Tạo link an toàn (nếu không có slug thì để '#')
    $pfHref  = (!empty(optional($pf)->slug)) ? route('frontend.slug.handle', $pf->slug) : '#';

    // Các label fallback
    $updating = 'Hiện đang cập nhật';
@endphp

<div class="banner">
    <img src="{{ asset('images/setting/cover01.jpg') }}" alt="Dự án của Cnet POS">
</div>

<section class="section section-featured-project">
    <div class="container">
        <h2 class="custom-section-title">
            DỰ ÁN TIÊU BIỂU
        </h2>

        {{-- Bắt đầu khối dự án tiêu biểu --}}
        <div class="featured-project-card-wrapper">
            @if($pf)
                <a href="{{ $pfHref }}" class="featured-project {{ $pfHref === '#' ? 'is-disabled' : '' }}"
                   @if($pfHref === '#') aria-disabled="true" onclick="return false;" @endif>
                    <div class="row g-0 h-100">
                        {{-- Cột hình ảnh bên trái --}}
                        <div class="col-lg-7">
                            <div class="featured-project__image">
                                <img src="{{ $pfImage }}" alt="{{ $pf->name ?? 'Dự án' }}">
                            </div>
                        </div>

                        {{-- Cột thông tin bên phải --}}
                        <div class="col-lg-5">
                            <div class="featured-project__info">
                                <div class="featured-project__item">
                                    <span class="featured-project__label">Tên dự án:</span>
                                    <h3 class="featured-project__value project-name">
                                        {{ $pf->name ?? $updating }}
                                    </h3>
                                </div>

                                <div class="featured-project__item">
                                    <span class="featured-project__label">Chủ đầu tư:</span>
                                    <p class="featured-project__value">
                                        {{ $pf->investor ?? $updating }}
                                    </p>
                                </div>

                                <div class="featured-project__item">
                                    <span class="featured-project__label">Địa chỉ:</span>
                                    <p class="featured-project__value">
                                        {{ $pf->address ?? $updating }}
                                    </p>
                                </div>

                                <div class="featured-project__item">
                                    <span class="featured-project__label">Năm thực hiện:</span>
                                    <p class="featured-project__value">
                                        {{ $pf->year ?? $updating }}
                                    </p>
                                </div>

                                <div class="featured-project__item">
                                    <span class="featured-project__label">Giá trị gói thầu:</span>
                                    <p class="featured-project__value">
                                        @if(isset($pf->value) && is_numeric($pf->value))
                                            {{ number_format($pf->value, 0, ',', '.') }}
                                        @else
                                            {{ $updating }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            @else
                {{-- Trạng thái khi chưa có dự án tiêu biểu --}}
                <div class="featured-project featured-project--placeholder">
                    <div class="row g-0 h-100">
                        <div class="col-lg-7">
                            <div class="featured-project__image">
                                <img src="{{ $fallbackImage }}" alt="Đang cập nhật">
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="featured-project__info">
                                <div class="featured-project__item">
                                    <span class="featured-project__label">Tên dự án:</span>
                                    <h3 class="featured-project__value project-name">{{ $updating }}</h3>
                                </div>
                                <div class="featured-project__item">
                                    <span class="featured-project__label">Chủ đầu tư:</span>
                                    <p class="featured-project__value">{{ $updating }}</p>
                                </div>
                                <div class="featured-project__item">
                                    <span class="featured-project__label">Địa chỉ:</span>
                                    <p class="featured-project__value">{{ $updating }}</p>
                                </div>
                                <div class="featured-project__item">
                                    <span class="featured-project__label">Năm thực hiện:</span>
                                    <p class="featured-project__value">{{ $updating }}</p>
                                </div>
                                <div class="featured-project__item">
                                    <span class="featured-project__label">Giá trị gói thầu:</span>
                                    <p class="featured-project__value">{{ $updating }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        {{-- Kết thúc khối dự án tiêu biểu --}}
    </div>
</section>

<section class="section section-other-projects">
    <div class="container">
        <h2 class="custom-section-title">
            NHỮNG DỰ ÁN TIÊU BIỂU KHÁC
        </h2>

        @php
            $hasProjects = isset($projects) && (is_countable($projects) ? count($projects) : $projects?->count());
        @endphp

        @if($hasProjects)
            <div class="row">
                @foreach($projects as $project)
                    <div class="col-6 col-md-6 mb-3">
                        {{-- Card tái sử dụng: tự xử lý fallback bên trong component nếu có --}}
                        <x-reusable-card :item="$project"/>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-info mb-0" role="status">
                Hiện đang cập nhật danh sách dự án.
            </div>
        @endif
    </div>
</section>
@endsection
