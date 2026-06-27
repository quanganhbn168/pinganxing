@extends('layouts.master')
@section('title', $post->title)
@section('meta_description', $post->description ?? Str::limit(strip_tags($post->content), 155))
@section('meta_image', $postImageUrl ?: ($postBannerUrl ?: ''))

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
    "{{ $postImageUrl ?: ($postBannerUrl ?: '') }}"
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
    .news-detail-page {
        padding: 3.75rem 0 5rem;
        background:
            radial-gradient(circle at 8% 12%, rgba(14, 74, 134, .08), transparent 24rem),
            linear-gradient(180deg, #f7faff 0%, #fff 48%);
    }

    .news-detail-layout {
        display: grid;
        align-items: start;
        gap: 2rem;
    }

    .news-detail-main {
        min-width: 0;
    }

    .news-article,
    .news-comments,
    .news-related,
    .news-side-panel {
        border: 1px solid rgba(203, 213, 225, .68);
        border-radius: 1.5rem;
        background: #fff;
        box-shadow: 0 20px 52px rgba(15, 23, 42, .07);
    }

    .news-article {
        overflow: hidden;
    }

    .news-article-cover {
        position: relative;
        margin: 0;
        aspect-ratio: 16 / 9;
        overflow: hidden;
        background: linear-gradient(145deg, #dbeafe, #f8fafc);
    }

    .news-article-cover::after {
        position: absolute;
        inset: auto 0 0;
        height: 30%;
        content: '';
        pointer-events: none;
        background: linear-gradient(180deg, transparent, rgba(7, 35, 61, .18));
    }

    .news-article-cover img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .news-article-inner {
        padding: clamp(1.25rem, 4vw, 2.5rem);
    }

    .news-article-header {
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .news-article-meta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: .65rem 1rem;
        margin-bottom: 1rem;
        color: #64748b;
        font-size: .78rem;
        font-weight: 650;
    }

    .news-article-meta a {
        display: inline-flex;
        align-items: center;
        min-height: 2rem;
        padding: .45rem .75rem;
        border-radius: 999px;
        background: #eaf4ff;
        color: #0357a8;
        font-size: .68rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .news-article-meta time,
    .news-article-meta span {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
    }

    .news-article-summary {
        position: relative;
        margin: 0;
        padding: 1rem 1.1rem 1rem 1.35rem;
        border-left: 4px solid #f39221;
        border-radius: 0 .8rem .8rem 0;
        background: #f8fbff;
        color: #334155;
        font-size: 1rem;
        font-weight: 550;
        line-height: 1.75;
    }

    .news-article-content {
        color: #334155;
        font-size: 1rem;
        line-height: 1.88;
        overflow-wrap: anywhere;
    }

    .news-article-content :where(p, ul, ol, blockquote, table, figure) {
        margin: 0 0 1.2rem;
    }

    .news-article-content :where(h2, h3, h4) {
        scroll-margin-top: 7rem;
        margin: 2.2rem 0 .9rem;
        color: #0b3762;
        font-weight: 800;
        line-height: 1.35;
    }

    .news-article-content h2 { font-size: clamp(1.4rem, 2.4vw, 1.85rem); }
    .news-article-content h3 { font-size: clamp(1.18rem, 2vw, 1.45rem); }
    .news-article-content h4 { font-size: 1.08rem; }

    .news-article-content :where(ul, ol) {
        padding-left: 1.4rem;
    }

    .news-article-content ul { list-style: disc; }
    .news-article-content ol { list-style: decimal; }

    .news-article-content li {
        margin-bottom: .45rem;
        padding-left: .2rem;
    }

    .news-article-content a {
        color: #026ed3;
        font-weight: 700;
        text-decoration: underline;
        text-underline-offset: 3px;
    }

    .news-article-content :where(img, video, iframe) {
        max-width: 100%;
        border-radius: 1rem;
    }

    .news-article-content img {
        display: block;
        height: auto !important;
        margin: 1.75rem auto;
        box-shadow: 0 16px 38px rgba(15, 23, 42, .12);
    }

    .news-article-content blockquote {
        padding: 1.15rem 1.3rem;
        border-left: 4px solid #0e4a86;
        border-radius: 0 1rem 1rem 0;
        background: #eff6ff;
        color: #0f172a;
        font-style: italic;
    }

    .news-article-content table {
        display: block;
        width: 100%;
        overflow-x: auto;
        border-collapse: collapse;
    }

    .news-article-content :where(th, td) {
        padding: .7rem .8rem;
        border: 1px solid #dbe4ee;
        text-align: left;
    }
</style>
@endpush

@push('css')
<style>
    .news-article-footer {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-top: 2.5rem;
        padding: 1.15rem 1.25rem;
        border: 1px solid #dbeafe;
        border-radius: 1rem;
        background: linear-gradient(135deg, #f0f7ff, #fff);
    }

    .news-article-footer strong,
    .news-article-footer span {
        display: block;
    }

    .news-article-footer strong {
        color: #0b3762;
        font-size: .92rem;
        font-weight: 800;
    }

    .news-article-footer span {
        margin-top: .2rem;
        color: #64748b;
        font-size: .78rem;
    }

    .news-comments,
    .news-related {
        margin-top: 1.5rem;
        padding: clamp(1.2rem, 3vw, 2rem);
    }

    .news-section-heading {
        display: flex;
        align-items: end;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.35rem;
    }

    .news-section-heading span {
        display: block;
        margin-bottom: .3rem;
        color: #e0740b;
        font-size: .7rem;
        font-weight: 850;
        letter-spacing: .14em;
        text-transform: uppercase;
    }

    .news-section-heading h2 {
        color: #0b3762;
        font-size: clamp(1.35rem, 2.3vw, 1.8rem);
        font-weight: 850;
        line-height: 1.3;
    }

    .news-related-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.15rem;
    }

    .news-related-card {
        display: flex;
        min-width: 0;
        height: 100%;
        flex-direction: column;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        border-radius: 1.1rem;
        background: #fff;
        transition: border-color .25s ease, box-shadow .25s ease, transform .25s ease;
    }

    .news-related-card:hover {
        border-color: rgba(14, 74, 134, .35);
        box-shadow: 0 18px 38px rgba(15, 23, 42, .1);
        transform: translateY(-4px);
    }

    .news-related-image {
        display: block;
        aspect-ratio: 16 / 9;
        overflow: hidden;
        background: linear-gradient(145deg, #dbeafe, #f8fafc);
    }

    .news-related-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .55s ease;
    }

    .news-related-card:hover .news-related-image img {
        transform: scale(1.045);
    }

    .news-related-body {
        display: flex;
        min-width: 0;
        flex: 1;
        flex-direction: column;
        padding: 1rem;
    }

    .news-related-meta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: .4rem .7rem;
        margin-bottom: .65rem;
        color: #64748b;
        font-size: .68rem;
        font-weight: 700;
    }

    .news-related-meta em {
        color: #0357a8;
        font-style: normal;
        font-weight: 800;
        text-transform: uppercase;
    }

    .news-related-title {
        display: -webkit-box;
        overflow: hidden;
        color: #0f172a;
        font-size: 1rem;
        font-weight: 800;
        line-height: 1.45;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
    }

    .news-related-excerpt {
        display: -webkit-box;
        overflow: hidden;
        margin-top: .55rem;
        color: #64748b;
        font-size: .78rem;
        line-height: 1.6;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
    }

    .news-related-link {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        margin-top: auto;
        padding-top: .9rem;
        color: #026ed3;
        font-size: .75rem;
        font-weight: 800;
    }

    .news-detail-sidebar {
        display: grid;
        gap: 1.15rem;
        min-width: 0;
    }

    .news-side-panel {
        padding: 1.25rem;
    }

    .news-side-panel h3 {
        display: flex;
        align-items: center;
        gap: .6rem;
        margin-bottom: 1rem;
        color: #0b3762;
        font-size: 1rem;
        font-weight: 850;
    }

    .news-side-panel h3 i {
        color: #e0740b;
    }

    .news-toc-scroll {
        max-height: 55vh;
        overflow-y: auto;
        padding-right: .25rem;
    }

    .news-toc-scroll .toc-wrapper ul {
        display: grid;
        gap: .55rem;
        margin: 0;
    }

    .news-toc-scroll .toc-wrapper a {
        display: block;
        padding: .45rem .65rem;
        border-left: 2px solid #bfdbfe;
        color: #475569;
        font-size: .8rem;
        line-height: 1.5;
    }

    .news-toc-scroll .toc-wrapper a:hover {
        border-color: #f39221;
        background: #fff9ed;
        color: #0b3762;
    }

    .news-category-list {
        display: grid;
        gap: .55rem;
    }

    .news-category-list a {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .8rem;
        min-height: 2.65rem;
        padding: .65rem .8rem;
        border: 1px solid #e2e8f0;
        border-radius: .75rem;
        color: #475569;
        font-size: .8rem;
        font-weight: 700;
        transition: border-color .2s ease, background .2s ease, color .2s ease;
    }

    .news-category-list a:hover {
        border-color: #93c5fd;
        background: #eff6ff;
        color: #0357a8;
    }

    @media (min-width: 700px) {
        .news-related-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (min-width: 1024px) {
        .news-detail-layout {
            grid-template-columns: minmax(0, 1fr) 20rem;
        }

        .news-detail-sidebar {
            position: sticky;
            top: 7rem;
        }
    }

    @media (min-width: 1280px) {
        .news-detail-layout {
            grid-template-columns: minmax(0, 1fr) 22rem;
            gap: 2.25rem;
        }
    }

    @media (max-width: 699px) {
        .news-detail-page { padding: 2.25rem 0 3.5rem; }
        .news-detail-layout { gap: 1.5rem; }
        .news-article,
        .news-comments,
        .news-related,
        .news-side-panel { border-radius: 1.15rem; }
        .news-article-cover { aspect-ratio: 4 / 3; }
        .news-article-footer { align-items: flex-start; }
        .news-section-heading { align-items: flex-start; }
    }

    @media (prefers-reduced-motion: reduce) {
        .news-related-card,
        .news-related-image img {
            transition: none;
        }
    }
</style>
@endpush

@section('content')
@php
    $bannerUrl = $postBannerUrl ?: (!empty($setting->banner) ? asset($setting->banner) : null);
    $mainUrl = $postImageUrl;
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
    :image="$bannerUrl ?: ($pageSettings->posts_banner ?: $mainUrl)"
    :title="$post->title"
    :subline="$post->category?->name ?? 'Tin tức'"
    :description="$postSummary"
    :breadcrumb="$postBreadcrumbs"
/>

<div class="news-detail-page">
    <div class="max-w-screen-xl mx-auto px-4">
        <div class="news-detail-layout">
            <main class="news-detail-main">
                <article class="news-article">
                    @if($mainUrl)
                        <figure class="news-article-cover">
                            <img src="{{ $mainUrl }}" alt="{{ $post->title }}" loading="eager" decoding="async">
                        </figure>
                    @endif

                    <div class="news-article-inner">
                        <header class="news-article-header">
                            <div class="news-article-meta">
                                @if($post->category)
                                    <a href="{{ $post->category->slug_url }}">{{ $post->category->name }}</a>
                                @endif
                                <time datetime="{{ $post->updated_at->toDateString() }}">
                                    <i class="far fa-calendar-alt"></i>{{ $post->updated_at->format('d/m/Y') }}
                                </time>
                                <span><i class="far fa-user"></i>Admin</span>
                            </div>

                            @if($postSummary)
                                <p class="news-article-summary">{{ $postSummary }}</p>
                            @endif
                        </header>

                        <div class="news-article-content">
                            {!! $contentHtml !!}
                        </div>

                        <footer class="news-article-footer">
                            <div>
                                <strong>Chia sẻ bài viết</strong>
                                <span>Gửi nội dung này cho đội ngũ hoặc người quan tâm.</span>
                            </div>
                            <x-social-share :title="$post->title" />
                        </footer>
                    </div>
                </article>

                <section class="news-comments">
                    <x-comment-list :comments="$post->approvedComments" />
                    <x-comment-form :commentable="$post" type="post" />
                </section>

                @if ($relatedPosts->count())
                    <section class="news-related">
                        <div class="news-section-heading">
                            <div>
                                <span>Đọc thêm</span>
                                <h2>Tin tức liên quan</h2>
                            </div>
                        </div>

                        <div class="news-related-grid">
                            @foreach ($relatedPosts as $related)
                                @php
                                    $relatedImage = $relatedPostImageUrls->get($related->id)
                                        ?: asset('images/setting/no-image.png');
                                    $relatedExcerpt = Str::limit(
                                        strip_tags((string) ($related->description ?? $related->content)),
                                        105
                                    );
                                @endphp
                                <article class="news-related-card">
                                    <a href="{{ $related->slug_url }}" class="news-related-image">
                                        <img src="{{ $relatedImage }}" alt="{{ $related->title }}" loading="lazy"
                                            decoding="async">
                                    </a>
                                    <div class="news-related-body">
                                        <div class="news-related-meta">
                                            @if($related->category)
                                                <em>{{ $related->category->name }}</em>
                                            @endif
                                            <time datetime="{{ $related->created_at->toDateString() }}">
                                                {{ $related->created_at->format('d/m/Y') }}
                                            </time>
                                        </div>
                                        <a href="{{ $related->slug_url }}" class="news-related-title">
                                            {{ $related->title }}
                                        </a>
                                        @if($relatedExcerpt)
                                            <p class="news-related-excerpt">{{ $relatedExcerpt }}</p>
                                        @endif
                                        <a href="{{ $related->slug_url }}" class="news-related-link">
                                            Đọc bài viết <i class="fas fa-arrow-right" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endif
            </main>

            <aside class="news-detail-sidebar">
                @if(isset($tocList) && count($tocList) > 0)
                    <section class="news-side-panel">
                        <h3><i class="fas fa-list-ul"></i>Mục lục nội dung</h3>
                        <div class="news-toc-scroll">
                            <x-toc :list="$tocList" />
                        </div>
                    </section>
                @endif

                <section class="news-side-panel">
                    <h3><i class="fas fa-folder-open"></i>Danh mục bài viết</h3>
                    <div class="news-category-list">
                        @foreach($allCategories as $category)
                            <a href="{{ $category->slug_url }}">
                                <span>{{ $category->name }}</span>
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        @endforeach
                    </div>
                </section>

                <div class="news-contact-box">
                    @include('partials.frontend.contact_register')
                </div>
            </aside>
        </div>
    </div>
</div>
@endsection
