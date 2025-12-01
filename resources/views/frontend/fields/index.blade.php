@extends('layouts.master')
@section('title', $pageTitle)
@section('meta_description', $page->description ?? '')
@section('meta_image', optional($page->mainImage())->url() ?? '')
@push('css')
<link rel="stylesheet" href="{{ asset('css/product.css') }}">
@endpush
@section('content')
{{-- Phần Banner --}}
<div class="banner">
    @isset($category)
        <img src="{{ optional($category->bannerImage()->url() ?? '') }}" alt="{{ $category->name }}">
    @else
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-5">
                    <img src="{{ optional($page->mainImage())->url() }}" alt="{{ $page->title }}">
                </div>
                <div class="col-md-7">
                    <h3 class="">
                        {{ $page->title }}
                    </h3>
                    <div>
                        {{ $page->description }}
                    </div>
                    <a href="tel:{{ $setting->phone }}" class="btn btn-primary rounded-pill btn-crossover">
                        <span class="btn-crossover-text">Gọi ngay</span>
                        <span class="btn-crossover-icon">
                            <i class="fa-solid fa-arrow-right-long"></i>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    @endisset
</div>
{{-- Phần Lĩnh vực hoạt động --}}
<section class="section section-field">
    @isset($category)
        <h2 class="section-title"><a href="{{ route('frontend.slug.handle', $category->slugValue) }}">{{ $category->name }}</a></h2>
    @else
        <h2 class="section-title"><a href="{{ route('frontend.fields.index') }}">Lĩnh vực hoạt động</a></h2>
    @endisset
    <div class="container py-5">
        <div class="row">
            @if(!empty($field_categories))
                @foreach($field_categories as $field_category)
                <div class="col-6 col-md-4 mb-3">
                    <div class="field-category-item">
                        <div class="field-category-item__image">
                            <a href="{{ route('frontend.slug.handle', $field_category->slugValue) }}">
                                <img src="{{ optional($field_category->mainImage())->url() }}" alt="{{ $field_category->name }}">
                            </a>
                        </div>
                        <div class="field-category-item__name">
                            <a href="{{ route('frontend.slug.handle', $field_category->slugValue) }}">
                                {{ $field_category->name }}
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="alert alert-warning">Thông tin đang được cập nhật...</div>
            @endif
        </div>
    </div>
</section>
@endsection