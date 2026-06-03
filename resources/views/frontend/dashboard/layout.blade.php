@extends('layouts.master')

@section('content')
@php
    $dashboardUser = auth()->user();
    $navItems = [
        ['label' => 'Tổng quan', 'route' => 'user.dashboard', 'icon' => 'fa-chart-line'],
        ['label' => 'Hồ sơ', 'route' => 'user.profile', 'icon' => 'fa-user'],
        ['label' => 'Đơn hàng', 'route' => 'user.orders', 'icon' => 'fa-receipt'],
        ['label' => 'Yêu thích', 'route' => 'user.wishlist', 'icon' => 'fa-heart'],
    ];
@endphp

<section class="bg-gray-50 py-8 dark:bg-gray-950 md:py-12">
    <div class="mx-auto max-w-screen-xl px-4">
        <div class="mb-6 flex flex-col gap-4 rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900 md:flex-row md:items-center md:justify-between">
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-lg bg-blue-600 text-xl font-bold text-white">
                    {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($dashboardUser?->name ?? 'U', 0, 1)) }}
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Tài khoản của tôi</p>
                    <h1 class="text-2xl font-bold text-gray-950 dark:text-white">@yield('dashboard_title', 'Dashboard')</h1>
                </div>
            </div>

            <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-5 py-3 text-sm font-bold text-white transition-colors hover:bg-blue-700">
                Tiếp tục mua hàng
                <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-sm font-semibold text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-[260px_minmax(0,1fr)]">
            <aside class="lg:sticky lg:top-24 lg:self-start">
                <nav class="rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-800 dark:bg-gray-900">
                    @foreach($navItems as $item)
                        <a href="{{ route($item['route']) }}" class="mb-1 flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-bold transition-colors last:mb-0 {{ request()->routeIs($item['route']) ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700 dark:text-gray-200 dark:hover:bg-gray-800' }}">
                            <i class="fas {{ $item['icon'] }} w-5 text-center"></i>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>
            </aside>

            <main>
                @yield('dashboard_content')
            </main>
        </div>
    </div>
</section>
@endsection
