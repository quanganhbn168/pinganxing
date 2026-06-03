@extends('layouts.master')

@section('title', $pageSettings->agency_title ?: 'Hợp tác Đại lý & Đối tác')
@section('meta_description', $pageSettings->agency_description ?: 'Đăng ký trở thành đại lý, đối tác phân phối giải pháp của CNETPOS.')
@section('meta_image', $pageSettings->agency_banner ?: ($setting->banner ?? asset('images/setting/cover01.jpg')))

@section('content')
@php
    $agencyTitle = $pageSettings->agency_title ?: 'Hợp tác Đại lý';
    $agencyDescription = $pageSettings->agency_description
        ?: 'Cùng mở rộng hệ sinh thái thiết bị, phần mềm và giải pháp vận hành cho doanh nghiệp.';

    $benefits = [
        ['icon' => 'fa-percent', 'title' => 'Chính sách giá rõ ràng', 'text' => 'Bảng giá đại lý, chiết khấu theo cấp và cơ chế bảo vệ khu vực minh bạch.'],
        ['icon' => 'fa-boxes', 'title' => 'Nguồn hàng ổn định', 'text' => 'Danh mục sản phẩm đa dạng, cập nhật nhanh, hỗ trợ tư vấn cấu hình theo nhu cầu khách hàng.'],
        ['icon' => 'fa-tools', 'title' => 'Hỗ trợ kỹ thuật', 'text' => 'Đồng hành trước và sau bán: cài đặt, bảo hành, xử lý sự cố và đào tạo sản phẩm.'],
        ['icon' => 'fa-bullhorn', 'title' => 'Hỗ trợ bán hàng', 'text' => 'Cung cấp tài liệu, hình ảnh, catalogue và phối hợp tư vấn các đơn hàng cần chuyên môn.'],
    ];

    $steps = [
        ['title' => 'Gửi thông tin', 'text' => 'Điền form đăng ký với khu vực, mô hình kinh doanh và nhóm sản phẩm quan tâm.'],
        ['title' => 'Tư vấn chính sách', 'text' => 'Đội ngũ phụ trách liên hệ, trao đổi điều kiện hợp tác và định hướng bán hàng.'],
        ['title' => 'Kích hoạt hợp tác', 'text' => 'Hoàn tất hồ sơ, nhận bảng giá đại lý và bắt đầu triển khai đơn hàng đầu tiên.'],
    ];
@endphp

<x-frontend.leaderboard
    :image="$pageSettings->agency_banner ?: ($setting->banner ?? null)"
    :title="$agencyTitle"
    :subline="$pageSettings->agency_leaderboard_subline ?: 'Đại lý & đối tác phân phối'"
    :description="$pageSettings->agency_leaderboard_description ?: ($pageSettings->agency_headline ?: $agencyDescription)"
    :breadcrumb="[['label' => $agencyTitle]]"
    :actions="$pageSettings->agency_leaderboard_actions"
    :stats="$pageSettings->agency_leaderboard_stats"
/>

