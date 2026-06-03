@extends('frontend.dashboard.layout')

@section('title', 'Sản phẩm yêu thích')
@section('dashboard_title', 'Yêu thích')

@section('dashboard_content')
<section class="rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900 md:p-6">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-lg font-bold text-gray-950 dark:text-white">Sản phẩm yêu thích</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $products->total() }} sản phẩm đã lưu.</p>
        </div>
        <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-bold text-gray-700 hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">
            Xem thêm sản phẩm
            <i class="fas fa-arrow-right text-xs"></i>
        </a>
    </div>

    @if($products->isEmpty())
        <div class="rounded-lg border border-dashed border-gray-200 bg-gray-50 p-8 text-center dark:border-gray-700 dark:bg-gray-800">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-lg bg-white text-gray-500 dark:bg-gray-900">
                <i class="far fa-heart"></i>
            </div>
            <h3 class="font-bold text-gray-950 dark:text-white">Chưa có sản phẩm yêu thích</h3>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Anh có thể lưu sản phẩm để quay lại xem nhanh hơn.</p>
        </div>
    @else
        <div class="grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-3 xl:grid-cols-4">
            @foreach($products as $product)
                @include('partials.frontend.product_item', ['product' => $product])
            @endforeach
        </div>

        <div class="mt-8">
            {{ $products->links('frontend.products.partials.pagination') }}
        </div>
    @endif
</section>
@endsection
