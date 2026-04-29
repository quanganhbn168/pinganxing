@extends('layouts.master')
@section('title', $post->title)
@section('meta_description', $post->description ?? Str::limit(strip_tags($post->content), 155))
@section('meta_image', $post->image?->url ?: ($post->image ? $post->image?->url : ''))

@push('jsonld')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "NewsArticle",
  "mainEntityOfPage": {
    "@type": "WebPage",
    "@id": "{{ url()->current() }}"
  },
  "headline": "{{ $post->title }}",
  "image": [
    "{{ $post->image?->url ?: ($post->image ? $post->image?->url : '') }}"
  ],
  "datePublished": "{{ $post->created_at->toIso8601String() }}",
  "dateModified": "{{ $post->updated_at->toIso8601String() }}",
  "author": {
    "@type": "Person",
    "name": "{{ $setting->site_name ?? 'Admin' }}",
    "url": "{{ url('/') }}",
    "image": "{{ asset($setting->logo) }}"
  },
  "publisher": {
    "@type": "Organization",
    "name": "{{ $setting->site_name }}",
    "logo": {
      "@type": "ImageObject",
      "url": "{{ asset($setting->logo) }}"
    }
  },
  "description": "{{ $post->description ?? Str::limit(strip_tags($post->content), 155) }}"
  @if($aggregateRating = $post->getAggregateRatingData())
  ,"aggregateRating": @json($aggregateRating)
  @endif
}
</script>
@endpush

