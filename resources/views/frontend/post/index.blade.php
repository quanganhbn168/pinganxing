@extends('layouts.master')
@section('title', $pageTitle ?? 'Tin tức')
@section('meta_description', $pageSettings->posts_description ?? '')

@section('content')

@php
    $activeCategory = $categoryId ?: 'all';
    $mainPost = $featuredPost ?: $popularPosts->first();
    $topPosts = $heroPosts->when($mainPost, fn ($items) => $items->where('id', '!=', $mainPost->id))->take(3)->values();
    $mainPostImage = $mainPost?->image?->url ?? 'https://placehold.co/900x520/0b3762/ffffff?text=CNETPOS';
@endphp

<x-frontend.leaderboard
    :image="$postsBannerUrl ?: $pageSettings->posts_banner"
    :title="$pageTitle ?? 'Tin tức & Blog'"
    :subline="$pageSettings->posts_leaderboard_subline"
    :description="$pageSettings->posts_leaderboard_description ?: ($pageSubtitle ?? 'Cập nhật những xu hướng công nghệ, câu chuyện chuyển đổi số, kinh nghiệm vận hành và tin tức mới nhất từ CNETPOS.')"
    :breadcrumb="$breadcrumbs"
    :actions="$pageSettings->posts_leaderboard_actions"
    :stats="$pageSettings->posts_leaderboard_stats"
/>

