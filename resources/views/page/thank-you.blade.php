@extends('layouts.master')

@section('title', $order ? 'Đặt hàng thành công' : 'Cảm ơn bạn đã liên hệ')
@section('meta_robots', 'noindex, nofollow')

@push('conversion_script')
    <script>
        if (typeof gtag === 'function') {
            gtag('event', 'conversion', {'send_to': 'AW-833638621/dioGCP6HwZIYEN2hwY0D'});
        }
    </script>
@endpush

@section('content')
@php
    $isBankTransfer = $order?->payment_method === 'bank_transfer';
    $qrCodeUrl = null;

    if ($isBankTransfer) {
        $bankId = '970436';
        $accountNo = '105867163975';
        $accountName = 'TRAN QUANG ANH';
        $amount = (float) $order->total_price;
        $note = 'CNETPOS ' . $order->code;
        $qrCodeUrl = "https://api.vietqr.io/image/{$bankId}-{$accountNo}-print.png?amount={$amount}&addInfo=" . urlencode($note) . '&accountName=' . urlencode($accountName);
    }
@endphp

<section class="bg-gray-50 py-12 dark:bg-gray-950">
    <div class="mx-auto max-w-screen-xl px-4">
        <div class="mx-auto max-w-3xl rounded-2xl border border-gray-100 bg-white p-6 text-center shadow-sm dark:border-gray-800 dark:bg-gray-900 md:p-10">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-green-50 text-green-600 dark:bg-green-950 dark:text-green-300">
                <i class="fas fa-check text-2xl"></i>
            </div>

            <h1 class="mt-5 text-2xl font-bold text-gray-950 dark:text-white md:text-3xl">
                {{ $order ? 'Đặt hàng thành công!' : 'Cảm ơn bạn đã gửi thông tin!' }}
            </h1>

            <p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-gray-600 dark:text-gray-300">
                {{ session('success') ?? ($order ? 'Chúng tôi đã nhận được đơn hàng và sẽ liên hệ xác nhận trong thời gian sớm nhất.' : 'Chúng tôi đã nhận được yêu cầu của bạn và sẽ liên hệ lại trong thời gian sớm nhất.') }}
            </p>

            @if($order)
                <div class="mt-6 rounded-xl border border-blue-100 bg-blue-50 p-4 text-left dark:border-blue-900 dark:bg-blue-950">
                    <div class="grid gap-3 text-sm sm:grid-cols-2">
                        <div>
                            <span class="block text-xs font-bold uppercase tracking-wide text-blue-700 dark:text-blue-300">Mã đơn hàng</span>
                            <strong class="mt-1 block text-gray-950 dark:text-white">#{{ $order->code }}</strong>
                        </div>
                        <div>
                            <span class="block text-xs font-bold uppercase tracking-wide text-blue-700 dark:text-blue-300">Tổng tiền</span>
                            <strong class="mt-1 block text-gray-950 dark:text-white">{{ number_format((float) $order->total_price) }}đ</strong>
                        </div>
                        <div>
                            <span class="block text-xs font-bold uppercase tracking-wide text-blue-700 dark:text-blue-300">Thanh toán</span>
                            <strong class="mt-1 block text-gray-950 dark:text-white">{{ $isBankTransfer ? 'Chuyển khoản ngân hàng' : 'Thanh toán khi nhận hàng' }}</strong>
                        </div>
                        <div>
                            <span class="block text-xs font-bold uppercase tracking-wide text-blue-700 dark:text-blue-300">Số điện thoại</span>
                            <strong class="mt-1 block text-gray-950 dark:text-white">{{ $order->customer_phone }}</strong>
                        </div>
                    </div>
                </div>

                @if($isBankTransfer && $qrCodeUrl)
                    <div class="mx-auto mt-6 max-w-md rounded-2xl border border-gray-100 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-950">
                        <h2 class="text-base font-bold text-gray-950 dark:text-white">Quét mã QR để thanh toán</h2>
                        <div class="mt-3 text-sm text-gray-600 dark:text-gray-300">
                            <p><strong>Nội dung:</strong> <span class="text-red-600">{{ $note }}</span></p>
                            <p><strong>Số tiền:</strong> <span class="text-red-600">{{ number_format($amount) }}đ</span></p>
                        </div>
                        <img src="{{ $qrCodeUrl }}" alt="Mã QR thanh toán" class="mx-auto mt-4 max-w-full rounded-xl">
                    </div>
                @endif
            @endif

            <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                <a href="{{ route('home') }}" class="inline-flex items-center justify-center rounded-xl bg-blue-700 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-500/20 transition-colors hover:bg-blue-800">
                    Quay về trang chủ
                </a>
                <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center rounded-xl border border-blue-200 px-5 py-3 text-sm font-bold text-blue-700 transition-colors hover:bg-blue-50 dark:border-blue-900 dark:text-blue-300 dark:hover:bg-blue-950">
                    Xem thêm sản phẩm
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
