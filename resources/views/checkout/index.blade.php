@extends('layouts.master')

@section('title', 'Thanh toán')

@section('content')
<section class="bg-gray-50 py-10 dark:bg-gray-950">
    <div class="mx-auto max-w-screen-xl px-4">
        <div class="mb-8">
            <p class="text-sm font-bold uppercase tracking-widest text-blue-700 dark:text-blue-400">Thanh toán</p>
            <h1 class="mt-2 text-2xl font-bold text-gray-950 dark:text-white md:text-3xl">Hoàn tất đơn hàng</h1>
        </div>

        @if(session('error'))
            <div class="mb-6 rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('checkout.place') }}" method="POST" id="checkout-form" novalidate>
            @csrf
            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_420px]">
                <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900 md:p-6">
                    <div class="mb-6 flex items-center justify-between border-b border-gray-100 pb-4 dark:border-gray-800">
                        <h2 class="text-lg font-bold text-gray-950 dark:text-white">Thông tin giao hàng</h2>
                        @auth('web')
                            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700 dark:bg-blue-950 dark:text-blue-300">
                                {{ auth('web')->user()->name }}
                            </span>
                        @endauth
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label for="customer_name" class="mb-2 block text-sm font-bold text-gray-700 dark:text-gray-300">Họ và tên <span class="text-red-600">*</span></label>
                            <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name', auth('web')->user()->name ?? '') }}" required class="checkout-field">
                        </div>

                        <div>
                            <label for="customer_phone" class="mb-2 block text-sm font-bold text-gray-700 dark:text-gray-300">Số điện thoại <span class="text-red-600">*</span></label>
                            <input type="tel" id="customer_phone" name="customer_phone" value="{{ old('customer_phone', auth('web')->user()->phone ?? '') }}" required class="checkout-field">
                        </div>

                        <div class="md:col-span-2">
                            <label for="customer_address" class="mb-2 block text-sm font-bold text-gray-700 dark:text-gray-300">Địa chỉ <span class="text-red-600">*</span></label>
                            <input type="text" id="customer_address" name="customer_address" value="{{ old('customer_address', auth('web')->user()->address ?? '') }}" required class="checkout-field">
                        </div>

                        <div class="md:col-span-2">
                            <label for="note" class="mb-2 block text-sm font-bold text-gray-700 dark:text-gray-300">Ghi chú đơn hàng</label>
                            <textarea id="note" name="note" rows="4" class="checkout-field min-h-28 resize-y py-3">{{ old('note') }}</textarea>
                        </div>
                    </div>

                    <div class="mt-6 rounded-xl bg-blue-50 p-4 text-sm font-medium text-blue-900 dark:bg-blue-950 dark:text-blue-100">
                        <i class="fas fa-phone-alt mr-2"></i>
                        CNETPOS sẽ liên hệ xác nhận trước khi xử lý đơn.
                    </div>
                </div>

                <aside class="h-fit rounded-2xl border border-gray-100 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <h2 class="text-lg font-bold text-gray-950 dark:text-white">Đơn hàng của anh</h2>

                    <div id="order-summary-list" class="mt-5 divide-y divide-gray-100 dark:divide-gray-800"></div>

                    <div class="mt-5 space-y-3 border-t border-gray-100 pt-5 text-sm dark:border-gray-800">
                        <div class="flex items-center justify-between text-gray-600 dark:text-gray-300">
                            <span>Tạm tính</span>
                            <span id="summary-subtotal" class="font-semibold">0đ</span>
                        </div>
                        <div class="flex items-center justify-between text-gray-600 dark:text-gray-300">
                            <span>Phí vận chuyển</span>
                            <span class="font-semibold text-green-600">Miễn phí</span>
                        </div>
                        <div class="flex items-center justify-between border-t border-gray-100 pt-4 dark:border-gray-800">
                            <span class="font-bold text-gray-950 dark:text-white">Tổng cộng</span>
                            <span id="summary-total" class="text-xl font-bold text-blue-700 dark:text-blue-400">0đ</span>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h3 class="mb-3 text-sm font-bold uppercase tracking-wide text-gray-500">Phương thức thanh toán</h3>
                        <div class="space-y-3">
                            <label class="group relative block cursor-pointer">
                                <input type="radio" name="payment_method" value="cod" class="peer sr-only" {{ old('payment_method', 'cod') === 'cod' ? 'checked' : '' }}>
                                <span class="flex items-center gap-3 rounded-xl border border-gray-200 bg-white p-3 text-sm font-semibold text-gray-700 transition-colors peer-checked:border-blue-300 peer-checked:bg-blue-50 peer-checked:text-blue-900 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-300 dark:peer-checked:border-blue-800 dark:peer-checked:bg-blue-950 dark:peer-checked:text-blue-100">
                                    <span class="flex h-5 w-5 items-center justify-center rounded-full border border-gray-300 transition-colors group-has-[:checked]:border-blue-700 dark:border-gray-600">
                                        <span class="h-2.5 w-2.5 rounded-full bg-blue-700 opacity-0 transition-opacity group-has-[:checked]:opacity-100"></span>
                                    </span>
                                    <span>Thanh toán khi nhận hàng (COD)</span>
                                </span>
                            </label>

                            <label class="group relative block cursor-pointer">
                                <input type="radio" name="payment_method" value="bank_transfer" class="peer sr-only" {{ old('payment_method') === 'bank_transfer' ? 'checked' : '' }}>
                                <span class="flex items-center gap-3 rounded-xl border border-gray-200 bg-white p-3 text-sm font-semibold text-gray-700 transition-colors peer-checked:border-blue-300 peer-checked:bg-blue-50 peer-checked:text-blue-900 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-300 dark:peer-checked:border-blue-800 dark:peer-checked:bg-blue-950 dark:peer-checked:text-blue-100">
                                    <span class="flex h-5 w-5 items-center justify-center rounded-full border border-gray-300 transition-colors group-has-[:checked]:border-blue-700 dark:border-gray-600">
                                        <span class="h-2.5 w-2.5 rounded-full bg-blue-700 opacity-0 transition-opacity group-has-[:checked]:opacity-100"></span>
                                    </span>
                                    <span>Chuyển khoản ngân hàng (VietQR)</span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="mt-6 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-blue-700 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-500/20 transition-colors hover:bg-blue-800">
                        Đặt hàng
                        <i class="fas fa-check text-xs"></i>
                    </button>
                </aside>
            </div>
        </form>
    </div>
</section>
@endsection
