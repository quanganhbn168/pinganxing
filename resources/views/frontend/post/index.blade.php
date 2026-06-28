@extends('layouts.master')
@section('title', $pageTitle ?? 'Tin tức')
@section('meta_description', $pageSettings->posts_description ?? '')

@push('css')
<style>
    .news-page {
        position: relative;
        padding: 0 0 5rem;
        background:
            radial-gradient(circle at 8% 10%, rgba(14, 74, 134, .08), transparent 24rem),
            linear-gradient(180deg, #f4f8fd 0%, #fff 42%);
    }

    .news-page-shell {
        position: relative;
        z-index: 2;
        max-width: 80rem;
        margin: 0 auto;
        padding: 0 1rem;
    }

    .news-filter-card {
        position: relative;
        z-index: 3;
        margin-top: -2.25rem;
        padding: 1rem;
        border: 1px solid rgba(203, 213, 225, .72);
        border-radius: 1.35rem;
        background: rgba(255, 255, 255, .96);
        box-shadow: 0 20px 50px rgba(15, 23, 42, .1);
        backdrop-filter: blur(16px);
    }

    .news-filter-form {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 10rem;
        gap: .75rem;
    }

    .news-search-field {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr) auto;
        align-items: center;
        min-width: 0;
        min-height: 3.1rem;
        overflow: hidden;
        border: 1px solid #dbe4ee;
        border-radius: .85rem;
        background: #f8fafc;
    }

    .news-search-field > i {
        padding-left: 1rem;
        color: #64748b;
    }

    .news-search-field input {
        min-width: 0;
        height: 100%;
        padding: 0 .8rem;
        border: 0;
        outline: 0;
        background: transparent;
        color: #0f172a;
        font-size: .88rem;
    }

    .news-search-field button {
        align-self: stretch;
        min-width: 5rem;
        border: 0;
        background: #0e4a86;
        color: #fff;
        font-size: .78rem;
        font-weight: 800;
        transition: background .2s ease;
    }

    .news-search-field button:hover { background: #0b3762; }

    .news-sort-select {
        min-height: 3.1rem;
        padding: 0 2.2rem 0 .9rem;
        border: 1px solid #dbe4ee;
        border-radius: .85rem;
        outline: 0;
        background-color: #fff;
        color: #334155;
        font-size: .82rem;
        font-weight: 700;
    }

    .news-category-nav {
        display: flex;
        gap: .55rem;
        margin-top: .85rem;
        padding-top: .85rem;
        overflow-x: auto;
        border-top: 1px solid #edf2f7;
        scrollbar-width: none;
    }

    .news-category-nav::-webkit-scrollbar { display: none; }

    .news-category-nav a {
        display: inline-flex;
        min-height: 2.25rem;
        flex: 0 0 auto;
        align-items: center;
        gap: .4rem;
        padding: .48rem .8rem;
        border: 1px solid #e2e8f0;
        border-radius: 999px;
        background: #fff;
        color: #475569;
        font-size: .72rem;
        font-weight: 750;
        white-space: nowrap;
        transition: border-color .2s ease, background .2s ease, color .2s ease;
    }

    .news-category-nav a:hover,
    .news-category-nav a.is-active {
        border-color: #0e4a86;
        background: #0e4a86;
        color: #fff;
    }

    .news-category-nav small {
        display: inline-flex;
        min-width: 1.25rem;
        height: 1.25rem;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        background: rgba(148, 163, 184, .16);
        font-size: .62rem;
    }

    .news-category-nav a.is-active small { background: rgba(255, 255, 255, .2); }

    .news-spotlight {
        display: grid;
        gap: 1.25rem;
        margin-top: 2.25rem;
    }

    .news-lead-card,
    .news-quick-panel,
    .news-feed-card,
    .news-page-editorial {
        border: 1px solid rgba(203, 213, 225, .72);
        border-radius: 1.35rem;
        background: #fff;
        box-shadow: 0 18px 46px rgba(15, 23, 42, .07);
    }

    .news-lead-card {
        display: grid;
        min-width: 0;
        overflow: hidden;
    }

    .news-lead-media {
        position: relative;
        display: block;
        min-height: 19rem;
        overflow: hidden;
        background: #dbeafe;
    }

    .news-lead-media::after {
        position: absolute;
        inset: 0;
        content: '';
        background: linear-gradient(180deg, transparent 55%, rgba(7, 35, 61, .38));
        pointer-events: none;
    }

    .news-lead-media img,
    .news-quick-image img,
    .news-feed-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .6s ease;
    }

    .news-lead-card:hover .news-lead-media img,
    .news-feed-card:hover .news-feed-image img { transform: scale(1.035); }

    .news-lead-badge {
        position: absolute;
        z-index: 2;
        top: 1rem;
        left: 1rem;
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .48rem .75rem;
        border-radius: 999px;
        background: #f39221;
        color: #fff;
        font-size: .68rem;
        font-weight: 850;
        letter-spacing: .08em;
        text-transform: uppercase;
        box-shadow: 0 10px 24px rgba(243, 146, 33, .3);
    }

    .news-lead-content {
        display: flex;
        min-width: 0;
        flex-direction: column;
        justify-content: center;
        padding: clamp(1.35rem, 3vw, 2rem);
    }

    .news-card-meta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: .45rem .75rem;
        margin-bottom: .75rem;
        color: #64748b;
        font-size: .7rem;
        font-weight: 700;
    }

    .news-card-meta span {
        color: #0357a8;
        font-weight: 850;
        letter-spacing: .06em;
        text-transform: uppercase;
    }

    .news-lead-content h2 {
        color: #0b3762;
        font-size: clamp(1.45rem, 3vw, 2.2rem);
        font-weight: 850;
        line-height: 1.3;
        letter-spacing: -.025em;
    }

    .news-lead-content h2 a:hover,
    .news-feed-card h3 a:hover { color: #026ed3; }

    .news-lead-content p {
        margin-top: .85rem;
        color: #64748b;
        font-size: .88rem;
        line-height: 1.75;
    }

    .news-read-link {
        display: inline-flex;
        width: max-content;
        align-items: center;
        gap: .5rem;
        margin-top: 1.15rem;
        color: #0357a8;
        font-size: .78rem;
        font-weight: 850;
    }

    .news-read-link i { transition: transform .2s ease; }
    .news-read-link:hover i { transform: translateX(4px); }

    .news-quick-panel {
        padding: 1.15rem;
    }

    .news-quick-heading,
    .news-feed-heading {
        display: flex;
        align-items: end;
        justify-content: space-between;
        gap: 1rem;
    }

    .news-quick-heading {
        margin-bottom: .75rem;
        padding-bottom: .85rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .news-section-kicker {
        display: block;
        margin-bottom: .25rem;
        color: #e0740b;
        font-size: .68rem;
        font-weight: 850;
        letter-spacing: .14em;
        text-transform: uppercase;
    }

    .news-quick-heading h2,
    .news-feed-heading h2 {
        color: #0b3762;
        font-size: clamp(1.3rem, 2.4vw, 1.75rem);
        font-weight: 850;
        line-height: 1.3;
    }

    .news-quick-list {
        display: grid;
        gap: .15rem;
    }

    .news-quick-item {
        display: grid;
        grid-template-columns: 6.25rem minmax(0, 1fr);
        align-items: center;
        gap: .85rem;
        padding: .7rem 0;
        border-bottom: 1px solid #edf2f7;
    }

    .news-quick-item:last-child { border-bottom: 0; }

    .news-quick-image {
        aspect-ratio: 4 / 3;
        overflow: hidden;
        border-radius: .75rem;
        background: #eaf4fb;
    }

    .news-quick-item strong {
        display: -webkit-box;
        overflow: hidden;
        color: #1e293b;
        font-size: .82rem;
        font-weight: 800;
        line-height: 1.45;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
    }

    .news-quick-item time {
        display: block;
        margin-top: .4rem;
        color: #94a3b8;
        font-size: .67rem;
        font-weight: 650;
    }

    .news-page-editorial {
        margin-top: 2rem;
        padding: clamp(1.25rem, 3vw, 2rem);
        color: #475569;
        font-size: .9rem;
        line-height: 1.8;
    }

    .news-feed {
        margin-top: 3rem;
    }

    .news-feed-heading {
        margin-bottom: 1.25rem;
    }

    .news-feed-count {
        flex: 0 0 auto;
        padding: .45rem .7rem;
        border-radius: 999px;
        background: #eaf4ff;
        color: #0357a8;
        font-size: .7rem;
        font-weight: 800;
    }

    .news-feed-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.25rem;
    }

    .news-feed-card {
        display: flex;
        min-width: 0;
        height: 100%;
        flex-direction: column;
        overflow: hidden;
        transition: border-color .25s ease, box-shadow .25s ease, transform .25s ease;
    }

    .news-feed-card:hover {
        border-color: rgba(14, 74, 134, .32);
        box-shadow: 0 22px 48px rgba(15, 23, 42, .1);
        transform: translateY(-4px);
    }

    .news-feed-image {
        display: block;
        aspect-ratio: 16 / 10;
        overflow: hidden;
        background: #eaf4fb;
    }

    .news-feed-body {
        display: flex;
        flex: 1;
        flex-direction: column;
        padding: 1.15rem;
    }

    .news-feed-card h3 {
        display: -webkit-box;
        overflow: hidden;
        color: #0f172a;
        font-size: 1.02rem;
        font-weight: 850;
        line-height: 1.48;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
    }

    .news-feed-card p {
        display: -webkit-box;
        overflow: hidden;
        margin-top: .65rem;
        color: #64748b;
        font-size: .8rem;
        line-height: 1.68;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 3;
    }

    .news-feed-card .news-read-link {
        margin-top: auto;
        padding-top: 1rem;
    }

    .news-empty {
        grid-column: 1 / -1;
        display: flex;
        min-height: 16rem;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: .8rem;
        border: 1px dashed #bfdbfe;
        border-radius: 1.25rem;
        background: #f8fbff;
        color: #64748b;
        text-align: center;
    }

    .news-empty i { color: #0e4a86; font-size: 2rem; }

    .news-pagination {
        margin-top: 1.75rem;
    }

    .news-newsletter {
        position: relative;
        display: grid;
        gap: 1.5rem;
        margin-top: 3rem;
        padding: clamp(1.5rem, 4vw, 2.5rem);
        overflow: hidden;
        border-radius: 1.5rem;
        background:
            radial-gradient(circle at 90% 10%, rgba(243, 146, 33, .3), transparent 16rem),
            linear-gradient(135deg, #0b3762, #0e4a86);
        color: #fff;
        box-shadow: 0 24px 55px rgba(11, 55, 98, .22);
    }

    .news-newsletter h2 {
        max-width: 38rem;
        font-size: clamp(1.35rem, 3vw, 2rem);
        font-weight: 850;
        line-height: 1.35;
    }

    .news-newsletter p,
    .news-newsletter small {
        color: #dbeafe;
    }

    .news-newsletter p {
        max-width: 38rem;
        margin-top: .55rem;
        font-size: .85rem;
        line-height: 1.7;
    }

    .news-newsletter form { align-self: center; }

    .news-newsletter-field {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, .25);
        border-radius: .9rem;
        background: rgba(255, 255, 255, .1);
    }

    .news-newsletter-field input {
        min-width: 0;
        min-height: 3.15rem;
        padding: 0 1rem;
        border: 0;
        outline: 0;
        background: transparent;
        color: #fff;
        font-size: .82rem;
    }

    .news-newsletter-field input::placeholder { color: #bfdbfe; }

    .news-newsletter-field button {
        padding: 0 1.15rem;
        border: 0;
        background: #f39221;
        color: #fff;
        font-size: .75rem;
        font-weight: 850;
    }

    .news-newsletter small {
        display: block;
        margin-top: .55rem;
        font-size: .67rem;
    }

    .news-newsletter-error {
        margin-top: .65rem;
        color: #fed7aa;
        font-size: .75rem;
        font-weight: 700;
    }

    @media (min-width: 700px) {
        .news-feed-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .news-newsletter { grid-template-columns: minmax(0, 1fr) minmax(20rem, .7fr); }
    }

    @media (min-width: 1024px) {
        .news-spotlight { grid-template-columns: minmax(0, 1.55fr) minmax(19rem, .8fr); }
        .news-lead-card { grid-template-columns: minmax(0, 1.2fr) minmax(17rem, .8fr); }
        .news-lead-media { min-height: 27rem; }
        .news-feed-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }

    @media (max-width: 699px) {
        .news-page { padding-bottom: 3.5rem; }
        .news-filter-card { margin-top: -1.4rem; padding: .75rem; border-radius: 1rem; }
        .news-filter-form { grid-template-columns: 1fr; }
        .news-lead-card,
        .news-quick-panel,
        .news-feed-card,
        .news-page-editorial { border-radius: 1.05rem; }
        .news-lead-media { min-height: 14rem; }
        .news-feed { margin-top: 2.25rem; }
        .news-feed-heading { align-items: flex-start; }
        .news-newsletter { border-radius: 1.15rem; }
        .news-newsletter-field { grid-template-columns: 1fr; overflow: visible; border: 0; background: transparent; }
        .news-newsletter-field input { border: 1px solid rgba(255, 255, 255, .25); border-radius: .75rem; background: rgba(255, 255, 255, .1); }
        .news-newsletter-field button { min-height: 3rem; margin-top: .6rem; border-radius: .75rem; }
    }

    @media (prefers-reduced-motion: reduce) {
        .news-lead-media img,
        .news-feed-card,
        .news-feed-image img,
        .news-read-link i { transition: none; }
    }
</style>
@endpush

@section('content')
@php
    $activeCategory = $categoryId ?: 'all';
    $activeCategoryModel = $postCategories->firstWhere('id', (int) $categoryId);
    $feedTitle = $keyword !== ''
        ? 'Kết quả tìm kiếm'
        : ($activeCategoryModel ? $activeCategoryModel->name : 'Tất cả bài viết');
    $placeholderImage = asset('images/setting/no-image.png');
@endphp

<x-frontend.leaderboard
    :image="$postsBannerUrl ?: $pageSettings->posts_banner"
    :title="$pageTitle ?? 'Tin tức & Blog'"
    :subline="$pageSettings->posts_leaderboard_subline"
    :description="$pageSettings->posts_leaderboard_description ?: ($pageSubtitle ?? 'Cập nhật những xu hướng du lịch, kinh nghiệm hành trình và tin tức mới nhất từ Ping An Xing.')"
    :breadcrumb="$breadcrumbs"
    :actions="$pageSettings->posts_leaderboard_actions"
    :stats="$pageSettings->posts_leaderboard_stats"
/>

<section class="news-page">
    <div class="news-page-shell">
        <section class="news-filter-card" aria-label="Tìm kiếm và lọc tin tức">
            <form action="{{ route('frontend.posts.index') }}" method="GET" class="news-filter-form">
                @if($activeCategory !== 'all')
                    <input type="hidden" name="category" value="{{ $activeCategory }}">
                @endif
                <div class="news-search-field">
                    <i class="fas fa-search" aria-hidden="true"></i>
                    <input type="search" name="q" value="{{ $keyword }}" placeholder="Tìm bài viết, chủ đề..."
                        aria-label="Từ khóa tìm kiếm">
                    <button type="submit">Tìm kiếm</button>
                </div>
                <select name="sort" class="news-sort-select" onchange="this.form.submit()" aria-label="Sắp xếp bài viết">
                    <option value="latest" @selected($sort === 'latest')>Mới nhất</option>
                    <option value="oldest" @selected($sort === 'oldest')>Cũ nhất</option>
                    <option value="featured" @selected($sort === 'featured')>Nổi bật</option>
                </select>
            </form>

            <nav class="news-category-nav" aria-label="Chủ đề tin tức">
                <a href="{{ route('frontend.posts.index', request()->except(['category', 'page'])) }}"
                    class="{{ $activeCategory === 'all' ? 'is-active' : '' }}">
                    Tất cả
                </a>
                @foreach($postCategories as $category)
                    <a href="{{ route('frontend.posts.index', array_merge(request()->except(['category', 'page']), ['category' => $category->id])) }}"
                        class="{{ (string) $activeCategory === (string) $category->id ? 'is-active' : '' }}">
                        <span>{{ $category->name }}</span>
                        <small>{{ $category->posts_count }}</small>
                    </a>
                @endforeach
            </nav>
        </section>

        @if($showEditorial && $featuredPost)
            <section class="news-spotlight" aria-label="Tin tức nổi bật">
                <article class="news-lead-card">
                    <a href="{{ $featuredPost->slug_url }}" class="news-lead-media">
                        <img src="{{ $featuredPostImageUrl ?: $placeholderImage }}" alt="{{ $featuredPost->title }}"
                            loading="eager" decoding="async">
                        <span class="news-lead-badge"><i class="fas fa-bolt" aria-hidden="true"></i>Nổi bật</span>
                    </a>
                    <div class="news-lead-content">
                        <div class="news-card-meta">
                            @if($featuredPost->category)
                                <span>{{ $featuredPost->category->name }}</span>
                            @endif
                            <time datetime="{{ $featuredPost->created_at->toDateString() }}">
                                {{ $featuredPost->created_at->format('d/m/Y') }}
                            </time>
                        </div>
                        <h2><a href="{{ $featuredPost->slug_url }}">{{ $featuredPost->title }}</a></h2>
                        <p>{{ Str::limit(strip_tags((string) ($featuredPost->description ?? $featuredPost->content)), 175) }}</p>
                        <a href="{{ $featuredPost->slug_url }}" class="news-read-link">
                            Đọc bài viết <i class="fas fa-arrow-right" aria-hidden="true"></i>
                        </a>
                    </div>
                </article>

                @if($heroPosts->isNotEmpty())
                    <aside class="news-quick-panel">
                        <div class="news-quick-heading">
                            <div>
                                <span class="news-section-kicker">Mới cập nhật</span>
                                <h2>Điểm tin nhanh</h2>
                            </div>
                        </div>
                        <div class="news-quick-list">
                            @foreach($heroPosts as $heroPost)
                                <a href="{{ $heroPost->slug_url }}" class="news-quick-item">
                                    <span class="news-quick-image">
                                        <img src="{{ $heroPostImageUrls->get($heroPost->id) ?: $placeholderImage }}"
                                            alt="{{ $heroPost->title }}" loading="lazy" decoding="async">
                                    </span>
                                    <span>
                                        <strong>{{ $heroPost->title }}</strong>
                                        <time datetime="{{ $heroPost->created_at->toDateString() }}">
                                            {{ $heroPost->created_at->format('d/m/Y') }}
                                        </time>
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </aside>
                @endif
            </section>
        @endif

        @if(!empty($pageSettings->posts_content))
            <section class="news-page-editorial">
                {!! $pageSettings->posts_content !!}
            </section>
        @endif

        <section class="news-feed" id="danh-sach-tin">
            <div class="news-feed-heading">
                <div>
                    <span class="news-section-kicker">Khám phá</span>
                    <h2>{{ $feedTitle }}</h2>
                </div>
                <span class="news-feed-count">{{ $posts->total() }} bài viết</span>
            </div>

            <div class="news-feed-grid">
                @forelse($posts as $post)
                    @php
                        $postImage = $postImageUrls->get($post->id) ?: $placeholderImage;
                        $postExcerpt = Str::limit(strip_tags((string) ($post->description ?? $post->content)), 125);
                    @endphp
                    <article class="news-feed-card">
                        <a href="{{ $post->slug_url }}" class="news-feed-image">
                            <img src="{{ $postImage }}" alt="{{ $post->title }}" loading="lazy" decoding="async">
                        </a>
                        <div class="news-feed-body">
                            <div class="news-card-meta">
                                @if($post->category)
                                    <span>{{ $post->category->name }}</span>
                                @endif
                                <time datetime="{{ $post->created_at->toDateString() }}">
                                    {{ $post->created_at->format('d/m/Y') }}
                                </time>
                            </div>
                            <h3><a href="{{ $post->slug_url }}">{{ $post->title }}</a></h3>
                            @if($postExcerpt)
                                <p>{{ $postExcerpt }}</p>
                            @endif
                            <a href="{{ $post->slug_url }}" class="news-read-link">
                                Đọc tiếp <i class="fas fa-arrow-right" aria-hidden="true"></i>
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="news-empty">
                        <i class="far fa-newspaper" aria-hidden="true"></i>
                        <strong>Chưa tìm thấy bài viết phù hợp</strong>
                        <span>Thử thay đổi từ khóa hoặc chọn một chủ đề khác.</span>
                    </div>
                @endforelse
            </div>

            @if($posts->hasPages())
                <div class="news-pagination">
                    {{ $posts->links() }}
                </div>
            @endif
        </section>

        <section class="news-newsletter">
            <div>
                <span class="news-section-kicker">Bản tin Ping An Xing</span>
                <h2>Nhận gợi ý hành trình và tin mới mỗi tuần</h2>
                <p>Những nội dung hữu ích về du lịch, dịch vụ và chương trình mới được gửi gọn vào hộp thư của bạn.</p>
                @error('email')
                    <div class="news-newsletter-error">{{ $message }}</div>
                @enderror
            </div>
            <form action="{{ route('contact.store') }}" method="POST">
                @csrf
                <input type="hidden" name="source" value="newsletter">
                <input type="hidden" name="name" value="Đăng ký nhận bản tin">
                <input type="hidden" name="subject" value="Đăng ký nhận bản tin">
                <input type="hidden" name="message" value="Khách hàng đăng ký nhận bản tin từ trang tin tức.">
                <div class="news-newsletter-field">
                    <input id="newsletter-email" type="email" name="email" value="{{ old('email') }}" required
                        placeholder="Email của bạn" aria-label="Email nhận bản tin">
                    <button type="submit">Đăng ký ngay</button>
                </div>
                <small>Thông tin của bạn luôn được bảo mật.</small>
            </form>
        </section>
    </div>
</section>
@endsection
