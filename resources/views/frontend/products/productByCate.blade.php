@extends('layouts.master')
@section('meta_description', $category->meta_description ?? '')
@section('meta_image', $category->meta_image ?? '')

@section('title', $category->name)

@push('css')
    <link rel="stylesheet" href="{{ asset('css/product.css') }}">
@endpush

@section('content')
    <div class="banner">
        <img src="{{ optional($category->bannerImage())->url() }}" alt="{{ $category->name }}" class="w-100">
    </div>
    <div class="filter my-4">
        <div class="container">
            <div class="row">
                <div class="col-12 col-md-6"></div>
                <div class="col-12 col-md-6">
                    <form action="{{ route('frontend.products.search') }}" method="GET">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="q" placeholder="Recipient's username"
                            aria-label="Recipient's username" aria-describedby="basic-addon2" required>
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="product_list">
        <div class="container">
            <h2 class="custom-section-title">{{ $category->name }}</h2>
            <div class="row">
                @foreach ($products as $product)
                    <div class="col-6 col-md-4">
                        @include('partials.frontend.product_item', ['product' => $product])
                    </div>
                @endforeach
            </div>
        </div>
        <div class="container">
            {{ $products->links('pagination::bootstrap-4') }}
        </div>
    </div>
    <div class="product_feature">
        @foreach ($featuredProducts as $product)
            @include('partials.frontend.product_item', ['product' => $product])
        @endforeach
    </div>
@endsection

@push('js')
@endpush