<section class="bg-white py-12 dark:bg-gray-900 md:py-16">
    <div class="mx-auto max-w-screen-xl px-4">
        <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_420px] lg:items-start">
            <div>
                <div class="mb-8 max-w-3xl">
                    <span class="text-sm font-bold uppercase tracking-wide text-blue-700 dark:text-blue-400">Chương trình hợp tác</span>
                    <h2 class="mt-3 text-2xl font-bold text-gray-950 dark:text-white md:text-3xl">Mở rộng kinh doanh cùng {{ $setting->site_name ?? 'CNETPOS' }}</h2>
                    <p class="mt-4 text-base leading-7 text-gray-600 dark:text-gray-300">{{ $agencyDescription }}</p>
                </div>

                @if($pageSettings->agency_content)
                    <div class="prose prose-gray mb-8 max-w-none dark:prose-invert">
                        {!! $pageSettings->agency_content !!}
                    </div>
                @endif

                <div class="grid gap-4 sm:grid-cols-2">
                    @foreach($benefits as $benefit)
                        <article class="rounded-lg border border-gray-200 bg-gray-50 p-5 dark:border-gray-700 dark:bg-gray-800">
                            <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-lg bg-blue-600 text-white">
                                <i class="fas {{ $benefit['icon'] }}"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-950 dark:text-white">{{ $benefit['title'] }}</h3>
                            <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300">{{ $benefit['text'] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>

            <aside class="rounded-lg border border-gray-200 bg-gray-50 p-6 dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-start gap-4">
                    <img src="{{ asset('images/setting/bat-tay.png') }}" onerror="this.src='https://placehold.co/120x120/png?text=Partner'" alt="Hợp tác đại lý" class="h-20 w-20 shrink-0 rounded-lg object-contain">
                    <div>
                        <h3 class="text-lg font-bold text-gray-950 dark:text-white">Phù hợp với</h3>
                        <p class="mt-1 text-sm leading-6 text-gray-600 dark:text-gray-300">Cửa hàng thiết bị, đơn vị triển khai POS, công ty tích hợp hệ thống và đối tác có tệp khách hàng doanh nghiệp.</p>
                    </div>
                </div>

                <div class="mt-6 space-y-3 border-t border-gray-200 pt-6 dark:border-gray-700">
                    <div class="flex items-center gap-3 text-sm font-semibold text-gray-700 dark:text-gray-200">
                        <i class="fas fa-check text-blue-600"></i>
                        Hỗ trợ báo giá theo dự án
                    </div>
                    <div class="flex items-center gap-3 text-sm font-semibold text-gray-700 dark:text-gray-200">
                        <i class="fas fa-check text-blue-600"></i>
                        Tư vấn sản phẩm và cấu hình
                    </div>
                    <div class="flex items-center gap-3 text-sm font-semibold text-gray-700 dark:text-gray-200">
                        <i class="fas fa-check text-blue-600"></i>
                        Đồng hành xử lý sau bán
                    </div>
                </div>

                @if($setting?->phone || $setting?->email)
                    <div class="mt-6 rounded-lg bg-white p-4 text-sm dark:bg-gray-900">
                        @if($setting?->phone)
                            <a href="tel:{{ preg_replace('/\s+/', '', $setting->phone) }}" class="mb-2 flex items-center gap-3 font-bold text-gray-950 hover:text-blue-700 dark:text-white">
                                <i class="fas fa-phone text-blue-600"></i>
                                {{ $setting->phone }}
                            </a>
                        @endif
                        @if($setting?->email)
                            <a href="mailto:{{ $setting->email }}" class="flex items-center gap-3 font-bold text-gray-950 hover:text-blue-700 dark:text-white">
                                <i class="fas fa-envelope text-blue-600"></i>
                                {{ $setting->email }}
                            </a>
                        @endif
                    </div>
                @endif
            </aside>
        </div>
    </div>
</section>

