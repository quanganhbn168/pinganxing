@extends('layouts.master')
@section('title', $service->name)
@section('content')
    <div class="bg-white min-h-screen pt-24 pb-12">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">
            <!-- Breadcrumbs -->
            <div class="text-sm text-slate-500 mb-6">
                <a href="{{ route('home') }}" class="hover:text-primary">Trang chủ</a>
                <span class="mx-2">/</span>
                <a href="{{ route('frontend.services.index') }}" class="hover:text-primary">Dịch vụ</a>
                @if($service->category)
                    <span class="mx-2">/</span>
                    <a href="{{ $service->category->slug_url }}" class="hover:text-primary">{{ $service->category->name }}</a>
                @endif
                <span class="mx-2">/</span>
                <span class="text-slate-900">{{ $service->name }}</span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-16 h-16 rounded-2xl bg-slate-50 flex items-center justify-center">
                            @if($service->image)
                                <img src="{{ url('storage/' . $service->image->path) }}" alt="{{ $service->name }}"
                                    class="w-10 h-10 object-contain">
                            @else
                                <i class="fas fa-concierge-bell text-2xl text-slate-400"></i>
                            @endif
                        </div>
                        <h1 class="text-3xl lg:text-4xl font-serif font-bold text-slate-900"
                            style="font-family: 'Playfair Display', serif;">
                            {{ $service->name }}
                        </h1>
                    </div>

                    @if($service->banner)
                        <div class="rounded-2xl overflow-hidden mb-8 shadow-sm">
                            <img src="{{ url('storage/' . $service->banner->path) }}" alt="{{ $service->name }}"
                                class="w-full h-auto object-cover aspect-video">
                        </div>
                    @endif

                    @if($service->description)
                        <div
                            class="text-lg text-slate-600 font-medium leading-relaxed mb-8 bg-slate-50 p-6 rounded-xl border border-slate-100 border-l-4 border-l-primary">
                            {{ $service->description }}
                        </div>
                    @endif

                    <!-- Content -->
                    <div class="prose prose-slate max-w-none prose-img:rounded-xl mb-12">
                        {!! $service->content !!}
                    </div>
                </div>

                <!-- Sidebar / CTA Box -->
                <div class="lg:col-span-1">
                    <div
                        class="sticky top-28 bg-white border border-slate-200 shadow-xl shadow-slate-200/50 rounded-2xl p-6">
                        <div class="text-center mb-6">
                            <h3 class="text-xl font-bold text-slate-900 mb-2">Đăng ký dịch vụ</h3>
                            <p class="text-sm text-slate-500">Để lại thông tin, chuyên viên của chúng tôi sẽ liên hệ tư vấn
                                chi tiết cho bạn.</p>
                        </div>

                        <a href="{{ route('contact.show') }}"
                            class="block w-full py-4 bg-primary text-white text-center font-bold text-lg rounded-xl shadow-lg shadow-primary/30 hover:bg-dark-primary transition-colors mb-4">
                            <i class="fas fa-paper-plane mr-2"></i> Yêu cầu tư vấn
                        </a>
                        <a href="tel:{{ $setting->phone ?? '19001234' }}"
                            class="block w-full py-3.5 bg-slate-50 text-slate-700 text-center font-bold border border-slate-200 rounded-xl hover:bg-slate-100 transition-colors">
                            <i class="fas fa-phone-alt text-primary mr-2"></i> Gọi tư vấn:
                            {{ $setting->phone ?? '1900 1234' }}
                        </a>

                        <hr class="my-6 border-slate-100">
                        <div class="text-sm text-slate-600 space-y-3">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-check-circle text-primary mt-1"></i>
                                <div><b>Uy tín & Chuyên nghiệp</b><br>Dịch vụ chất lượng cao</div>
                            </div>
                            <div class="flex items-start gap-3">
                                <i class="fas fa-headset text-primary mt-1"></i>
                                <div><b>Hỗ trợ tận tâm</b><br>Xử lý nhanh chóng 24/7</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Services -->
            @if(isset($relatedServices) && $relatedServices->count() > 0)
                <div class="mt-16 pt-12 border-t border-slate-100">
                    <h3 class="text-2xl font-bold text-slate-900 mb-6">Các dịch vụ liên quan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach($relatedServices as $relService)
                            <a href="{{ $relService->slug_url }}"
                                class="group block bg-white rounded-3xl p-6 shadow-sm border border-slate-100 hover:border-primary/30 hover:shadow-xl hover:shadow-primary/5 transition-all">
                                <div
                                    class="w-16 h-16 rounded-2xl bg-slate-50 flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                                    @if($relService->image)
                                        <img src="{{ url('storage/' . $relService->image->path) }}" alt="{{ $relService->name }}"
                                            class="w-10 h-10 object-contain">
                                    @else
                                        <i class="fas fa-concierge-bell text-2xl text-slate-400"></i>
                                    @endif
                                </div>
                                <h3 class="font-extrabold text-slate-900 mb-2">{{ $relService->name }}</h3>
                                <p class="text-sm text-slate-500 leading-6">
                                    {{ Str::limit(strip_tags($relService->description ?? $relService->content), 80) }}</p>
                                <div class="mt-4 text-sm font-bold text-primary">
                                    Xem chi tiết <i class="fas fa-arrow-right text-xs ml-1"></i>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection