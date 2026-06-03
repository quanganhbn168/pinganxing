@extends('frontend.dashboard.layout')

@section('title', 'Đơn hàng của tôi')
@section('dashboard_title', 'Đơn hàng')

@section('dashboard_content')
@php
    $statusMap = [
        'pending' => ['label' => 'Chờ xác nhận', 'class' => 'bg-amber-50 text-amber-700 border-amber-200'],
        'processing' => ['label' => 'Đang xử lý', 'class' => 'bg-blue-50 text-blue-700 border-blue-200'],
        'completed' => ['label' => 'Hoàn thành', 'class' => 'bg-green-50 text-green-700 border-green-200'],
        'cancelled' => ['label' => 'Đã hủy', 'class' => 'bg-red-50 text-red-700 border-red-200'],
    ];
@endphp

<section class="rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
    <div class="border-b border-gray-200 p-5 dark:border-gray-800">
        <h2 class="text-lg font-bold text-gray-950 dark:text-white">Lịch sử đơn hàng</h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tất cả đơn hàng đã đặt bằng tài khoản này.</p>
    </div>

    @if($orders->isEmpty())
        <div class="p-8 text-center">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-lg bg-gray-100 text-gray-500 dark:bg-gray-800">
                <i class="fas fa-receipt"></i>
            </div>
            <h3 class="font-bold text-gray-950 dark:text-white">Chưa có đơn hàng</h3>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Anh có thể xem sản phẩm và đặt đơn đầu tiên ngay.</p>
            <a href="{{ route('products.index') }}" class="mt-5 inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-3 text-sm font-bold text-white hover:bg-blue-700">
                Xem sản phẩm
                <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                    <tr>
                        <th class="px-5 py-4">Mã đơn</th>
                        <th class="px-5 py-4">Ngày đặt</th>
                        <th class="px-5 py-4">Sản phẩm</th>
                        <th class="px-5 py-4">Trạng thái</th>
                        <th class="px-5 py-4 text-right">Tổng tiền</th>
                        <th class="px-5 py-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($orders as $order)
                        @php $status = $statusMap[$order->status] ?? ['label' => $order->status ?: 'Đang cập nhật', 'class' => 'bg-gray-50 text-gray-700 border-gray-200']; @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-5 py-4 font-bold text-gray-950 dark:text-white">#{{ $order->code ?? $order->id }}</td>
                            <td class="px-5 py-4 text-gray-600 dark:text-gray-300">{{ $order->created_at?->format('d/m/Y') }}</td>
                            <td class="px-5 py-4 text-gray-600 dark:text-gray-300">{{ $order->order_items_count ?? 0 }}</td>
                            <td class="px-5 py-4">
                                <span class="rounded-full border px-2.5 py-1 text-xs font-bold {{ $status['class'] }}">{{ $status['label'] }}</span>
                            </td>
                            <td class="px-5 py-4 text-right font-bold text-blue-700 dark:text-blue-400">{{ number_format((float) $order->total_price) }}đ</td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('user.order.detail', $order->id) }}" class="font-bold text-gray-700 hover:text-blue-700 dark:text-gray-200">Chi tiết</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-200 p-5 dark:border-gray-800">
            {{ $orders->links('frontend.products.partials.pagination') }}
        </div>
    @endif
</section>
@endsection
