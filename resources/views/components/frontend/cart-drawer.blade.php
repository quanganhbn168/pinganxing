<div
    class="cart-drawer-overlay fixed inset-0 z-[120] hidden bg-gray-950/50 opacity-0 transition-opacity duration-200"
    data-cart-drawer-close
    aria-hidden="true"
></div>

<aside
    id="cart-drawer"
    class="cart-offcanvas-wrapper fixed inset-y-0 right-0 z-[121] flex w-full max-w-sm translate-x-full flex-col bg-white shadow-2xl transition-transform duration-300 dark:bg-gray-900"
    tabindex="-1"
    aria-labelledby="cart-drawer-title"
    aria-hidden="true"
>
    <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4 dark:border-gray-800">
        <div>
            <h2 id="cart-drawer-title" class="text-base font-bold uppercase tracking-wide text-gray-900 dark:text-white">
                Giỏ hàng
            </h2>
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">
                Sản phẩm vừa thêm của anh ở đây.
            </p>
        </div>

        <button
            type="button"
            class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-gray-200 text-gray-500 transition-colors hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800"
            data-cart-drawer-close
            aria-label="Đóng giỏ hàng"
        >
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="offcanvas-body flex-1 overflow-y-auto px-5 py-4">
        <p class="p-4 text-center text-sm text-gray-500">Đang tải giỏ hàng...</p>
    </div>

    <div class="offcanvas-footer border-t border-gray-100 px-5 py-4 dark:border-gray-800">
        <div class="mb-4 flex items-center justify-between text-sm font-semibold text-gray-600 dark:text-gray-300">
            <span>Tạm tính</span>
            <span class="total-price text-lg font-bold text-blue-700 dark:text-blue-400">0đ</span>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <a
                href="{{ route('cart.page') }}"
                class="inline-flex items-center justify-center rounded-xl border border-blue-200 px-4 py-3 text-sm font-bold text-blue-700 transition-colors hover:bg-blue-50 dark:border-blue-900 dark:text-blue-300 dark:hover:bg-blue-950"
            >
                Xem giỏ hàng
            </a>
            <a
                href="{{ route('checkout.index') }}"
                class="inline-flex items-center justify-center rounded-xl bg-blue-700 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-blue-500/20 transition-colors hover:bg-blue-800"
            >
                Thanh toán
            </a>
        </div>
    </div>
</aside>

<template id="guest-cart-item-template">
    <div class="cart-drawer-item flex gap-3 border-b border-gray-100 py-4 last:border-b-0 dark:border-gray-800">
        <a href="__URL__" class="h-20 w-20 shrink-0 rounded-lg border border-gray-100 bg-gray-50 p-2 dark:border-gray-800 dark:bg-gray-800">
            <img src="__IMAGE__" alt="__NAME__" class="h-full w-full object-contain">
        </a>
        <div class="min-w-0 flex-1">
            <a href="__URL__" class="line-clamp-2 text-sm font-semibold text-gray-900 hover:text-blue-700 dark:text-white dark:hover:text-blue-300">
                __NAME__
            </a>
            <div class="mt-1 text-xs text-gray-500">__VARIANT__</div>
            <div class="mt-2 flex items-center justify-between gap-2">
                <span class="text-sm font-bold text-blue-700 dark:text-blue-400">__PRICE__đ</span>
                <span class="text-xs font-semibold text-gray-500">x __QUANTITY__</span>
            </div>
        </div>
        <button type="button" class="item-remove h-8 w-8 shrink-0 rounded-full text-gray-400 transition-colors hover:bg-red-50 hover:text-red-600" data-item-id="__ID__" aria-label="Xóa sản phẩm">
            <i class="fas fa-trash-alt text-xs"></i>
        </button>
    </div>
</template>
