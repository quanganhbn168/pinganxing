@extends('layouts.master')
@section('title', $pageTitle ?? 'Tin tức')

@section('content')

<x-frontend.page-hero
    :title="$pageTitle"
    :subtitle="$pageSubtitle"
    :breadcrumb="$breadcrumbs"
/>

<section class="py-14 bg-white dark:bg-gray-900">
    <div class="max-w-screen-xl mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 lg:gap-12">

            {{-- Cột nội dung chính --}}
            <div class="lg:col-span-3 space-y-10">

                {{-- ── BÀI VIẾT NỔI BẬT ─────────────────────────── --}}
                @if(isset($featuredPost) && $featuredPost)
                <div class="mb-2">
                    <x-frontend.section-heading title="Bài viết nổi bật" />

                    <a href="{{ $featuredPost->slug_url }}"
                       class="group relative flex flex-col md:flex-row bg-white dark:bg-gray-800 rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-xl transition-all duration-300">

                        {{-- Ảnh --}}
                        <div class="relative md:w-1/2 aspect-video md:aspect-auto overflow-hidden bg-gray-100 dark:bg-gray-700 flex-shrink-0">
                            @if($featuredPost->image)
                                <img src="{{ $featuredPost->image->url }}"
                                     alt="{{ $featuredPost->title }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-800 to-blue-600 min-h-[240px]">
                                    <i class="far fa-newspaper text-5xl text-blue-300 opacity-40"></i>
                                </div>
                            @endif

                            {{-- Nhãn nổi bật --}}
                            <div class="absolute top-4 left-4">
                                <span class="inline-flex items-center gap-1.5 bg-amber-400 text-amber-900 text-xs font-black uppercase tracking-widest px-3 py-1 rounded-full shadow">
                                    <i class="fas fa-star text-[10px]"></i> Nổi bật
                                </span>
                            </div>
                        </div>

                        {{-- Nội dung --}}
                        <div class="flex flex-col justify-center p-6 md:p-8 md:w-1/2">
                            @if($featuredPost->category)
                                <span class="text-xs font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400 mb-2">
                                    {{ $featuredPost->category->name }}
                                </span>
                            @endif

                            <h2 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white mb-3 leading-snug group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-3">
                                {{ $featuredPost->title }}
                            </h2>

                            @if($featuredPost->description)
                                <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-3 mb-5 leading-relaxed">
                                    {{ strip_tags($featuredPost->description) }}
                                </p>
                            @endif

                            <div class="flex items-center justify-between mt-auto">
                                <div class="flex items-center text-xs text-gray-400 gap-3">
                                    <span class="flex items-center gap-1">
                                        <i class="far fa-calendar-alt text-blue-500"></i>
                                        {{ $featuredPost->updated_at->format('d/m/Y') }}
                                    </span>
                                </div>
                                <span class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 dark:text-blue-400 group-hover:gap-2 transition-all">
                                    Đọc ngay <i class="fas fa-arrow-right text-[10px]"></i>
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
                @endif

                {{-- ── DANH SÁCH BÀI VIẾT ──────────────────────────── --}}
                <div>
                    @if(isset($featuredPost) && $featuredPost)
                        <x-frontend.section-heading title="Bài viết mới nhất" />
                    @else
                        <x-frontend.section-heading title="Bài viết mới nhất" />
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        @forelse($posts as $post)
                            <x-frontend.card
                                :href="$post->slug_url"
                                :image="$post->image ? $post->image->url : null"
                                :title="$post->title"
                                :description="$post->description"
                                :date="$post->created_at->format('d/m/Y')"
                                :badge="$post->category->name ?? null"
                            />
                        @empty
                            <div class="col-span-full text-center py-16 bg-gray-50 dark:bg-gray-800 rounded-2xl">
                                <i class="far fa-newspaper text-4xl text-gray-300 mb-4 block"></i>
                                <p class="text-gray-500">Chưa có bài viết nào.</p>
                            </div>
                        @endforelse
                    </div>

                    @if($posts->hasPages())
                        <div class="mt-10 flex justify-center">
                            {{ $posts->links('vendor.pagination.tailwind') }}
                        </div>
                    @endif
                </div>

            </div>

            {{-- Sidebar --}}
            <div class="lg:col-span-1">
                <div class="sticky top-24">
                    <x-frontend.aside />
                </div>
            </div>

        </div>
    </div>
</section>

@endsection
