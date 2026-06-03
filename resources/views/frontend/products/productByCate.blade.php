@extends('layouts.master')

@section('title', $category->name)
@section('meta_description', $category->meta_description ?? '')
@section('meta_image', $category->meta_image ?? '')

@section('content')
<x-frontend.leaderboard
    :image="$category->banner?->url ?? $pageSettings->products_banner"
    :title="$category->name"
    subline="Danh mục sản phẩm"
    :description="$category->description ?? null"
    :breadcrumb="[
        ['label' => 'Sản phẩm', 'url' => route('products.index')],
        ['label' => $category->name],
    ]"
/>

<section class="bg-white py-10 dark:bg-gray-900">
    <div class="mx-auto max-w-screen-xl px-4">
        @if($childCategories->isNotEmpty())
            <div class="mb-5 flex gap-2 overflow-x-auto pb-2">
                @foreach($childCategories as $childCategory)
                    <a href="{{ $childCategory->slug_url }}" class="shrink-0 rounded-full border border-gray-200 bg-gray-50 px-4 py-2 text-sm font-bold text-gray-700 transition-colors hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-blue-950">
                        {{ $childCategory->name }}
                    </a>
                @endforeach
            </div>
        @endif

        @include('frontend.products.partials.filter-bar', [
            'allCategories' => $allCategories,
            'allBrands' => $allBrands,
            'action' => url()->current(),
            'showCategory' => false,
        ])

        <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold text-gray-950 dark:text-white">{{ $category->name }}</h2>
                <p class="mt-1 text-sm font-medium text-gray-500">{{ $products->total() }} sản phẩm</p>
            </div>
            <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-blue-700 hover:text-blue-900 dark:text-blue-400">
                Tất cả sản phẩm
                <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>

        @if($products->isEmpty())
            <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 p-10 text-center dark:border-gray-700 dark:bg-gray-800">
                <p class="font-semibold text-gray-600 dark:text-gray-300">Chưa có sản phẩm phù hợp trong danh mục này.</p>
            </div>
        @else
            <div class="grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                @foreach ($products as $product)
                    @include('partials.frontend.product_item', ['product' => $product])
                @endforeach
            </div>

            <div class="mt-8">
                {{ $products->links('frontend.products.partials.pagination') }}
            </div>
        @endif
    </div>
</section>
@endsection
