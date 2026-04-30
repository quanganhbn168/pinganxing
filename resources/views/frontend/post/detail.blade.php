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

@section('content')
@php
    $bannerUrl = $post->banner?->url ?: (!empty($setting->banner) ? asset($setting->banner) : null);
    $mainUrl = $post->image?->url ?: ($post->image ? $post->image?->url : null);
    $postSummary = $post->description ?? Str::limit(strip_tags((string) $post->content), 180);
    $postBreadcrumbs = [
        ['label' => 'Tin tức', 'url' => route('frontend.posts.index')],
    ];

    if ($post->category) {
        $postBreadcrumbs[] = ['label' => $post->category->name, 'url' => $post->category->slug_url];
    }

    $postBreadcrumbs[] = ['label' => $post->title];
@endphp

<x-frontend.leaderboard
    :image="$bannerUrl ?: ($mainUrl ?? $pageSettings->posts_banner)"
    :title="$post->title"
    :subline="$post->category?->name ?? 'Tin tức'"
    :description="$postSummary"
    :breadcrumb="$postBreadcrumbs"
/>

<div class="post-detail-page">
    <div class="max-w-screen-xl mx-auto px-4">
        <div class="post-detail-layout">
            <main class="post-detail-main">
                <article class="post-article-card">
                    @if($mainUrl)
                        <figure class="post-article-cover">
                            <img src="{{ $mainUrl }}" alt="{{ $post->title }}" loading="eager" decoding="async">
                        </figure>
                    @endif

                    <div class="post-article-inner">
                        <header class="post-article-header">
                            <div class="post-article-meta">
                                @if($post->category)
                                    <a href="{{ $post->category->slug_url }}">{{ $post->category->name }}</a>
                                @endif
                                <time datetime="{{ $post->updated_at->toDateString() }}">
                                    <i class="far fa-calendar-alt"></i>{{ $post->updated_at->format('d/m/Y') }}
                                </time>
                                <span><i class="far fa-user"></i>Admin</span>
                            </div>

                            @if($postSummary)
                                <p>{{ $postSummary }}</p>
                            @endif
                        </header>

                        <div class="post-detail-prose">
                            {!! $contentHtml !!}
                        </div>

                        <footer class="post-article-footer">
                            <div>
                                <strong>Chia sẻ bài viết</strong>
                                <span>Gửi nội dung này cho đội ngũ hoặc người quan tâm.</span>
                            </div>
                            <x-social-share :title="$post->title" />
                        </footer>
                    </div>
                </article>

                <section class="post-comment-panel">
                    <x-comment-list :comments="$post->approvedComments" />
                    <x-comment-form :commentable="$post" type="post" />
                </section>

                @if ($relatedPosts->count())
                    <section class="post-related-section">
                        <div class="post-section-heading">
                            <span>Đọc thêm</span>
                            <h2>Bài viết liên quan</h2>
                        </div>

                        <div class="swiper related-post-swiper post-related-swiper">
                            <div class="swiper-wrapper">
                                @foreach ($relatedPosts as $related)
                                    @php
                                        $relatedImage = $related->image?->url ?? $related->banner?->url ?? asset('images/setting/no-image.png');
                                    @endphp
                                    <div class="swiper-slide">
                                        <a href="{{ $related->slug_url }}" class="post-related-card">
                                            <span class="post-related-image">
                                                <img src="{{ $relatedImage }}" alt="{{ $related->title }}" loading="lazy" decoding="async">
                                            </span>
                                            <span class="post-related-body">
                                                @if($related->category)
                                                    <em>{{ $related->category->name }}</em>
                                                @endif
                                                <strong>{{ $related->title }}</strong>
                                                <small>Đọc tiếp <i class="fas fa-arrow-right"></i></small>
                                            </span>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                            <div class="post-related-pagination swiper-pagination"></div>
                        </div>
                    </section>
                @endif
            </main>

            <aside class="post-detail-sidebar">
                @if(isset($tocList) && count($tocList) > 0)
                    <section class="post-side-box post-toc-box">
                        <h3><i class="fas fa-list-ul"></i>Mục lục nội dung</h3>
                        <div class="post-toc-scroll">
                            <x-toc :list="$tocList" />
                        </div>
                    </section>
                @endif

                <section class="post-side-box">
                    <h3><i class="fas fa-folder-open"></i>Danh mục bài viết</h3>
                    <div class="post-category-list">
                        @foreach($allCategories as $category)
                            <a href="{{ $category->slug_url }}">
                                <span>{{ $category->name }}</span>
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        @endforeach
                    </div>
                </section>

                <div class="post-contact-box">
                    @include('partials.frontend.contact_register')
                </div>
            </aside>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
(function () {
    function initRelatedPostSwiper() {
        const relSlider = document.querySelector('.related-post-swiper');

        if (!relSlider) return;

        if (typeof Swiper === 'undefined') {
            console.warn('Swiper chưa được load.');
            return;
        }

        const paginationEl = relSlider.querySelector('.post-related-pagination');

        new Swiper(relSlider, {
            slidesPerView: 1,
            spaceBetween: 16,
            watchOverflow: true,
            pagination: {
                el: paginationEl,
                clickable: true
            },
            breakpoints: {
                640: {
                    slidesPerView: 2,
                    spaceBetween: 18
                },
                1024: {
                    slidesPerView: 3,
                    spaceBetween: 20
                }
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initRelatedPostSwiper);
    } else {
        initRelatedPostSwiper();
    }
})();
</script>
@endpush
