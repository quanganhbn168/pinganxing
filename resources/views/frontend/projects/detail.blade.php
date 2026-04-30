@extends('layouts.master')
@section('title', $pageTitle)
@section('meta_description', $metaDescription ?? '')
@section('meta_image', $project->banner ? asset($project->banner->url) : ($project->image ? asset($project->image->url) : ''))

@push('jsonld')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "CreativeWork",
  "mainEntityOfPage": {
    "@type": "WebPage",
    "@id": "{{ url()->current() }}"
  },
  "name": "{{ $project->name }}",
  "image": "{{ $project->banner ? asset($project->banner->url) : ($project->image ? asset($project->image->url) : '') }}",
  "dateCreated": "{{ $project->created_at->toIso8601String() }}",
  "dateModified": "{{ $project->updated_at->toIso8601String() }}",
  "creator": {
    "@type": "Organization",
    "name": "{{ $setting->site_name ?? config('app.name') }}",
    "url": "{{ url('/') }}",
    "image": "{{ asset($setting->logo) }}"
  },
  "description": "{{ $metaDescription ?? '' }}"
  @if($aggregateRating = $project->getAggregateRatingData())
  ,"aggregateRating": @json($aggregateRating)
  @endif
}
</script>
@endpush

@push('css')
<style>
    /* Custom styles cho Swiper Thumbs */
    .gallery-top {
        background: #000;
        border-radius: 0.5rem;
    }
    .gallery-thumbs .swiper-slide {
        opacity: 0.4;
        transition: opacity 0.3s;
        border-radius: 0.25rem;
        border: 2px solid transparent;
        cursor: pointer;
    }
    .gallery-thumbs .swiper-slide-thumb-active {
        opacity: 1;
        border-color: #2563eb; /* blue-600 */
    }
    /* Formatto nội dung bài viết WYSIWYG */
    .prose-custom img {
        max-width: 100%;
        height: auto !important;
        border-radius: 0.5rem;
        margin: 1.5rem auto;
    }
</style>
@endpush

@section('content')

<x-frontend.leaderboard
    :image="$bannerUrl"
    :title="$pageTitle"
    :subline="$project->category?->name"
    :description="$project->description"
    :breadcrumb="$breadcrumbs"
    :stats="array_values(array_filter([
        $project->year ? ['icon' => 'fas fa-calendar-check', 'value' => $project->year, 'label' => 'Năm triển khai'] : null,
        $project->investor ? ['icon' => 'fas fa-building', 'value' => Str::limit($project->investor, 18), 'label' => 'Chủ đầu tư'] : null,
        $project->address ? ['icon' => 'fas fa-location-dot', 'value' => Str::limit($project->address, 18), 'label' => 'Địa điểm'] : null,
        $project->value ? ['icon' => 'fas fa-chart-line', 'value' => $project->value, 'label' => 'Quy mô'] : null,
    ]))"
/>

