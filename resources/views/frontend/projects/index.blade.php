@extends('layouts.master')
@section('title', $pageTitle)
@section('meta_description', $metaDescription ?? '')
@section('meta_image', $bannerUrl ?? '')

@section('content')

    @php
        $mainProject = $projectFeature ?: $popularProjects->first();
        $topProjects = $topProjects
            ->when($mainProject, fn($items) => $items->where('id', '!=', $mainProject->id))
            ->take(3)
            ->values();
        $mainProjectImage = $mainProject?->image?->url ?? 'https://placehold.co/900x520/0b3762/ffffff?text=CNETPOS';
    @endphp

    <x-frontend.leaderboard
        :image="$bannerUrl"
        :title="$pageTitle"
        :subline="$activeCategory ? 'Dự án theo danh mục' : $pageSettings->projects_leaderboard_subline"
        :description="$activeCategory ? $activeCategory->description : ($pageSettings->projects_leaderboard_description ?: $pageSubtitle)"
        :breadcrumb="$breadcrumbs"
        :actions="$activeCategory ? [] : $pageSettings->projects_leaderboard_actions"
        :stats="$activeCategory ? [] : $pageSettings->projects_leaderboard_stats"
    />

    <section class="project-index-page">
        <div class="container mx-auto px-4 max-w-7xl">
            <form action="{{ $activeCategory ? $activeCategory->slug_url : route('frontend.projects.index') }}" method="GET"
                class="project-filter-bar">
                <div class="project-search-box">
                    <input type="search" name="q" value="{{ $keyword }}" placeholder="Tìm kiếm dự án...">
                    <button type="submit" aria-label="Tìm kiếm"><i class="fas fa-search"></i></button>
                </div>
                <select name="sort" onchange="this.form.submit()" aria-label="Sắp xếp dự án">
                    <option value="latest" @selected($sort === 'latest')>Mới nhất</option>
                    <option value="oldest" @selected($sort === 'oldest')>Cũ nhất</option>
                    <option value="featured" @selected($sort === 'featured')>Nổi bật</option>
                </select>
            </form>
            <div class="project-category-pills">
                <a href="{{ route('frontend.projects.index') }}" class="{{ !$activeCategory ? 'is-active' : '' }}">Tất
                    cả</a>
                @foreach ($projectCategories as $category)
                    <a href="{{ $category->slug_url }}"
                        class="{{ $activeCategory && $activeCategory->id === $category->id ? 'is-active' : '' }}">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
            @if ($mainProject)
                <div class="project-top-grid">
                    <article class="project-main-card">
                        <a href="{{ $mainProject->slug_url }}" class="project-main-image">
                            <img src="{{ $mainProjectImage }}" alt="{{ $mainProject->name }}" loading="eager"
                                decoding="async">
                            <span>Nổi bật</span>
                        </a>
                        <div class="project-main-body">
                            <div class="project-meta-line">
                                @if ($mainProject->category)
                                    <span>{{ $mainProject->category->name }}</span>
                                @endif
                                @if ($mainProject->year)
                                    <time>{{ $mainProject->year }}</time>
                                @endif
                            </div>
                            <h2><a href="{{ $mainProject->slug_url }}">{{ $mainProject->name }}</a></h2>
                            <p>{{ Str::limit(strip_tags($mainProject->description ?? $mainProject->content), 150) }}</p>
                            <div class="project-info-row">
                                @if ($mainProject->investor)
                                    <span><i
                                            class="fas fa-building"></i>{{ Str::limit($mainProject->investor, 34) }}</span>
                                @endif
                                @if ($mainProject->address)
                                    <span><i
                                            class="fas fa-location-dot"></i>{{ Str::limit($mainProject->address, 34) }}</span>
                                @endif
                            </div>
                            <a href="{{ $mainProject->slug_url }}" class="project-read-link">Xem chi tiết <i
                                    class="fas fa-arrow-right"></i></a>
                        </div>
                    </article>

                    <div class="project-top-list">
                        @foreach ($topProjects as $project)
                            @php
                                $projectImage =
                                    $project->image?->url ?? 'https://placehold.co/360x240/eaf4fb/0e4a86?text=Project';
                            @endphp
                            <article class="project-top-card">
                                <a href="{{ $project->slug_url }}" class="project-top-image">
                                    <img src="{{ $projectImage }}" alt="{{ $project->name }}" loading="lazy"
                                        decoding="async">
                                </a>
                                <div class="project-top-body">
                                    <div class="project-meta-line">
                                        @if ($project->category)
                                            <span>{{ $project->category->name }}</span>
                                        @endif
                                        @if ($project->year)
                                            <time>{{ $project->year }}</time>
                                        @endif
                                    </div>
                                    <h3><a href="{{ $project->slug_url }}">{{ $project->name }}</a></h3>
                                    <p>{{ Str::limit(strip_tags($project->description ?? $project->content), 90) }}</p>
                                    <a href="{{ $project->slug_url }}" class="project-read-link">Xem chi tiết <i
                                            class="fas fa-arrow-right"></i></a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif

            @if (!empty($pageSettings->projects_content) && !$activeCategory)
                <section class="project-page-content">
                    {!! $pageSettings->projects_content !!}
                </section>
            @endif

            <div class="project-content-grid">
                <main>
                    <h2 class="project-block-title">
                        {{ $activeCategory ? 'Dự án thuộc ' . $activeCategory->name : 'Tất cả dự án' }}</h2>

                    <div class="project-card-grid">
                        @forelse($projects as $project)
                            @php
                                $projectImage =
                                    $project->image?->url ?? 'https://placehold.co/360x220/eaf4fb/0e4a86?text=Project';
                            @endphp
                            <article class="project-list-card">
                                <a href="{{ $project->slug_url }}" class="project-list-image">
                                    <img src="{{ $projectImage }}" alt="{{ $project->name }}" loading="lazy"
                                        decoding="async">
                                </a>
                                <div class="project-list-body">
                                    <div class="project-meta-line">
                                        @if ($project->category)
                                            <span>{{ $project->category->name }}</span>
                                        @endif
                                        @if ($project->year)
                                            <time>{{ $project->year }}</time>
                                        @endif
                                    </div>
                                    <h3><a href="{{ $project->slug_url }}">{{ $project->name }}</a></h3>
                                    <p>{{ Str::limit(strip_tags($project->description ?? $project->content), 128) }}</p>
                                    @if ($project->investor)
                                        <small>Chủ đầu tư: {{ Str::limit($project->investor, 60) }}</small>
                                    @endif
                                    <a href="{{ $project->slug_url }}" class="project-grid-link">Xem chi tiết <i class="fas fa-arrow-right"></i></a>
                                </div>
                            </article>
                        @empty
                            <div class="project-empty-state">
                                <i class="far fa-folder-open"></i>
                                <p>Chưa có dự án phù hợp.</p>
                            </div>
                        @endforelse
                    </div>

                    @if ($projects->hasPages())
                        <div class="project-pagination">
                            {{ $projects->links() }}
                        </div>
                    @endif
                </main>

                <aside class="project-sidebar">
                    <section class="project-side-box">
                        <h3>Dự án nổi bật</h3>
                        <div class="project-featured-list">
                            @foreach ($popularProjects as $project)
                                @php
                                    $projectImage =
                                        $project->image?->url ??
                                        'https://placehold.co/140x110/eaf4fb/0e4a86?text=Project';
                                @endphp
                                <a href="{{ $project->slug_url }}">
                                    <img src="{{ $projectImage }}" alt="{{ $project->name }}" loading="lazy"
                                        decoding="async">
                                    <span>
                                        <strong>{{ Str::limit($project->name, 72) }}</strong>
                                        @if ($project->year)
                                            <time>{{ $project->year }}</time>
                                        @endif
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </section>

                    <section class="project-side-box">
                        <h3>Danh mục dự án</h3>
                        <div class="project-topic-cloud">
                            <a href="{{ route('frontend.projects.index') }}"
                                class="{{ !$activeCategory ? 'is-active' : '' }}">Tất cả danh mục</a>
                            @foreach ($projectCategories as $category)
                                <a href="{{ $category->slug_url }}"
                                    class="{{ $activeCategory && $activeCategory->id === $category->id ? 'is-active' : '' }}">
                                    {{ $category->name }} <span>{{ $category->projects_count }}</span>
                                </a>
                            @endforeach
                        </div>
                    </section>
                </aside>
            </div>
        </div>
    </section>

    <x-frontend.page-cta :title="$pageSettings->projects_cta_title" :description="$pageSettings->projects_cta_description" :link="$pageSettings->projects_cta_link" />

@endsection
