@extends('layouts.master')
@section('title', $tour->name)
@php
    $resolveTourMedia = function ($media): ?string {
        if (! $media) {
            return null;
        }

        $path = trim((string) ($media->path ?? ''));

        if ($path === '' || preg_match('~(?:picsum\.photos|placehold\.co|images\.unsplash\.com)~i', $path)) {
            return null;
        }

        return filter_var($path, FILTER_VALIDATE_URL) ? $path : $media->url;
    };

    $tourImageUrl = $resolveTourMedia($tour->image)
        ?: $resolveTourMedia($tour->category?->image);
@endphp
@section('content')
    <div class="bg-white min-h-screen pt-24 pb-12">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">
            <!-- Breadcrumbs -->
            <div class="text-sm text-slate-500 mb-6">
                <a href="{{ route('home') }}" class="hover:text-primary">Trang chủ</a>
                <span class="mx-2">/</span>
                <a href="{{ route('frontend.tours.index') }}" class="hover:text-primary">Tour du lịch</a>
                <span class="mx-2">/</span>
                <a href="{{ route('frontend.tours.category', $category->slug) }}"
                    class="hover:text-primary">{{ $category->name }}</a>
                <span class="mx-2">/</span>
                <span class="text-slate-900">{{ $tour->name }}</span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <h1 class="text-3xl lg:text-4xl font-serif font-bold text-slate-900 mb-4"
                        style="font-family: 'Playfair Display', serif;">
                        {{ $tour->name }}
                    </h1>

                    <div
                        class="flex flex-wrap items-center gap-4 text-sm text-slate-600 mb-6 border-b border-slate-100 pb-6">
                        @if($tour->code)
                            <div class="flex items-center gap-1.5 bg-slate-50 px-3 py-1.5 rounded-md">
                                <i class="fas fa-barcode text-primary"></i> <b>Mã tour:</b> {{ $tour->code }}
                            </div>
                        @endif
                        <div class="flex items-center gap-1.5">
                            <i class="fas fa-clock text-primary"></i> {{ $tour->duration ?? 'Đang cập nhật' }}
                        </div>
                        <div class="flex items-center gap-1.5">
                            <i class="fas fa-plane text-primary"></i> {{ $tour->transport ?? 'Đang cập nhật' }}
                        </div>
                        <div class="flex items-center gap-1.5">
                            <i class="fas fa-calendar-alt text-primary"></i> {{ $tour->departure ?? 'Đang cập nhật' }}
                        </div>
                    </div>

                    <!-- Images -->
                    @if($tourImageUrl)
                        <div class="rounded-2xl overflow-hidden mb-8 shadow-sm">
                            <img src="{{ $tourImageUrl }}" alt="{{ $tour->name }}"
                                class="w-full h-auto object-cover aspect-video">
                        </div>
                    @endif

                    @if($tour->description)
                        <div
                            class="text-lg text-slate-600 font-medium leading-relaxed mb-8 bg-slate-50 p-6 rounded-xl border border-slate-100 border-l-4 border-l-primary">
                            {{ $tour->description }}
                        </div>
                    @endif

                    @if($tour->features && count($tour->features) > 0)
                        <div class="mb-8">
                            <h3 class="text-xl font-bold text-slate-900 mb-4">Điểm nhấn hành trình</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($tour->features as $feature)
                                    <span
                                        class="inline-flex items-center gap-2 bg-emerald-50 text-emerald-700 px-4 py-2 rounded-lg font-medium text-sm">
                                        <i class="fas fa-check-circle text-emerald-500"></i> {{ $feature }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Content -->
                    <div class="prose prose-slate max-w-none prose-img:rounded-xl">
                        {!! $tour->content !!}
                    </div>
                </div>

                <!-- Sidebar / Booking Box -->
                <div class="lg:col-span-1">
                    <div
                        class="sticky top-28 bg-white border border-slate-200 shadow-xl shadow-slate-200/50 rounded-2xl p-6">
                        <div class="text-center mb-6">
                            @if($tour->price_discount > 0)
                                <div class="text-sm text-slate-500 line-through mb-1">
                                    {{ number_format($tour->price, 0, ',', '.') }}₫</div>
                                <div class="text-3xl font-extrabold text-red-600">
                                    {{ number_format($tour->price_discount, 0, ',', '.') }}₫ <span
                                        class="text-sm font-normal text-slate-500">/khách</span></div>
                            @else
                                <div class="text-3xl font-extrabold text-red-600">
                                    {{ $tour->price > 0 ? number_format($tour->price, 0, ',', '.') . '₫' : 'Liên hệ' }} <span
                                        class="text-sm font-normal text-slate-500">{{ $tour->price > 0 ? '/khách' : '' }}</span>
                                </div>
                            @endif
                        </div>

                        <a href="{{ route('contact.show') }}"
                            class="block w-full py-4 bg-primary text-white text-center font-bold text-lg rounded-xl shadow-lg shadow-primary/30 hover:bg-dark-primary transition-colors mb-4">
                            <i class="fas fa-paper-plane mr-2"></i> Đặt tour ngay
                        </a>
                        @if(filled($setting->phone))
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $setting->phone) }}"
                                class="block w-full py-3.5 bg-slate-50 text-slate-700 text-center font-bold border border-slate-200 rounded-xl hover:bg-slate-100 transition-colors">
                                <i class="fas fa-phone-alt text-primary mr-2"></i> Gọi tư vấn:
                                {{ $setting->phone_display ?: $setting->phone }}
                            </a>
                        @endif

                        <hr class="my-6 border-slate-100">
                        <div class="text-sm text-slate-600 space-y-3">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-shield-alt text-primary mt-1"></i>
                                <div><b>Thông tin rõ ràng</b><br>Tư vấn chi tiết trước khi đặt tour</div>
                            </div>
                            <div class="flex items-start gap-3">
                                <i class="fas fa-headset text-primary mt-1"></i>
                                <div><b>Hỗ trợ tận tâm</b><br>Đồng hành cùng bạn trong suốt hành trình</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Tours -->
            @if($relatedTours->count() > 0)
                <div class="mt-16 pt-12 border-t border-slate-100">
                    <h3 class="text-2xl font-bold text-slate-900 mb-6">Có thể bạn sẽ thích</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($relatedTours as $product)
                            @include('partials.frontend.tour-card', ['product' => $product])
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