<div class="bg-gray-50 dark:bg-gray-900 py-16 md:py-24">
    <div class="max-w-screen-xl mx-auto px-4">
        {{-- HEADER INFO --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 bg-white dark:bg-gray-800 rounded-sm shadow-sm border border-gray-100 dark:border-gray-700 p-8 md:p-12 mb-16">
            {{-- Chi tiết --}}
            <div class="flex flex-col justify-center order-2 lg:order-1">
                <h1 class="text-3xl md:text-4xl font-black text-brand-700 dark:text-brand-500 mb-6 uppercase tracking-tight">
                    {{$project->name}}
                </h1>
                <div class="text-gray-600 dark:text-gray-400 text-lg mb-8 leading-relaxed font-medium">
                    {!! $project->description !!}
                </div>
                
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-sm border border-gray-100 dark:border-gray-600 overflow-hidden text-sm">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-600 flex flex-col md:flex-row md:items-center gap-2">
                        <strong class="w-40 text-gray-900 dark:text-white uppercase tracking-wider text-xs">Tên dự án:</strong>
                        <span class="text-gray-700 dark:text-gray-300 flex-1">{{$project->name}}</span>
                    </div>
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-600 flex flex-col md:flex-row md:items-center gap-2">
                        <strong class="w-40 text-gray-900 dark:text-white uppercase tracking-wider text-xs">Chủ đầu tư:</strong>
                        <span class="text-gray-700 dark:text-gray-300 flex-1">{{$project->investor}}</span>
                    </div>
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-600 flex flex-col md:flex-row md:items-center gap-2">
                        <strong class="w-40 text-gray-900 dark:text-white uppercase tracking-wider text-xs">Địa chỉ:</strong>
                        <span class="text-gray-700 dark:text-gray-300 flex-1">{{$project->address}}</span>
                    </div>
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-600 flex flex-col md:flex-row md:items-center gap-2">
                        <strong class="w-40 text-gray-900 dark:text-white uppercase tracking-wider text-xs">Năm thực hiện:</strong>
                        <span class="text-gray-700 dark:text-gray-300 flex-1">{{$project->year}}</span>
                    </div>
                    <div class="px-6 py-4 flex flex-col md:flex-row md:items-center gap-2">
                        <strong class="w-40 text-gray-900 dark:text-white uppercase tracking-wider text-xs">Giá trị gói thầu:</strong>
                        <span class="text-gray-700 dark:text-gray-300 flex-1">{{number_format((float)$project->value,0,',','.')}} VNĐ</span>
                    </div>
                </div>
            </div>
            
            {{-- Ảnh nổi bật --}}
            <div class="flex items-center justify-center order-1 lg:order-2">
                <img src="{{ $project->image_id ? asset($project->image->url) : asset('images/setting/no-image.png') }}" 
                     alt="{{$project->name}}" 
                     class="w-full h-auto max-h-[500px] object-cover rounded-sm shadow-md">
            </div>
        </div>

        {{-- GALLERY THƯ VIỆN ẢNH --}}
        @if(isset($images) && $images->count() > 0)
        <div class="mb-16">
            <div class="text-center mb-10">
                <h2 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight">
                    Hình ảnh thi công chi tiết
                </h2>
                <div class="w-16 h-1 bg-brand-600 mx-auto mt-4"></div>
            </div>

            <div class="bg-gray-50 dark:bg-gray-800 rounded-sm p-4 md:p-8 border border-gray-200 dark:border-gray-700 w-full mx-auto max-w-4xl">
                <div class="swiper gallery-top w-full h-[300px] md:h-[500px] mb-4">
                    <div class="swiper-wrapper">
                        @foreach($images as $img)
                            <div class="swiper-slide flex items-center justify-center">
                                <img src="{{ asset($img) }}" alt="{{ $project->name }}" loading="lazy" class="max-w-full max-h-full object-contain rounded-sm mix-blend-multiply dark:mix-blend-normal">
                                <div class="swiper-lazy-preloader swiper-lazy-preloader-brand"></div>
                            </div>
                        @endforeach
                    <div class="gallery-custom-next absolute top-1/2 -translate-y-1/2 right-4 z-10 w-10 h-10 bg-white/80 border border-gray-200 rounded-full shadow-md hover:bg-brand-600 focus:outline-none hover:text-white text-gray-600 transition-colors flex items-center justify-center cursor-pointer">
                        <i class="fas fa-chevron-right text-sm"></i>
                    </div>
                    <div class="gallery-custom-prev absolute top-1/2 -translate-y-1/2 left-4 z-10 w-10 h-10 bg-white/80 border border-gray-200 rounded-full shadow-md hover:bg-brand-600 focus:outline-none hover:text-white text-gray-600 transition-colors flex items-center justify-center cursor-pointer">
                        <i class="fas fa-chevron-left text-sm"></i>
                    </div>
                </div>

                @if($images->count() > 1)
                <div class="swiper gallery-thumbs h-20 md:h-24 w-full relative">
                    <div class="swiper-wrapper">
                        @foreach($images as $img)
                            <div class="swiper-slide bg-white dark:bg-gray-900 p-2 border-2 border-transparent hover:border-brand-500 rounded-sm overflow-hidden cursor-pointer">
                                <img src="{{ asset($img) }}" alt="Thumb" class="w-full h-full object-contain mix-blend-multiply dark:mix-blend-normal">
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- NỘI DUNG CHI TIẾT --}}
        <div class="mb-16">
            <div class="bg-white dark:bg-gray-800 p-8 md:p-12 rounded-sm shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="text-center mb-10">
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight">
                        Thông tin chi tiết
                    </h2>
                    <div class="w-16 h-1 bg-brand-600 mx-auto mt-4"></div>
                </div>
                <div class="prose prose-lg max-w-none prose-blue dark:prose-invert prose-custom font-sans">
                    @if(empty($project->content) || trim(strip_tags($project->content)) == '')
                        <div class="flex flex-col items-center justify-center py-16 bg-gray-50 dark:bg-gray-700/50 rounded-sm border border-dashed border-gray-200 dark:border-gray-600">
                            <i class="fa-solid fa-file-pen text-6xl mb-4 text-gray-300 dark:text-gray-500"></i>
                            <p class="text-gray-500 dark:text-gray-400 font-medium">Nội dung đang được cập nhật...</p>
                        </div>
                    @else
                        {!! $project->content !!}
                    @endif
                </div>

                {{-- Nút Share --}}
                <div class="mt-12 pt-8 border-t border-gray-100 dark:border-gray-700">
                    <x-social-share :title="$project->name" />
                </div>
            </div>
        </div>

        {{-- BÌNH LUẬN --}}
        <div class="mb-16 bg-white dark:bg-gray-800 p-8 md:p-12 rounded-sm shadow-sm border border-gray-100 dark:border-gray-700">
            <x-comment-list :comments="$project->approvedComments" />
            <x-comment-form :commentable="$project" type="project" />
        </div>

        {{-- DỰ ÁN LIÊN QUAN --}}
        @if($relatedProjects && $relatedProjects->count() > 0)
        <div class="mb-16">
            <div class="text-center mb-10">
                <h2 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tight">
                    Dự án tiêu biểu khác
                </h2>
                <div class="w-16 h-1 bg-brand-600 mx-auto mt-4"></div>
            </div>
            <div class="relative px-0 md:px-10">
                <div class="swiper related-project-slider py-4 -my-4 overflow-hidden">
                    <div class="swiper-wrapper">
                        @foreach($relatedProjects as $other)
                            <div class="swiper-slide h-auto">
                                <x-frontend.card 
                                    :href="$other->slug_url"
                                    :image="$other->image ? asset($other->image->url) : asset('images/setting/no-image.png')"
                                    :title="$other->name"
                                    :description="$other->investor ? 'Chủ đầu tư: ' . $other->investor : ''"
                                />
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="related-custom-prev absolute top-1/2 -translate-y-1/2 -left-5 z-10 w-10 h-10 bg-white border border-gray-200 rounded-full shadow-md hover:bg-brand-600 focus:outline-none hover:text-white text-brand-600 transition-colors hidden md:flex items-center justify-center cursor-pointer">
                    <i class="fas fa-chevron-left text-sm"></i>
                </div>
                <div class="related-custom-next absolute top-1/2 -translate-y-1/2 -right-5 z-10 w-10 h-10 bg-white border border-gray-200 rounded-full shadow-md hover:bg-brand-600 focus:outline-none hover:text-white text-brand-600 transition-colors hidden md:flex items-center justify-center cursor-pointer">
                    <i class="fas fa-chevron-right text-sm"></i>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Khởi tạo Gallery Swiper nếu có
    if (document.querySelector('.gallery-thumbs') && document.querySelector('.gallery-top')) {
        const galleryThumbs = new Swiper('.gallery-thumbs', {
            spaceBetween: 10,
            slidesPerView: 4,
            freeMode: true,
            watchSlidesProgress: true,
            breakpoints: {
                640: { slidesPerView: 5 },
                1024: { slidesPerView: 6 },
            }
        });

        const galleryTop = new Swiper('.gallery-top', {
            spaceBetween: 10,
            navigation: {
                nextEl: '.gallery-custom-next',
                prevEl: '.gallery-custom-prev',
            },
            lazy: { loadPrevNext: true, loadOnTransitionStart: true },
            preloadImages: false,
            watchSlidesProgress: true,
            thumbs: {
                swiper: galleryThumbs,
            },
            observer: true,
            observeParents: true,
        });
    }

    // Khởi tạo Related Project Swiper
    if (document.querySelector('.related-project-slider')) {
        const relatedEl = document.querySelector('.related-project-slider');
        new Swiper(relatedEl, {
            slidesPerView: 1,
            spaceBetween: 20,
            navigation: {
                nextEl: relatedEl.parentElement.querySelector('.related-custom-next'),
                prevEl: relatedEl.parentElement.querySelector('.related-custom-prev'),
            },
            breakpoints: {
                640:  { slidesPerView: 2, spaceBetween: 20 },
                1024: { slidesPerView: 3, spaceBetween: 30 }
            }
        });
    }
});
</script>
@endpush