@push('css')
<style>
    .prose-custom img {
        max-width: 100%;
        height: auto !important;
        border-radius: 0.75rem;
        margin: 2rem auto;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    .prose-custom p {
        text-align: justify;
        line-height: 1.8;
    }
    .sticky-sidebar {
        position: sticky;
        top: 6rem;
        z-index: 10;
    }
</style>
@endpush

@section('content')
{{-- Header/Banner --}}
@php
    $bannerUrl = $post->banner?->url ?: ($post->banner ? asset($post->banner) : asset($setting->banner ?? ''));
@endphp
@if($bannerUrl)
<div class="w-full h-[20vh] md:h-[30vh] overflow-hidden bg-gray-900">
    <img src="{{ $bannerUrl }}" alt="{{ $post->title }}" class="w-full h-full object-cover opacity-70" loading="lazy">
</div>
@endif

<div class="bg-gray-50 dark:bg-gray-900 py-10 md:py-16">
    <div class="max-w-screen-xl mx-auto px-4">
        <div class="flex flex-col lg:flex-row gap-8 lg:gap-12">

            {{-- Cột NỘI DUNG CHÍNH (Trai) --}}
            <div class="w-full lg:w-3/4">
                <article class="bg-white dark:bg-gray-800 rounded-3xl p-6 md:p-10 shadow-sm border border-gray-100 dark:border-gray-700">

                    {{-- Meta Header --}}
                    <header class="mb-8 border-b border-gray-100 dark:border-gray-700 pb-8">
                        <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 dark:text-white leading-tight mb-4">
                            {{ $post->title }}
                        </h1>

                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div class="flex items-center text-gray-500 dark:text-gray-400 text-sm font-medium">
                                <span class="flex items-center mr-4">
                                    <i class="far fa-calendar-alt mr-2 text-blue-600"></i> {{ $post->updated_at->format('d/m/Y') }}
                                </span>
                                <span class="flex items-center">
                                    <i class="far fa-user mr-2 text-blue-600"></i> Admin
                                </span>
                            </div>

                            {{-- Chia sẻ --}}
                            <div class="flex items-center">
                                <x-social-share :title="$post->title" />
                            </div>
                        </div>
                    </header>

                    {{-- Ảnh Main nếu có --}}
                    @php
                        $mainUrl = $post->image?->url ?: ($post->image ? $post->image?->url : null);
                    @endphp
                    @if($mainUrl)
                    <div class="mb-10 rounded-2xl overflow-hidden shadow-sm">
                         <img src="{{ $mainUrl }}" alt="{{ $post->title }}" class="w-full h-auto object-cover">
                    </div>
                    @endif

                    {{-- Nội dung bài viết HTML --}}
                    <div class="prose prose-lg md:prose-xl max-w-none prose-blue dark:prose-invert prose-custom mb-10">
                        {!! $contentHtml !!}
                    </div>

                    {{-- Footer bài viết --}}
                    <footer class="mt-10 pt-8 border-t border-gray-100 dark:border-gray-700">
                        <div class="bg-blue-50 dark:bg-gray-700/50 rounded-2xl p-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                            <p class="font-bold text-gray-900 dark:text-white mb-0">Bạn thấy bài viết hữu ích? Ủng hộ bằng cách chia sẻ!</p>
                            <x-social-share :title="$post->title" />
                        </div>
                    </footer>
                </article>
                {{-- BÌNH LUẬN --}}
                <div class="mt-12 bg-white dark:bg-gray-800 p-6 md:p-10 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <x-comment-list :comments="$post->approvedComments" />
                    <x-comment-form :commentable="$post" type="post" />
                </div>
                {{-- BÀI VIẾT LIÊN QUAN --}}
                @if ($relatedPosts->count())
                <div class="mt-12 md:mt-16">
                    <div class="flex items-center justify-between border-b-2 border-gray-100 dark:border-gray-700 mb-8 pb-2">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white uppercase relative inline-block">
                            Bài viết liên quan
                            <div class="absolute -bottom-[4px] left-0 w-16 h-1 bg-blue-600 rounded-r-full"></div>
                        </h3>
                    </div>

                    <div class="swiper related-post-swiper overflow-hidden py-4 -my-4 px-2">
                        <div class="swiper-wrapper">
                            @foreach ($relatedPosts as $related)
                                <div class="swiper-slide">
                                    <div class="group bg-white dark:bg-gray-800 rounded-xl overflow-hidden border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow h-full flex flex-col">
                                        <a href="{{ $related->slug_url }}" class="block relative aspect-video overflow-hidden">
                                            <img src="{{ $related->image?->url ?? $related->banner?->url ?? ($related->image ? $related->image?->url : asset('images/setting/no-image.png')) }}"
                                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                                 alt="{{ $related->title }}" loading="lazy">
                                        </a>
                                        <div class="p-4 flex flex-col flex-1">
                                            <h4 class="font-bold text-gray-900 dark:text-white line-clamp-2 group-hover:text-blue-600 transition-colors text-base">
                                                <a href="{{ $related->slug_url }}">
                                                    {{ $related->title }}
                                                </a>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="swiper-pagination !bottom-0 mt-4 relative"></div>
                    </div>
                </div>
                @endif


            </div>

            {{-- Cột SIDEBAR (Phải) --}}
            <div class="w-full lg:w-1/4">
                <aside class="sticky-sidebar space-y-8">

                    {{-- Widget Danh mục --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm">
                        <h4 class="text-lg font-bold text-gray-900 dark:text-white uppercase mb-4 pb-2 border-b border-gray-100 dark:border-gray-700">
                            Danh mục bài viết
                        </h4>
                        <ul class="space-y-3">
                            @foreach($allCategories as $category)
                            <li>
                                <a href="{{ $category->slug_url }}"
                                   class="flex items-center text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors group">
                                    <span class="w-2 h-2 rounded-full bg-blue-100 group-hover:bg-blue-600 transition-colors mr-3"></span>
                                    {{ $category->name }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Widget Table of Contents (Mục lục tĩnh nếu có) --}}
                    @if(isset($tocList) && count($tocList) > 0)
                    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="p-4 bg-gray-100 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                             <h4 class="text-base font-bold text-gray-900 dark:text-white flex items-center">
                                 <i class="fas fa-list-ul text-blue-600 mr-2"></i> Mục lục nội dung
                             </h4>
                        </div>
                        <div class="p-4 max-h-[60vh] overflow-y-auto custom-scrollbar">
                            <x-toc :list="$tocList" />
                        </div>
                    </div>
                    @endif

                    {{-- Form Tư Vấn --}}
                    <div class="hidden md:block">
                        @include('partials.frontend.contact_register')
                    </div>

                </aside>
            </div>

        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const relSlider = document.querySelector('.related-post-swiper');
        if (relSlider) {
            new Swiper(relSlider, {
                slidesPerView: 2,
                spaceBetween: 16,
                pagination: {
                    el: ".related-post-swiper .swiper-pagination",
                    clickable: true
                },
                breakpoints: {
                    640: { slidesPerView: 2, spaceBetween: 20 },
                    768: { slidesPerView: 3, spaceBetween: 20 },
                    1024: { slidesPerView: 3, spaceBetween: 24 }
                }
            });
        }
    });
</script>
@endpush