<section class="news-index-page">
    <div class="container mx-auto px-4 max-w-7xl">
        <form action="{{ route('frontend.posts.index') }}" method="GET" class="news-filter-bar">
            <div class="news-search-box">
                <input type="search" name="q" value="{{ $keyword }}" placeholder="Tìm kiếm bài viết...">
                <button type="submit" aria-label="Tìm kiếm"><i class="fas fa-search"></i></button>
            </div>

            <select name="sort" onchange="this.form.submit()" aria-label="Sắp xếp bài viết">
                <option value="latest" @selected($sort === 'latest')>Mới nhất</option>
                <option value="oldest" @selected($sort === 'oldest')>Cũ nhất</option>
                <option value="featured" @selected($sort === 'featured')>Nổi bật</option>
            </select>
        </form>

        <div class="news-category-pills">
            <a href="{{ route('frontend.posts.index', request()->except(['category', 'page'])) }}" class="{{ $activeCategory === 'all' ? 'is-active' : '' }}">Tất cả</a>
            @foreach($postCategories as $category)
                <a
                    href="{{ route('frontend.posts.index', array_merge(request()->except(['category', 'page']), ['category' => $category->id])) }}"
                    class="{{ (string) $activeCategory === (string) $category->id ? 'is-active' : '' }}"
                >
                    {{ $category->name }}
                </a>
            @endforeach
        </div>

        @if($mainPost)
        <div class="news-top-grid">
            <article class="news-main-card">
                <a href="{{ $mainPost->slug_url }}" class="news-main-image">
                    <img src="{{ $mainPostImage }}" alt="{{ $mainPost->title }}" loading="eager" decoding="async">
                    <span>Nổi bật</span>
                </a>
                <div class="news-main-body">
                    <div class="news-meta-line">
                        @if($mainPost->category)
                            <span>{{ $mainPost->category->name }}</span>
                        @endif
                        <time datetime="{{ $mainPost->created_at->toDateString() }}">{{ $mainPost->created_at->format('d/m/Y') }}</time>
                    </div>
                    <h2><a href="{{ $mainPost->slug_url }}">{{ $mainPost->title }}</a></h2>
                    <p>{{ Str::limit(strip_tags($mainPost->description ?? $mainPost->content), 150) }}</p>
                    <a href="{{ $mainPost->slug_url }}" class="news-read-link">Đọc tiếp <i class="fas fa-arrow-right"></i></a>
                </div>
            </article>

            <div class="news-top-list">
                @foreach($topPosts as $post)
                @php
                    $postImage = $post->image?->url ?? 'https://placehold.co/360x240/eaf4fb/0e4a86?text=News';
                @endphp
                <article class="news-top-card">
                    <a href="{{ $post->slug_url }}" class="news-top-image">
                        <img src="{{ $postImage }}" alt="{{ $post->title }}" loading="lazy" decoding="async">
                    </a>
                    <div class="news-top-body">
                        <div class="news-meta-line">
                            @if($post->category)
                                <span>{{ $post->category->name }}</span>
                            @endif
                            <time datetime="{{ $post->created_at->toDateString() }}">{{ $post->created_at->format('d/m/Y') }}</time>
                        </div>
                        <h3><a href="{{ $post->slug_url }}">{{ $post->title }}</a></h3>
                        <p>{{ Str::limit(strip_tags($post->description ?? $post->content), 92) }}</p>
                        <a href="{{ $post->slug_url }}" class="news-read-link">Đọc tiếp <i class="fas fa-arrow-right"></i></a>
                    </div>
                </article>
                @endforeach
            </div>
        </div>
        @endif

        @if(!empty($pageSettings->posts_content))
        <section class="news-page-content">
            {!! $pageSettings->posts_content !!}
        </section>
        @endif

        <div class="news-content-grid">
            <main>
                <h2 class="news-block-title">Tất cả bài viết</h2>

                <div class="news-post-list">
                    @forelse($posts as $post)
                    @php
                        $postImage = $post->image?->url ?? 'https://placehold.co/360x220/eaf4fb/0e4a86?text=News';
                    @endphp
                    <article class="news-list-card">
                        <a href="{{ $post->slug_url }}" class="news-list-image">
                            <img src="{{ $postImage }}" alt="{{ $post->title }}" loading="lazy" decoding="async">
                        </a>
                        <div class="news-list-body">
                            <div class="news-meta-line">
                                @if($post->category)
                                    <span>{{ $post->category->name }}</span>
                                @endif
                                <time datetime="{{ $post->created_at->toDateString() }}">{{ $post->created_at->format('d/m/Y') }}</time>
                            </div>
                            <h3><a href="{{ $post->slug_url }}">{{ $post->title }}</a></h3>
                            <p>{{ Str::limit(strip_tags($post->description ?? $post->content), 130) }}</p>
                        </div>
                        <a href="{{ $post->slug_url }}" class="news-list-arrow" aria-label="Đọc {{ $post->title }}"><i class="fas fa-chevron-right"></i></a>
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
            </main>

            <aside class="news-sidebar">
                <section class="news-side-box">
                    <h3>Bài viết nổi bật</h3>
                    <div class="news-featured-list">
                        @foreach($popularPosts as $post)
                        @php
                            $postImage = $post->image?->url ?? 'https://placehold.co/140x110/eaf4fb/0e4a86?text=News';
                        @endphp
                        <a href="{{ $post->slug_url }}">
                            <img src="{{ $postImage }}" alt="{{ $post->title }}" loading="lazy" decoding="async">
                            <span>
                                <strong>{{ Str::limit($post->title, 72) }}</strong>
                                <time datetime="{{ $post->created_at->toDateString() }}">{{ $post->created_at->format('d/m/Y') }}</time>
                            </span>
                        </a>
                        @endforeach
                    </div>
                </section>

                <section class="news-side-box">
                    <h3>Chủ đề quan tâm</h3>
                    <div class="news-topic-cloud">
                        <a href="{{ route('frontend.posts.index') }}" class="{{ $activeCategory === 'all' ? 'is-active' : '' }}">Tất cả chủ đề</a>
                        @foreach($postCategories as $category)
                            <a href="{{ $category->slug_url }}">
                                {{ $category->name }} <span>{{ $category->posts_count }}</span>
                            </a>
                        @endforeach
                    </div>
                </section>
            </aside>
        </div>

        <section class="news-newsletter">
            <div class="news-newsletter-icon">
                <i class="fas fa-envelope-open-text"></i>
            </div>
            <div>
                <h2>Đăng ký nhận bản tin</h2>
                <p>Nhận những bài viết hữu ích về công nghệ, chuyển đổi số và giải pháp quản trị doanh nghiệp mới nhất từ CNETPOS.</p>
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
                <div class="news-newsletter-input-group">
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
