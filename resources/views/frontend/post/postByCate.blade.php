@extends('layouts.master')
@section('title', $category->name ?? 'Tin tức')

@section('content')
{{-- Hero Banner --}}
<div class="relative w-full h-[25vh] md:h-[35vh] overflow-hidden">
    <img src="{{ optional($category->bannerImage())->url() ?: ($category->banner ? asset($category->banner) : asset($setting->banner)) }}" 
         alt="{{ $category->name }}" class="w-full h-full object-cover">
    <div class="absolute inset-0 bg-gray-900/60 flex flex-col items-center justify-center">
        <h1 class="text-3xl md:text-5xl font-bold text-white uppercase tracking-wider mb-4 text-center px-4">{{ $category->name }}</h1>
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="/" class="inline-flex items-center text-sm font-medium text-gray-200 hover:text-white transition-colors">
                        <i class="fas fa-home mr-2"></i> Trang chủ
                    </a>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2 text-sm"></i>
                        <span class="text-sm font-medium text-gray-100">{{ $category->name }}</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>
</div>

<div class="bg-white dark:bg-gray-900 py-12">
    <div class="max-w-screen-xl mx-auto px-4">
        
        <div class="flex flex-col sm:flex-row justify-between items-center mb-10 pb-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4 sm:mb-0">
                Cập nhật mới nhất
            </h2>
            
            {{-- View Switcher --}}
            <div class="flex space-x-2">
                <button class="btn-view grid-view active w-10 h-10 flex items-center justify-center rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500"
                        onclick="setView('grid')" title="Dạng lưới">
                    <i class="fas fa-th-large"></i>
                </button>
                <button class="btn-view list-view w-10 h-10 flex items-center justify-center rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500"
                        onclick="setView('list')" title="Dạng danh sách">
                    <i class="fas fa-list"></i>
                </button>
            </div>
        </div>

        {{-- Post Container --}}
        <div class="post-container" data-layout="grid">
            @if($posts->isEmpty())
                <div class="text-center py-12">
                    <i class="fas fa-folder-open text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                    <p class="text-gray-500 dark:text-gray-400">Chưa có bài viết nào trong danh mục này.</p>
                </div>
            @else
                <div class="grid gap-6 grid-cols-1 md:grid-cols-2 lg:grid-cols-3 list-cols">
                    @foreach($posts as $post)
                        <article class="post-card group flex flex-col bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-xl overflow-hidden transition-all duration-300">
                            <a href="{{ $post->slug_url }}" class="block post-img-wrapper overflow-hidden relative aspect-[16/9]">
                                <img src="{{ optional($post->mainImage())->url() ?? optional($post->bannerImage())->url() ?? ($post->image ? asset($post->image) : asset('images/setting/no-image.png')) }}"
                                     alt="{{ $post->title }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                <div class="absolute inset-0 bg-gray-900/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            </a>
                            <div class="p-5 sm:p-6 flex flex-col flex-1 post-content">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2 flex items-center">
                                    <i class="far fa-calendar-alt mr-1.5"></i>
                                    {{ $post->created_at ? $post->created_at->format('d/m/Y') : '' }}
                                </p>
                                <h3 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white mb-3 line-clamp-2 group-hover:text-blue-600 transition-colors leading-tight">
                                    <a href="{{ $post->slug_url }}">
                                        {{ $post->title }}
                                    </a>
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-3 mb-4 flex-1 post-desc">
                                    {{ strip_tags($post->description) }}
                                </p>
                                <a href="{{ $post->slug_url }}" class="inline-flex items-center text-sm font-bold text-blue-600 dark:text-blue-400 group-hover:underline mt-auto w-max">
                                    Đọc tiếp <i class="fas fa-arrow-right ml-1.5 text-xs group-hover:translate-x-1 transition-transform"></i>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Pagination --}}
        @if(method_exists($posts, 'links') && $posts->hasPages())
            <div class="mt-12 flex justify-center">
                {{ $posts->links('vendor.pagination.tailwind') }}
            </div>
        @endif

    </div>
</div>
@endsection

@push('js')
<style>
    .btn-view.active {
        @apply bg-blue-600 text-white border-blue-600 dark:bg-blue-600 dark:border-blue-600 dark:text-white;
    }
    
    @media (min-width: 768px) {
        .post-container[data-layout="list"] .list-cols {
            @apply grid-cols-1;
        }
        .post-container[data-layout="list"] .post-card {
            @apply flex-row h-56;
        }
        .post-container[data-layout="list"] .post-img-wrapper {
            @apply w-2/5 aspect-auto h-full border-r border-gray-100 dark:border-gray-700;
        }
        .post-container[data-layout="list"] .post-content {
            @apply w-3/5 p-6 sm:p-8;
        }
        .post-container[data-layout="list"] .post-card h3 {
            @apply text-xl sm:text-2xl mb-4;
        }
        .post-container[data-layout="list"] .post-desc {
            @apply line-clamp-3 text-base;
        }
    }
</style>

<script>
    function setView(mode) {
        const container = document.querySelector('.post-container');
        const btnGrid = document.querySelector('.btn-view.grid-view');
        const btnList = document.querySelector('.btn-view.list-view');

        if(mode === 'grid') {
            if(btnGrid) btnGrid.classList.add('active');
            if(btnList) btnList.classList.remove('active');
        } else {
            if(btnList) btnList.classList.add('active');
            if(btnGrid) btnGrid.classList.remove('active');
        }

        if(container) {
            container.setAttribute('data-layout', mode);
        }

        localStorage.setItem('cateViewMode', mode);
    }

    document.addEventListener('DOMContentLoaded', () => {
        const savedMode = localStorage.getItem('cateViewMode') || 'grid';
        setView(savedMode);
    });
</script>
@endpush
