@extends('layouts.master')
@section('title', $pageTitle)

@push('css')
<link rel="stylesheet" href="{{ asset('css/product.css') }}">
@endpush

@section('content')
<section class="section py-4">
    <div class="container">
        {{-- Breadcrumb sẽ luôn đúng vì đã chuẩn bị sẵn từ Controller --}}
        <x-frontend.breadcrumb :items="$breadcrumbItems" />

        <div class="row">
            <div class="col-12">
                {{-- Dùng isset() để kiểm tra xem đây là trang danh mục cụ thể hay trang tổng --}}
                @if (isset($category))
                    {{-- KỊCH BẢN 2: TRANG DANH MỤC CỤ THỂ --}}
                    <h1 class="mb-4">{{ $category->name }}</h1>
                    
                    {{-- Chỉ cần lặp qua biến $services --}}
                    @forelse($services as $service)
                        @include('frontend.services.service-item', ['service' => $service])
                    @empty
                        <p>Chưa có dịch vụ nào trong danh mục này.</p>
                    @endforelse

                    <div class="mt-4">{{ $services->links() }}</div>

                @else
                    {{-- KỊCH BẢN 1: TRANG DỊCH VỤ TỔNG --}}
                    <h1 class="mb-4">Dịch vụ của chúng tôi</h1>

                    {{-- Lặp qua các DANH MỤC --}}
                    @foreach($serviceCategories as $cat)
                        {{-- Hiển thị tên danh mục --}}
                        <h3 class="category-group-title mt-5 mb-4">{{ $cat->name }}</h3>
                        
                        {{-- Lặp qua các DỊCH VỤ trong từng danh mục --}}
                        @forelse($cat->services as $service)
                             @include('frontend.services.service-item', ['service' => $service])
                        @empty
                            <p>Chưa có dịch vụ nào trong danh mục này.</p>
                        @endforelse
                    @endforeach

                @endif
            </div>
        </div>
    </div>
</section>
@endsection