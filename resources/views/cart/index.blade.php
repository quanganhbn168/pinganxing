@extends('layouts.master')

@section('title', 'Giỏ hàng của bạn')

@section('content')
<section class="bg-gray-50 py-10 dark:bg-gray-950">
    <div class="mx-auto max-w-screen-xl px-4">
        <div class="mb-8 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-widest text-blue-700 dark:text-blue-400">Giỏ hàng</p>
                <h1 class="mt-2 text-2xl font-bold text-gray-950 dark:text-white md:text-3xl">Sản phẩm đã chọn</h1>
            </div>
            <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 transition-colors hover:text-blue-700 dark:text-gray-300">
                <i class="fas fa-arrow-left text-xs"></i>
                Tiếp tục xem sản phẩm
            </a>
        </div>

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
            <div class="rounded-2xl border border-gray-100 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-800">
                    <div class="grid grid-cols-[1fr_120px_140px_120px_44px] items-center gap-4 text-xs font-bold uppercase tracking-wide text-gray-500 max-lg:hidden">
                        <span>Sản phẩm</span>
                        <span class="text-right">Đơn giá</span>
                        <span class="text-center">Số lượng</span>
                        <span class="text-right">Tạm tính</span>
                        <span></span>
                    </div>
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 lg:hidden">Danh sách sản phẩm</p>
                </div>

                <div id="cart-items-container" class="divide-y divide-gray-100 dark:divide-gray-800"></div>
            </div>

            <aside class="h-fit rounded-2xl border border-gray-100 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <h2 class="text-lg font-bold text-gray-950 dark:text-white">Tóm tắt đơn hàng</h2>
                <div class="mt-5 space-y-3 text-sm">
                    <div class="flex items-center justify-between text-gray-600 dark:text-gray-300">
                        <span>Số lượng</span>
                        <span id="summary-quantity" class="font-semibold">0</span>
                    </div>
                    <div class="flex items-center justify-between text-gray-600 dark:text-gray-300">
                        <span>Tạm tính</span>
                        <span id="summary-subtotal" class="font-semibold">0đ</span>
                    </div>
                    <div class="flex items-center justify-between text-gray-600 dark:text-gray-300">
                        <span>Phí vận chuyển</span>
                        <span class="font-semibold text-green-600">Miễn phí</span>
                    </div>
                    <div class="border-t border-gray-100 pt-4 dark:border-gray-800">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-gray-950 dark:text-white">Tổng cộng</span>
                            <span id="summary-total" class="text-xl font-bold text-blue-700 dark:text-blue-400">0đ</span>
                        </div>
                    </div>
                </div>

                <a id="checkout-link" href="{{ route('checkout.index') }}" class="mt-6 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-blue-700 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-500/20 transition-colors hover:bg-blue-800">
                    Thanh toán
                    <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </aside>
        </div>
    </div>
</section>

<template id="cart-item-template">
    <div class="cart-item-row grid gap-4 p-5 lg:grid-cols-[1fr_120px_140px_120px_44px] lg:items-center" data-id="__ID__">
        <div class="flex min-w-0 gap-4">
            <a href="__URL__" class="h-24 w-24 shrink-0 rounded-xl border border-gray-100 bg-gray-50 p-2 dark:border-gray-800 dark:bg-gray-800">
                <img src="__IMAGE__" class="h-full w-full object-contain" alt="__NAME__">
            </a>
            <div class="min-w-0">
                <a href="__URL__" class="line-clamp-2 text-sm font-bold text-gray-950 transition-colors hover:text-blue-700 dark:text-white dark:hover:text-blue-300">__NAME__</a>
                <div class="mt-2 flex flex-wrap gap-2 text-xs font-semibold">
                    <span class="rounded-full bg-gray-100 px-2.5 py-1 text-gray-600 dark:bg-gray-800 dark:text-gray-300">__PRODUCT_TYPE__</span>
                    <span class="variant-pill rounded-full bg-blue-50 px-2.5 py-1 text-blue-700 dark:bg-blue-950 dark:text-blue-300">__VARIANT__</span>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between lg:block lg:text-right">
            <span class="text-xs font-bold uppercase text-gray-400 lg:hidden">Đơn giá</span>
            <span class="price-per-item text-sm font-bold text-gray-900 dark:text-white" data-price="__PRICE_RAW__">__PRICE__</span>
        </div>

        <div class="flex items-center justify-between lg:justify-center">
            <span class="text-xs font-bold uppercase text-gray-400 lg:hidden">Số lượng</span>
            <div class="inline-flex h-10 items-center overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700">
                <button class="btn-minus h-full w-10 text-gray-600 transition-colors hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800" type="button">-</button>
                <input type="number" class="quantity-input h-full w-14 border-0 bg-transparent text-center text-sm font-bold text-gray-900 focus:ring-0 dark:text-white" value="__QUANTITY__" min="0">
                <button class="btn-plus h-full w-10 text-gray-600 transition-colors hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800" type="button">+</button>
            </div>
        </div>

        <div class="flex items-center justify-between lg:block lg:text-right">
            <span class="text-xs font-bold uppercase text-gray-400 lg:hidden">Tạm tính</span>
            <span class="item-subtotal text-sm font-bold text-blue-700 dark:text-blue-400">__SUBTOTAL__</span>
        </div>

        <div class="flex justify-end">
            <button type="button" class="remove-item-btn inline-flex h-10 w-10 items-center justify-center rounded-full text-gray-400 transition-colors hover:bg-red-50 hover:text-red-600" aria-label="Xóa sản phẩm">
                <i class="fas fa-trash-alt text-sm"></i>
            </button>
        </div>
    </div>
</template>
@endsection
