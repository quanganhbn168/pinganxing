@extends('layouts.master')
@section('title', $service->name)
@section('meta_description', Str::limit(strip_tags($service->content), 155))
@section('meta_image', optional($service->image) ? asset($service->image) : '')
@section('content')
<x-frontend.page-hero 
    :image="$bannerUrl" 
    :title="$service->name" 
    :breadcrumb="$breadcrumbItems" 
/>

<div class="bg-white dark:bg-gray-900 py-12 md:py-20 border-b border-gray-100 dark:border-gray-800">
    <div class="max-w-screen-xl mx-auto px-4">
        
        {{-- Phần giới thiệu dịch vụ (Top Hero Section) --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 mb-16 items-start">
            {{-- Cột hình ảnh --}}
            <div class="rounded-sm overflow-hidden shadow-2xl border border-gray-100 dark:border-gray-800">
                <img src="{{ $service->image ? asset($service->image->path) : asset('images/setting/no-image.png') }}" 
                     alt="{{ $service->name }}" 
                     class="w-full h-auto object-cover aspect-[4/3] @if(!$service->image) grayscale @endif">
            </div>

            {{-- Cột thông tin --}}
            <div class="flex flex-col justify-center h-full">
                <h1 class="text-3xl md:text-5xl font-black text-gray-900 dark:text-white leading-tight mb-4">
                    {{ $service->name }}
                </h1>
                
                @if(!empty($service->description))
                <div class="text-lg md:text-xl font-bold text-gray-800 dark:text-gray-300 mb-6 leading-snug">
                    {!! nl2br(e($service->description)) !!}
                </div>
                @endif
                
                <div class="flex flex-wrap gap-4 mt-auto pt-6 border-t border-gray-100 dark:border-gray-800">
                    <a href="{{ route('contact.show') }}" class="inline-flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white font-bold uppercase tracking-wider text-sm px-8 py-4 rounded-sm transition-colors shadow-md">
                        <i class="fas fa-file-invoice"></i> Yêu cầu báo giá
                    </a>
                    <a href="tel:{{ $setting->phone ?? '' }}" class="inline-flex items-center justify-center gap-2 bg-brand-800 hover:bg-brand-900 text-white font-bold uppercase tracking-wider text-sm px-8 py-4 rounded-sm transition-colors shadow-md">
                        <i class="fas fa-phone-alt"></i> Gọi tư vấn
                    </a>
                </div>

                {{-- Chia sẻ --}}
                <div class="mt-8 pt-6 flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    <span class="font-bold text-gray-900 dark:text-white uppercase text-sm tracking-wider">Chia sẻ dịch vụ:</span>
                    <x-social-share :title="$service->name" />
                </div>
            </div>
        </div>

        {{-- NỘI DUNG CHÍNH BÀI VIẾT (Full-width) --}}
        <div class="max-w-4xl mx-auto">
            <article class="prose prose-lg md:prose-xl max-w-none prose-blue dark:prose-invert">
                @if(empty($service->content) || trim(strip_tags($service->content)) == '')
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-sm p-10 text-center border border-dashed border-gray-300 dark:border-gray-600 my-12">
                        <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-sm flex items-center justify-center mx-auto mb-4">
                            <i class="fa-solid fa-file-pen text-2xl text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Nội dung đang cập nhật</h3>
                        <p class="text-gray-500 dark:text-gray-400">Thông tin chi tiết về bài viết/dịch vụ này đang được chúng tôi hoàn thiện.</p>
                    </div>
                @else
                    {!! $service->content !!}
                @endif
            </article>
        </div>
    </div>
</div>

{{-- LANDING PAGE SECTIONS (Trải Full-Width) --}}

{{-- DỰ ÁN LIÊN QUAN ĐẾN DỊCH VỤ --}}
@if(isset($service->projects) && $service->projects->count() > 0)
<section class="py-12 md:py-16 bg-white dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800">
    <div class="max-w-screen-xl mx-auto px-4">
        <div class="mb-10 text-center">
            <h2 class="text-2xl md:text-3xl font-black uppercase text-gray-900 dark:text-white tracking-tight">{{ $setting->projects_title ?? 'Dự Án Tiêu Biểu' }}</h2>
            <div class="w-16 h-1 bg-brand-600 mx-auto mt-4 mb-4"></div>
            @if(!empty($setting->projects_description))
                <p class="text-gray-600 dark:text-gray-400">{{ $setting->projects_description }}</p>
            @endif
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($service->projects as $project)
                <x-frontend.card 
                    :href="$project->slug_url"
                    :image="$project->image ? $project->image->path : null"
                    :title="$project->name"
                    :description="$project->description"
                />
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- GÓI SẢN PHẨM / PHÂN HỆ --}}
@if(isset($service->products) && $service->products->count() > 0)
<section class="py-12 md:py-16 bg-gray-50 dark:bg-gray-800">
    <div class="max-w-screen-xl mx-auto px-4">
        <div class="mb-10 text-center">
            <h2 class="text-2xl md:text-3xl font-black uppercase text-gray-900 dark:text-white tracking-tight">{{ $setting->products_title ?? 'Gói Giải Pháp & Phân Hệ' }}</h2>
            <div class="w-16 h-1 bg-brand-600 mx-auto mt-4 mb-4"></div>
            @if(!empty($setting->products_description))
                <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $setting->products_description }}</p>
            @endif
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($service->products as $product)
                <a href="{{ $product->slug_url }}" class="bg-white dark:bg-gray-900 p-6 rounded-sm shadow-sm hover:shadow-md hover:-translate-y-1 transition-all border border-gray-100 dark:border-gray-700 flex flex-col h-full group">
                    <h3 class="font-bold text-gray-900 dark:text-white text-lg mb-2 group-hover:text-brand-600 transition-colors">{{ $product->name }}</h3>
                    <div class="text-orange-600 font-bold mb-4">{{ $product->price > 0 ? number_format($product->price) . ' đ' : 'Báo giá linh hoạt' }}</div>
                    <div class="mt-auto text-brand-600 dark:text-brand-400 font-bold text-sm">Xem chi tiết &rarr;</div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- KIẾN THỨC VÀ HƯỚNG DẪN --}}
