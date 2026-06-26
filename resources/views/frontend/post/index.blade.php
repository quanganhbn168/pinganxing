@extends('layouts.master')
@section('title', $pageTitle ?? 'Tin tức')
@section('meta_description', $pageSettings->posts_description ?? '')

@section('content')

@php
    $activeCategory = $categoryId ?: 'all';
    $mainPost = $featuredPost ?: $popularPosts->first();
    $topPosts = $heroPosts->when($mainPost, fn ($items) => $items->where('id', '!=', $mainPost->id))->take(3)->values();
    $mainPostImage = $mainPost?->image?->url ?? 'https://placehold.co/900x520/0b3762/ffffff?text=Ping+An+Xing';
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

<section class="news-hub-page">
    <div class="container mx-auto px-4 max-w-7xl">
        <form action="{{ route('frontend.posts.index') }}" method="GET" class="news-hub-toolbar">
            <div class="news-hub-search">
                <i class="fas fa-search"></i>
                <input type="search" name="q" value="{{ $keyword }}" placeholder="Tìm kiếm bài viết...">
                <button type="submit" aria-label="Tìm kiếm">Tìm</button>
            </div>

            <select name="sort" onchange="this.form.submit()" aria-label="Sắp xếp bài viết">
                <option value="latest" @selected($sort === 'latest')>Mới nhất</option>
                <option value="oldest" @selected($sort === 'oldest')>Cũ nhất</option>
                <option value="featured" @selected($sort === 'featured')>Nổi bật</option>
            </select>
        </form>

        <nav class="news-hub-categories" aria-label="Chủ đề tin tức">
            <a href="{{ route('frontend.posts.index', request()->except(['category', 'page'])) }}" class="{{ $activeCategory === 'all' ? 'is-active' : '' }}">Tất cả</a>
            @foreach($postCategories as $category)
                <a
                    href="{{ route('frontend.posts.index', array_merge(request()->except(['category', 'page']), ['category' => $category->id])) }}"
                    class="{{ (string) $activeCategory === (string) $category->id ? 'is-active' : '' }}"
                >
                    {{ $category->name }}
                </a>
            @endforeach
        </nav>

        @if($mainPost)
        <section class="news-feature-stage">
            <article class="news-feature-article">
                <a href="{{ $mainPost->slug_url }}" class="news-feature-media">
                    <img src="{{ $mainPostImage }}" alt="{{ $mainPost->title }}" loading="eager" decoding="async">
                    <span>Nổi bật</span>
                </a>
                <div class="news-feature-content">
                    <div class="news-hub-meta">
                        @if($mainPost->category)
                            <span>{{ $mainPost->category->name }}</span>
                        @endif
                        <time datetime="{{ $mainPost->created_at->toDateString() }}">{{ $mainPost->created_at->format('d/m/Y') }}</time>
                    </div>
                    <h2><a href="{{ $mainPost->slug_url }}">{{ $mainPost->title }}</a></h2>
                    <p>{{ Str::limit(strip_tags((string) ($mainPost->description ?? $mainPost->content)), 170) }}</p>
                    <a href="{{ $mainPost->slug_url }}" class="news-hub-read-link">Đọc bài viết <i class="fas fa-arrow-right"></i></a>
                </div>
            </article>

            <aside class="news-brief-board">
                <div class="news-brief-list">
                    <div class="news-brief-heading">
                        <span>Đọc nhanh</span>
                        <strong>Chọn lọc đáng chú ý</strong>
                    </div>
                @foreach($topPosts as $post)
                    <a href="{{ $post->slug_url }}" class="news-brief-item">
                        <span>{{ sprintf('%02d', $loop->iteration) }}</span>
                        <strong>{{ $post->title }}</strong>
                        <time datetime="{{ $post->created_at->toDateString() }}">{{ $post->created_at->format('d/m/Y') }}</time>
                    </a>
                @endforeach
                </div>

                <div class="news-topic-panel">
                    <h3>Chủ đề nổi bật</h3>
                    <div class="news-topic-grid">
                        <a href="{{ route('frontend.posts.index') }}" class="{{ $activeCategory === 'all' ? 'is-active' : '' }}">
                            <span>Tất cả</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                        @foreach($postCategories->take(5) as $category)
                            <a href="{{ route('frontend.posts.index', array_merge(request()->except(['category', 'page']), ['category' => $category->id])) }}">
                                <span>{{ $category->name }}</span>
                                <small>{{ $category->posts_count }}</small>
                            </a>
                        @endforeach
                    </div>
                </div>
            </aside>
        </section>
        @endif

        @if(!empty($pageSettings->posts_content))
            <section class="news-hub-content">
                {!! $pageSettings->posts_content !!}
            </section>
        @endif

        <section class="news-river-section">
            <div class="news-river-heading">
                <span>Tin mới</span>
                <h2>Tất cả bài viết</h2>
            </div>

            <div class="news-river-grid">
                @forelse($posts as $post)
                @php
                    $postImage = $post->image?->url ?? 'https://placehold.co/560x360/eaf4fb/0e4a86?text=News';
                @endphp
                    <article class="news-river-card {{ $loop->first ? 'is-wide' : '' }}">
                        <a href="{{ $post->slug_url }}" class="news-river-media">
                            <img src="{{ $postImage }}" alt="{{ $post->title }}" loading="lazy" decoding="async">
                        </a>
                        <div class="news-river-body">
                            <div class="news-hub-meta">
                                @if($post->category)
                                    <span>{{ $post->category->name }}</span>
                                @endif
                                <time datetime="{{ $post->created_at->toDateString() }}">{{ $post->created_at->format('d/m/Y') }}</time>
                            </div>
                            <h3><a href="{{ $post->slug_url }}">{{ $post->title }}</a></h3>
                            <p>{{ Str::limit(strip_tags((string) ($post->description ?? $post->content)), $loop->first ? 150 : 105) }}</p>
                            <a href="{{ $post->slug_url }}" class="news-hub-read-link">Đọc tiếp <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </article>
                @empty
                    <div class="news-empty-state">
                        <i class="far fa-newspaper"></i>
                        <p>Chưa có bài viết phù hợp.</p>
                    </div>
                @endforelse
            </div>

            @if($posts->hasPages())
                <div class="news-pagination">
                    {{ $posts->links() }}
                </div>
            @endif
        </section>

        @if($popularPosts->count())
        <section class="news-popular-strip">
            <div>
                <span>Được quan tâm</span>
                <h2>Bài viết nổi bật</h2>
            </div>
            <div class="news-popular-list">
                @foreach($popularPosts->take(4) as $post)
                    <a href="{{ $post->slug_url }}">
                        <small>{{ sprintf('%02d', $loop->iteration) }}</small>
                        <strong>{{ Str::limit($post->title, 76) }}</strong>
                    </a>
                @endforeach
            </div>
        </section>
        @endif

        <section class="news-hub-newsletter">
            <div class="news-hub-newsletter-copy">
                <span>Bản tin Ping An Xing</span>
                <h2>Nhận gợi ý hành trình và tin mới mỗi tuần</h2>
                <p>Chọn lọc những bài viết hữu ích về du lịch, dịch vụ và các chương trình mới nhất.</p>
                @error('email')
                    <div class="news-newsletter-message is-error">{{ $message }}</div>
                @enderror
            </div>
            <form action="{{ route('contact.store') }}" method="POST">
                @csrf
                <input type="hidden" name="source" value="newsletter">
                <input type="hidden" name="name" value="Đăng ký nhận bản tin">
                <input type="hidden" name="subject" value="Đăng ký nhận bản tin">
                <input type="hidden" name="message" value="Khách hàng đăng ký nhận bản tin từ trang tin tức.">
                <div class="news-hub-newsletter-field">
                    <input
                        id="newsletter-email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        placeholder="Nhập email của bạn"
                    >
                    <button type="submit">Đăng ký ngay</button>
                </div>
                <small>Chúng tôi cam kết bảo mật thông tin của bạn.</small>
            </form>
        </section>

    </div>
</section>


@endsection
