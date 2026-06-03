@extends('frontend.dashboard.layout')

@section('title', 'Chi tiết đơn hàng')
@section('dashboard_title', 'Chi tiết đơn hàng')

@section('dashboard_content')
@php
    $statusMap = [
        'pending' => ['label' => 'Chờ xác nhận', 'class' => 'bg-amber-50 text-amber-700 border-amber-200'],
        'processing' => ['label' => 'Đang xử lý', 'class' => 'bg-blue-50 text-blue-700 border-blue-200'],
        'completed' => ['label' => 'Hoàn thành', 'class' => 'bg-green-50 text-green-700 border-green-200'],
        'cancelled' => ['label' => 'Đã hủy', 'class' => 'bg-red-50 text-red-700 border-red-200'],
    ];
    $status = $statusMap[$order->status] ?? ['label' => $order->status ?: 'Đang cập nhật', 'class' => 'bg-gray-50 text-gray-700 border-gray-200'];
@endphp

<div class="space-y-6">
    <div class="rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <a href="{{ route('user.orders') }}" class="mb-3 inline-flex items-center gap-2 text-sm font-bold text-blue-700 hover:text-blue-900 dark:text-blue-400">
                    <i class="fas fa-arrow-left text-xs"></i>
                    Quay lại đơn hàng
                </a>
                <h2 class="text-xl font-bold text-gray-950 dark:text-white">Đơn #{{ $order->code ?? $order->id }}</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Đặt lúc {{ $order->created_at?->format('d/m/Y H:i') }}</p>
            </div>
            <span class="w-fit rounded-full border px-3 py-1.5 text-sm font-bold {{ $status['class'] }}">{{ $status['label'] }}</span>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_340px]">
        <section class="rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="border-b border-gray-200 p-5 dark:border-gray-800">
                <h3 class="font-bold text-gray-950 dark:text-white">Sản phẩm trong đơn</h3>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-800">
                @foreach($order->orderItems as $item)
                    <div class="p-5">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <h4 class="font-bold text-gray-950 dark:text-white">{{ $item->product_name }}</h4>
                                @if($item->variant?->sku)
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">SKU biến thể: {{ $item->variant->sku }}</p>
                                @endif
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                                    {{ number_format((float) $item->product_price) }}đ x {{ $item->quantity }}
                                </p>
                            </div>
                            <strong class="text-blue-700 dark:text-blue-400">{{ number_format((float) $item->subtotal) }}đ</strong>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <aside class="space-y-6">
            <section class="rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="font-bold text-gray-950 dark:text-white">Thông tin nhận hàng</h3>
                <dl class="mt-4 space-y-3 text-sm">
                    <div>
                        <dt class="font-semibold text-gray-500 dark:text-gray-400">Người nhận</dt>
                        <dd class="mt-1 font-bold text-gray-950 dark:text-white">{{ $order->customer_name }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-gray-500 dark:text-gray-400">Số điện thoại</dt>
                        <dd class="mt-1 text-gray-700 dark:text-gray-200">{{ $order->customer_phone }}</dd>
                    </div>
                    @if($order->customer_email)
                        <div>
                            <dt class="font-semibold text-gray-500 dark:text-gray-400">Email</dt>
                            <dd class="mt-1 text-gray-700 dark:text-gray-200">{{ $order->customer_email }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt class="font-semibold text-gray-500 dark:text-gray-400">Địa chỉ</dt>
                        <dd class="mt-1 text-gray-700 dark:text-gray-200">{{ $order->customer_address }}</dd>
                    </div>
                </dl>
            </section>

            <section class="rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="font-bold text-gray-950 dark:text-white">Thanh toán</h3>
                <div class="mt-4 flex items-center justify-between text-sm text-gray-600 dark:text-gray-300">
                    <span>Phương thức</span>
                    <strong class="text-gray-950 dark:text-white">{{ $order->payment_method === 'bank_transfer' ? 'Chuyển khoản' : 'COD' }}</strong>
                </div>
                <div class="mt-4 flex items-center justify-between border-t border-gray-100 pt-4 dark:border-gray-800">
                    <span class="font-bold text-gray-950 dark:text-white">Tổng tiền</span>
                    <strong class="text-xl text-blue-700 dark:text-blue-400">{{ number_format((float) $order->total_price) }}đ</strong>
                </div>
                @if($order->note)
                    <p class="mt-4 rounded-lg bg-gray-50 p-3 text-sm text-gray-600 dark:bg-gray-800 dark:text-gray-300">{{ $order->note }}</p>
                @endif
            </section>
        </aside>
    </div>
</div>
@endsection