@if(isset($service->posts) && $service->posts->count() > 0)
<section class="py-12 md:py-16 bg-white dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800">
    <div class="max-w-screen-xl mx-auto px-4">
        <div class="mb-10 text-center">
            <h2 class="text-2xl md:text-3xl font-black uppercase text-gray-900 dark:text-white tracking-tight">{{ $setting->posts_title ?? 'Kiến Thức & Tài Liệu' }}</h2>
            <div class="w-16 h-1 bg-brand-600 mx-auto mt-4 mb-4"></div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($service->posts as $post)
                <a href="{{ $post->slug_url }}" class="flex bg-gray-50 dark:bg-gray-800 rounded-sm overflow-hidden border border-gray-100 dark:border-gray-700 hover:border-brand-300 transition-all group">
                    @if($post->image)
                        <img src="{{ asset($post->image->path) }}" alt="{{ $post->title }}" class="w-1/3 object-cover">
                    @endif
                    <div class="w-2/3 p-4 flex flex-col justify-center">
                        <h4 class="font-bold text-gray-900 dark:text-white line-clamp-2 text-sm leading-snug group-hover:text-brand-600 transition-colors">{{ $post->title }}</h4>
                        <time class="text-xs text-gray-500 mt-2">{{ $post->created_at->format('d/m/Y') }}</time>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- DỊCH VỤ CÙNG DANH MỤC --}}
@if(isset($relatedServices) && $relatedServices->count() > 0)
<section class="py-12 bg-gray-50 dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700">
    <div class="max-w-screen-xl mx-auto px-4">
        <h2 class="text-xl font-bold uppercase text-gray-900 dark:text-white mb-6 border-l-4 border-brand-600 pl-3">Các dịch vụ khác</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($relatedServices as $related)
                <a href="{{ $related->slug_url }}" class="group block bg-white dark:bg-gray-900 rounded-sm border border-gray-100 dark:border-gray-700 overflow-hidden hover:border-brand-500 transition-colors">
                    <div class="aspect-video relative overflow-hidden bg-gray-200 dark:bg-gray-700">
                        <img src="{{ asset($related->image->path ?? 'images/setting/no-image.png') }}" class="w-full h-full object-cover">
                    </div>
                    <div class="p-3">
                        <h4 class="font-bold text-sm uppercase text-gray-800 dark:text-gray-200 group-hover:text-brand-600 transition-colors">
                            {{ $related->name }}
                        </h4>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- BÌNH LUẬN & ĐÁNH GIÁ DỊCH VỤ --}}
<section class="py-12 bg-white dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800">
    <div class="max-w-screen-xl mx-auto px-4">
        <div class="mb-10 text-center">
            <h2 class="text-2xl font-black uppercase text-gray-900 dark:text-white tracking-tight">Đánh giá khách hàng</h2>
            <div class="w-16 h-1 bg-brand-600 mx-auto mt-4"></div>
        </div>
        <div class="bg-gray-50 dark:bg-gray-800 p-8 md:p-12 rounded-sm shadow-sm border border-gray-100 dark:border-gray-700">
            <x-comment-list :comments="$service->approvedComments" />
            <x-comment-form :commentable="$service" type="service" />
        </div>
    </div>
</section>
@endsection
