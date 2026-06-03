@extends('frontend.dashboard.layout')

@section('title', 'Dashboard')
@section('dashboard_title', 'Tổng quan')

@section('dashboard_content')
@php
    $statusMap = [
        'pending' => ['label' => 'Chờ xác nhận', 'class' => 'bg-amber-50 text-amber-700 border-amber-200'],
        'processing' => ['label' => 'Đang xử lý', 'class' => 'bg-blue-50 text-blue-700 border-blue-200'],
        'completed' => ['label' => 'Hoàn thành', 'class' => 'bg-green-50 text-green-700 border-green-200'],
        'cancelled' => ['label' => 'Đã hủy', 'class' => 'bg-red-50 text-red-700 border-red-200'],
    ];

    $cards = [
        ['label' => 'Tổng đơn', 'value' => number_format($orderStats['total'] ?? 0), 'icon' => 'fa-receipt'],
        ['label' => 'Đang xử lý', 'value' => number_format(($orderStats['pending'] ?? 0) + ($orderStats['processing'] ?? 0)), 'icon' => 'fa-clock'],
        ['label' => 'Đã hoàn thành', 'value' => number_format($orderStats['completed'] ?? 0), 'icon' => 'fa-check-circle'],
        ['label' => 'Đã mua', 'value' => number_format((float) ($orderStats['total_spent'] ?? 0)) . 'đ', 'icon' => 'fa-wallet'],
    ];
@endphp

<div class="space-y-6">
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach($cards as $card)
            <article class="rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-lg bg-blue-50 text-blue-700 dark:bg-blue-950 dark:text-blue-300">
                    <i class="fas {{ $card['icon'] }}"></i>
                </div>
                <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">{{ $card['label'] }}</p>
                <strong class="mt-2 block text-2xl font-bold text-gray-950 dark:text-white">{{ $card['value'] }}</strong>
            </article>
        @endforeach
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_320px]">
        <section class="rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 p-5 dark:border-gray-800">
                <div>
                    <h2 class="text-lg font-bold text-gray-950 dark:text-white">Đơn hàng gần đây</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Theo dõi nhanh các đơn mới nhất của anh.</p>
                </div>
                <a href="{{ route('user.orders') }}" class="text-sm font-bold text-blue-700 hover:text-blue-900 dark:text-blue-400">Xem tất cả</a>
            </div>

            @if($recentOrders->isEmpty())
                <div class="p-8 text-center">
                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-lg bg-gray-100 text-gray-500 dark:bg-gray-800">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <h3 class="font-bold text-gray-950 dark:text-white">Chưa có đơn hàng</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Khi anh đặt hàng, lịch sử đơn sẽ xuất hiện tại đây.</p>
                    <a href="{{ route('products.index') }}" class="mt-5 inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-3 text-sm font-bold text-white hover:bg-blue-700">
                        Xem sản phẩm
                        <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
            @else
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($recentOrders as $order)
                        @php $status = $statusMap[$order->status] ?? ['label' => $order->status ?: 'Đang cập nhật', 'class' => 'bg-gray-50 text-gray-700 border-gray-200']; @endphp
                        <a href="{{ route('user.order.detail', $order->id) }}" class="block p-5 transition-colors hover:bg-gray-50 dark:hover:bg-gray-800">
                            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <strong class="text-gray-950 dark:text-white">#{{ $order->code ?? $order->id }}</strong>
                                        <span class="rounded-full border px-2.5 py-1 text-xs font-bold {{ $status['class'] }}">{{ $status['label'] }}</span>
                                    </div>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $order->created_at?->format('d/m/Y H:i') }} · {{ $order->orderItems->count() }} sản phẩm
                                    </p>
                                </div>
                                <div class="text-left md:text-right">
                                    <strong class="text-lg text-blue-700 dark:text-blue-400">{{ number_format((float) $order->total_price) }}đ</strong>
                                    <p class="mt-1 text-xs font-semibold uppercase text-gray-400">{{ $order->payment_method === 'bank_transfer' ? 'Chuyển khoản' : 'COD' }}</p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>

        <aside class="space-y-4">
            <a href="{{ route('cart.page') }}" class="flex items-center justify-between rounded-lg border border-gray-200 bg-white p-5 transition-colors hover:border-blue-200 hover:bg-blue-50 dark:border-gray-800 dark:bg-gray-900 dark:hover:bg-gray-800">
                <span>
                    <span class="block text-sm font-semibold text-gray-500 dark:text-gray-400">Giỏ hàng</span>
                    <strong class="mt-1 block text-xl text-gray-950 dark:text-white">{{ number_format($cartCount ?? 0) }} sản phẩm</strong>
                </span>
                <i class="fas fa-shopping-cart text-blue-600"></i>
            </a>
            <a href="{{ route('user.wishlist') }}" class="flex items-center justify-between rounded-lg border border-gray-200 bg-white p-5 transition-colors hover:border-blue-200 hover:bg-blue-50 dark:border-gray-800 dark:bg-gray-900 dark:hover:bg-gray-800">
                <span>
                    <span class="block text-sm font-semibold text-gray-500 dark:text-gray-400">Yêu thích</span>
                    <strong class="mt-1 block text-xl text-gray-950 dark:text-white">{{ number_format($wishlistCount ?? 0) }} sản phẩm</strong>
                </span>
                <i class="fas fa-heart text-blue-600"></i>
            </a>
            <a href="{{ route('user.profile') }}" class="flex items-center justify-between rounded-lg border border-gray-200 bg-white p-5 transition-colors hover:border-blue-200 hover:bg-blue-50 dark:border-gray-800 dark:bg-gray-900 dark:hover:bg-gray-800">
                <span>
                    <span class="block text-sm font-semibold text-gray-500 dark:text-gray-400">Thông tin</span>
                    <strong class="mt-1 block text-xl text-gray-950 dark:text-white">Cập nhật hồ sơ</strong>
                </span>
                <i class="fas fa-user-cog text-blue-600"></i>
            </a>
        </aside>
    </div>
</div>
@endsection