<section class="border-y border-gray-200 bg-gray-50 py-12 dark:border-gray-800 dark:bg-gray-950">
    <div class="mx-auto max-w-screen-xl px-4">
        <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div>
                <span class="text-sm font-bold uppercase tracking-wide text-blue-700 dark:text-blue-400">Quy trình</span>
                <h2 class="mt-3 text-2xl font-bold text-gray-950 dark:text-white md:text-3xl">Bắt đầu hợp tác trong 3 bước</h2>
            </div>
            <a href="#agency-form" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-3 text-sm font-bold text-white transition-colors hover:bg-blue-700">
                Đăng ký ngay
                <i class="fas fa-arrow-down text-xs"></i>
            </a>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            @foreach($steps as $index => $step)
                <article class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">
                    <div class="mb-5 flex h-10 w-10 items-center justify-center rounded-lg bg-gray-950 text-sm font-bold text-white dark:bg-blue-600">
                        {{ $index + 1 }}
                    </div>
                    <h3 class="text-lg font-bold text-gray-950 dark:text-white">{{ $step['title'] }}</h3>
                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300">{{ $step['text'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section id="agency-form" class="bg-white py-12 dark:bg-gray-900 md:py-16">
    <div class="mx-auto grid max-w-screen-xl gap-8 px-4 lg:grid-cols-[360px_minmax(0,1fr)]">
        <div>
            <span class="text-sm font-bold uppercase tracking-wide text-blue-700 dark:text-blue-400">Đăng ký đại lý</span>
            <h2 class="mt-3 text-2xl font-bold text-gray-950 dark:text-white md:text-3xl">Để lại thông tin, đội ngũ phụ trách sẽ liên hệ</h2>
            <p class="mt-4 text-base leading-7 text-gray-600 dark:text-gray-300">Thông tin càng cụ thể thì chính sách và nhóm sản phẩm tư vấn càng sát với mô hình kinh doanh của anh/chị.</p>
        </div>

        <form action="{{ route('agency.store') }}" method="POST" class="rounded-lg border border-gray-200 bg-gray-50 p-5 dark:border-gray-700 dark:bg-gray-800 md:p-8">
            @csrf

            @if ($errors->any())
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-700">
                    Vui lòng kiểm tra lại các thông tin bắt buộc.
                </div>
            @endif

            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label for="agency-name" class="mb-2 block text-sm font-bold text-gray-900 dark:text-white">Họ và tên <span class="text-red-500">*</span></label>
                    <input id="agency-name" type="text" name="name" value="{{ old('name') }}" required placeholder="Nguyễn Văn A" class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 outline-none transition-colors focus:border-blue-600 focus:ring-2 focus:ring-blue-100 dark:border-gray-600 dark:bg-gray-900 dark:text-white dark:focus:ring-blue-950">
                    @error('name') <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="agency-phone" class="mb-2 block text-sm font-bold text-gray-900 dark:text-white">Số điện thoại <span class="text-red-500">*</span></label>
                    <input id="agency-phone" type="tel" name="phone" value="{{ old('phone') }}" required placeholder="0987654321" class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 outline-none transition-colors focus:border-blue-600 focus:ring-2 focus:ring-blue-100 dark:border-gray-600 dark:bg-gray-900 dark:text-white dark:focus:ring-blue-950">
                    @error('phone') <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="agency-email" class="mb-2 block text-sm font-bold text-gray-900 dark:text-white">Email</label>
                    <input id="agency-email" type="email" name="email" value="{{ old('email') }}" placeholder="email@congty.vn" class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 outline-none transition-colors focus:border-blue-600 focus:ring-2 focus:ring-blue-100 dark:border-gray-600 dark:bg-gray-900 dark:text-white dark:focus:ring-blue-950">
                    @error('email') <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="agency-shop" class="mb-2 block text-sm font-bold text-gray-900 dark:text-white">Tên cửa hàng / công ty</label>
                    <input id="agency-shop" type="text" name="shop_name" value="{{ old('shop_name') }}" placeholder="Công ty TNHH ABC" class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 outline-none transition-colors focus:border-blue-600 focus:ring-2 focus:ring-blue-100 dark:border-gray-600 dark:bg-gray-900 dark:text-white dark:focus:ring-blue-950">
                    @error('shop_name') <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="agency-area" class="mb-2 block text-sm font-bold text-gray-900 dark:text-white">Khu vực đăng ký</label>
                    <input id="agency-area" type="text" name="area" value="{{ old('area') }}" placeholder="Hà Nội, TP.HCM..." class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 outline-none transition-colors focus:border-blue-600 focus:ring-2 focus:ring-blue-100 dark:border-gray-600 dark:bg-gray-900 dark:text-white dark:focus:ring-blue-950">
                    @error('area') <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="agency-address" class="mb-2 block text-sm font-bold text-gray-900 dark:text-white">Địa chỉ</label>
                    <input id="agency-address" type="text" name="address" value="{{ old('address') }}" placeholder="Số nhà, đường, quận/huyện..." class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 outline-none transition-colors focus:border-blue-600 focus:ring-2 focus:ring-blue-100 dark:border-gray-600 dark:bg-gray-900 dark:text-white dark:focus:ring-blue-950">
                    @error('address') <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-5">
                <label for="agency-details" class="mb-2 block text-sm font-bold text-gray-900 dark:text-white">Nhu cầu hợp tác</label>
                <textarea id="agency-details" name="details" rows="5" placeholder="Nhóm sản phẩm quan tâm, kinh nghiệm triển khai, tệp khách hàng hiện có..." class="block w-full resize-none rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 outline-none transition-colors focus:border-blue-600 focus:ring-2 focus:ring-blue-100 dark:border-gray-600 dark:bg-gray-900 dark:text-white dark:focus:ring-blue-950">{{ old('details') }}</textarea>
                @error('details') <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="mt-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm leading-6 text-gray-500 dark:text-gray-400">Thông tin được dùng để tư vấn chính sách hợp tác và không hiển thị công khai.</p>
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-6 py-3 text-sm font-bold text-white transition-colors hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-100 dark:focus:ring-blue-950">
                    Gửi đăng ký
                    <i class="fas fa-paper-plane text-xs"></i>
                </button>
            </div>
        </form>
    </div>
</section>

<x-frontend.page-cta
    :title="$pageSettings->agency_cta_title"
    :description="$pageSettings->agency_cta_description"
    :link="$pageSettings->agency_cta_link"
/>
@endsection
