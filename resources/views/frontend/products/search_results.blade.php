@extends('layouts.master')

{{-- Tiêu đề động theo từ khóa --}}
@section('title', 'Tìm kiếm: ' . $keyword)

@push('css')
    {{-- Sử dụng lại CSS của trang sản phẩm để đồng bộ giao diện --}}
    <style>
        .search-header {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .empty-state {
            text-align: center;
            padding: 50px 0;
        }
        .empty-state i {
            font-size: 60px;
            color: #dee2e6;
            margin-bottom: 20px;
        }
    </style>
@endpush

@section('content')
    {{-- Banner chung (Hoặc banner mặc định của web) --}}
    <div class="banner">
        {{-- Nếu anh có truyền $pageBanner từ controller thì dùng, không thì dùng banner mặc định của setting --}}
        <img src="{{ asset($setting->banner ?? 'images/default-banner.jpg') }}" 
             alt="Kết quả tìm kiếm" 
             style="width: 100%; max-height: 300px; object-fit: cover;">
    </div>

    <div class="container py-5">
        
        {{-- KHU VỰC TÌM KIẾM LẠI --}}
        <div class="row justify-content-center mb-5">
            <div class="col-md-8">
                <form action="{{ route('frontend.products.search') }}" method="GET">
                    <div class="input-group input-group-lg">
                        <input type="text" class="form-control" 
                               placeholder="Nhập tên sản phẩm cần tìm..." 
                               name="q" 
                               value="{{ $keyword }}">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i> Tìm kiếm
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- KẾT QUẢ TÌM KIẾM --}}
        <div class="search-results">
            @if($products->isNotEmpty())
                <div class="search-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        Kết quả tìm kiếm cho: <strong class="text-primary">"{{ $keyword }}"</strong>
                    </h4>
                    <span class="badge badge-secondary p-2">
                        Tìm thấy {{ $products->total() }} sản phẩm
                    </span>
                </div>

                <div class="row">
                    @foreach($products as $product)
                        <div class="col-6 col-md-3 mb-4">
                            {{-- Sử dụng lại partial item sản phẩm có sẵn của anh --}}
                            @include('partials.frontend.product_item', ['product' => $product])
                        </div>
                    @endforeach
                </div>

                {{-- PHÂN TRANG --}}
                <div class="d-flex justify-content-center mt-4">
                    {{-- Quan trọng: withQueryString() giúp giữ lại ?q=abc khi sang trang 2 --}}
                    {{ $products->withQueryString()->links() }}
                </div>

            @else
                {{-- TRƯỜNG HỢP KHÔNG TÌM THẤY --}}
                <div class="empty-state">
                    <i class="fa-solid fa-magnifying-glass-minus"></i>
                    <h3>Không tìm thấy sản phẩm nào</h3>
                    <p class="text-muted">
                        Rất tiếc, chúng tôi không tìm thấy sản phẩm nào phù hợp với từ khóa 
                        <strong>"{{ $keyword }}"</strong>.
                    </p>
                    <div class="mt-4">
                        <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
                            <i class="fa-solid fa-arrow-left"></i> Xem tất cả sản phẩm
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection