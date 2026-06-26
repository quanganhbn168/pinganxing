@extends('layouts.master')
@section('title', 'Dịch vụ của chúng tôi')
@section('content')
    <div class="bg-slate-50 min-h-screen pt-24 pb-12">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">
            <h1 class="text-4xl font-serif font-bold text-slate-900 mb-8" style="font-family: 'Playfair Display', serif;">
                {{ isset($category) ? 'Dịch vụ ' . $category->name : 'Các dịch vụ của chúng tôi' }}
            </h1>

            @if(!isset($category) && isset($serviceCategories) && $serviceCategories->count() > 0)
                <div class="mb-10 flex flex-wrap gap-3">
                    <a href="{{ route('frontend.services.index') }}"
                        class="px-5 py-2 rounded-full bg-primary text-white text-sm font-bold shadow-md">
                        Tất cả
                    </a>
                    @foreach($serviceCategories as $cat)
                        <a href="{{ $cat->slug_url }}"
                            class="px-5 py-2 rounded-full bg-white border border-slate-200 text-slate-600 hover:text-primary hover:border-primary text-sm font-bold transition-all">
                            {{ $cat->name }}
                        </a>
                    @endforeach
                </div>
            @elseif(isset($category))
                <div class="mb-10 flex flex-wrap gap-3">
                    <a href="{{ route('frontend.services.index') }}"
                        class="px-5 py-2 rounded-full bg-white border border-slate-200 text-slate-600 hover:text-primary hover:border-primary text-sm font-bold transition-all">
                        Tất cả
                    </a>
                    <a href="{{ $category->slug_url }}"
                        class="px-5 py-2 rounded-full bg-primary text-white text-sm font-bold shadow-md">
                        {{ $category->name }}
                    </a>
                </div>
            @endif

            @if(isset($category))
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($services as $service)
                        <a href="{{ $service->slug_url }}"
                            class="group block bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:border-primary/30 hover:shadow-xl hover:shadow-primary/5 transition-all">
                            <div class="relative aspect-video bg-slate-100 overflow-hidden">
                                @if($service->image)
                                    <img src="{{ url('storage/' . $service->image->path) }}" alt="{{ $service->name }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200">
                                        <i class="fas fa-concierge-bell text-4xl text-slate-400"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="p-5">
                                <h3 class="font-extrabold text-slate-900 mb-2 line-clamp-2">{{ $service->name }}</h3>
                                <p class="text-sm text-slate-500 leading-6 line-clamp-2">
                                    {{ Str::limit(strip_tags($service->description ?? $service->content), 96) }}</p>
                                <div class="mt-4 text-sm font-bold text-primary">
                                    Xem chi tiết <i class="fas fa-arrow-right text-xs ml-1"></i>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
                <div class="mt-12">
                    {{ $services->links() }}
                </div>
            @else
                @foreach($serviceCategories as $cat)
                    <div class="mb-16 last:mb-0">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold text-slate-900">{{ $cat->name }}</h2>
                            <a href="{{ $cat->slug_url }}"
                                class="text-sm font-bold text-primary hover:underline">Xem tất cả</a>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($cat->services->take(6) as $service)
                                <a href="{{ $service->slug_url }}"
                                    class="group block bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:border-primary/30 hover:shadow-xl hover:shadow-primary/5 transition-all">
                                    <div class="relative aspect-video bg-slate-100 overflow-hidden">
                                        @if($service->image)
                                            <img src="{{ url('storage/' . $service->image->path) }}" alt="{{ $service->name }}"
                                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                        @else
                                            <div class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200">
                                                <i class="fas fa-concierge-bell text-4xl text-slate-400"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="p-5">
                                        <h3 class="font-extrabold text-slate-900 mb-2 line-clamp-2">{{ $service->name }}</h3>
                                        <p class="text-sm text-slate-500 leading-6 line-clamp-2">
                                            {{ Str::limit(strip_tags($service->description ?? $service->content), 96) }}</p>
                                        <div class="mt-4 text-sm font-bold text-primary">
                                            Xem chi tiết <i class="fas fa-arrow-right text-xs ml-1"></i>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endif

        </div>
    </div>
@endsection
