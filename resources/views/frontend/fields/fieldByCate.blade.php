@extends('layouts.master')

@section('title', $pageTitle)



@push('css')

<link rel="stylesheet" href="{{ asset('css/product.css') }}">

@endpush



@section('content')



{{-- 

    Phần Banner: 

    - Lấy thông tin từ biến $current_category (danh mục cha, là một đối tượng đơn lẻ).

    - Em giả sử cột chứa ảnh banner của anh tên là 'banner', anh hãy đổi lại cho đúng nhé.

    - Lỗi 'prent' đã được sửa.

--}}

<div class="banner">

    {{-- Giả sử bạn có một cột 'banner' trong bảng field_categories để lưu ảnh banner --}}

    <img src="{{  optional($current_category->bannerImage())->url() ?? '' }}" alt="{{ $current_category->name }}">

</div>



{{-- Phần Lĩnh vực hoạt động --}}

<section class="section section-field">

    {{-- 

        Phần Tiêu đề:

        - Cũng lấy thông tin từ $current_category.

    --}}

    <h2 class="section-title">

        <a href="#">{{ $current_category->name }}</a>

    </h2>

    

    <div class="container py-5">

        <div class="row">

            @forelse($field_categories as $field_category)

                <div class="col-6 col-md-4">

                    <div class="field-category-item">

                        <div class="field-category-item__image">

                            {{-- Sử dụng slug của mục con --}}

                            <a href="{{ route('frontend.slug.handle', $field_category->slugValue) }}">

                                <img src="{{ optional($field_category->mainImage())->url() }}" alt="{{ $field_category->name }}">

                            </a>

                        </div>

                        <div class="field-category-item__name">

                            {{-- Sử dụng tên của mục con --}}

                            <a href="{{ route('frontend.slug.handle', $field_category->slugValue) }}">

                                {{ $field_category->name }}

                            </a>

                        </div>

                    </div>

                </div>

            @empty

                {{-- Thêm phần này để thông báo nếu danh mục không có mục con nào --}}

                <div class="col-12">

                    <div class="alert alert-warning">Thông tin đang được cập nhật...</div>

                </div>

            @endforelse

        </div>

    </div>

</section>



@endsection