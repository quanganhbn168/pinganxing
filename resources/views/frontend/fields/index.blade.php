@extends('layouts.master')
@section('title', $pageTitle)
@section('content')



<x-frontend.leaderboard
    :image="$bannerUrl"
    :title="$pageTitle"
    :subline="$pageSettings->fields_leaderboard_subline"
    :description="$pageSettings->fields_leaderboard_description ?: ($pageSubtitle ?? null)"
    :breadcrumb="$breadcrumbs"
    :actions="$pageSettings->fields_leaderboard_actions"
    :stats="$pageSettings->fields_leaderboard_stats"
/>

<div class="field-index-page">
    <div class="max-w-screen-xl mx-auto px-4">
        <div class="field-index-heading" data-aos="fade-up">
            <span>Tổng quan lĩnh vực</span>
            <h2>Chúng tôi đồng hành cùng doanh nghiệp trên mọi lĩnh vực</h2>
            @if(!empty($overviewDescription))
                <p>{{ $overviewDescription }}</p>
            @endif
        </div>

        @if($fieldCategoryCards->isNotEmpty())
            <section class="field-category-section" data-aos="fade-up" data-aos-delay="80">
                <div class="fields-grid">
                    @foreach($fieldCategoryCards as $card)
                        <a href="{{ $card['url'] }}" class="field-card group" data-aos="fade-up" data-aos-delay="{{ $card['delay'] }}">
                            <span class="field-card-media">
                                <img src="{{ $card['image'] }}" alt="{{ $card['title'] }}" loading="lazy" decoding="async">
                            </span>
                            <span class="field-card-body">
                                <span class="field-card-title">{{ $card['title'] }}</span>
                                @if(!empty($card['description']))
                                    <span class="field-card-text">{{ $card['description'] }}</span>
                                @endif
                                <span class="field-card-link">Tìm hiểu thêm <i class="fas fa-arrow-right"></i></span>
                            </span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        @if($fieldCategoryCards->isNotEmpty())
            <section class="field-showcase" data-aos="fade-up" data-aos-delay="80">
                <div class="field-showcase-media">
                    <img src="{{ $showcaseImage }}" alt="{{ $featuredFieldCategory?->name ?? $pageTitle }}" loading="lazy" decoding="async">

                    @if($storyFieldCard)
                        <a href="{{ $storyFieldCard['url'] }}" class="field-showcase-story">
                            <span>{{ $storyFieldCard['badge'] }}</span>
                            <strong>{{ $storyFieldCard['title'] }}</strong>
                            <em>Xem chi tiết <i class="fas fa-arrow-right"></i></em>
                        </a>
                    @endif
                </div>

                <div class="field-showcase-main">
                    <span class="field-section-kicker">Lĩnh vực tiêu biểu</span>
                    <h2>{{ $featuredFieldCategory?->name ?? 'Giải pháp theo lĩnh vực' }}</h2>

                    <div class="field-showcase-columns">
                        <div class="field-info-stack">
                            @if($showcaseDescription)
                                <div>
                                    <h3>Tổng quan giải pháp</h3>
                                    <p class="field-overview-text">{{ $showcaseDescription }}</p>
                                </div>
                            @endif

                            <div>
                                <h3>Thách thức doanh nghiệp</h3>
                                <ul class="field-check-list">
                                    @foreach($businessChallenges->take(5) as $challenge)
                                        <li>
                                            <i class="fas fa-check"></i>
                                            <span>
                                                <strong>{{ $challenge['title'] ?? $challenge['description'] ?? '' }}</strong>
                                                @if(!empty($challenge['description']))
                                                    <small>{{ $challenge['description'] }}</small>
                                                @endif
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <div>
                                <h3>Giải pháp CNETPOS</h3>
                                <ul class="field-check-list">
                                    @foreach($cnetposSolutions->take(5) as $solution)
                                        <li>
                                            <i class="fas fa-check"></i>
                                            <span>
                                                <strong>{{ $solution['title'] ?? $solution['description'] ?? '' }}</strong>
                                                @if(!empty($solution['description']))
                                                    <small>{{ $solution['description'] }}</small>
                                                @endif
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <div>
                            <h3>Tính năng nổi bật</h3>
                            <ul class="field-icon-list">
                                @foreach($keyFeatures->take(6) as $feature)
                                    <li>
                                        <i class="{{ $feature['icon'] ?? 'fas fa-layer-group' }}"></i>
                                        <span>
                                            <strong>{{ $feature['title'] ?? $feature['description'] ?? '' }}</strong>
                                            @if(!empty($feature['description']))
                                                <small>{{ $feature['description'] }}</small>
                                            @endif
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <div class="field-showcase-actions">
                        <a href="{{ $featuredFieldCategory?->slug_url ?? route('frontend.fields.index') }}">
                            Tư vấn giải pháp cho ngành này
                        </a>
                        <a href="{{ route('contact.show') }}" class="is-secondary">
                            Xem demo giải pháp
                        </a>
                    </div>
                </div>

                <aside class="field-impact-panel">
                    <h3>Hiệu quả đạt được</h3>
                    @foreach($impactStats as $stat)
                        <div class="field-impact-row">
                            <strong>{{ $stat['value'] ?? '' }}</strong>
                            <span>{{ $stat['label'] ?? '' }}</span>
                        </div>
                    @endforeach
                </aside>
            </section>

            <section class="field-process" data-aos="fade-up" data-aos-delay="120">
                <h2>Quy trình triển khai giải pháp</h2>
                <div class="field-process-grid">
                    @foreach($processSteps as $step)
                        <article class="field-process-card">
                            <span class="field-process-number">{{ $step['number'] }}</span>
                            <i class="{{ $step['icon'] ?? 'fas fa-circle-check' }}"></i>
                            <h3>{{ $step['title'] ?? $step['description'] ?? '' }}</h3>
                            <p>{{ $step['description'] ?? '' }}</p>
                        </article>
                    @endforeach
                </div>
            </section>

            @if($relatedProjectCards->isNotEmpty())
                <section class="field-project-section" data-aos="fade-up" data-aos-delay="140">
                    <div class="field-featured-heading">
                        <span>Dự án nổi bật</span>
                        <h2>Dự án tiêu biểu theo lĩnh vực</h2>
                    </div>

                    <div class="field-project-grid">
                        @foreach($relatedProjectCards as $card)
                            <a href="{{ $card['url'] }}" class="field-project-card">
                                <img src="{{ $card['image'] }}" alt="{{ $card['title'] }}" loading="lazy" decoding="async">
                                <span>{{ $card['badge'] }}</span>
                                <strong>{{ $card['title'] }}</strong>
                                @if(!empty($card['description']))
                                    <p>{{ $card['description'] }}</p>
                                @endif
                                <em>Xem chi tiết <i class="fas fa-arrow-right"></i></em>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            <section class="field-featured-section" data-field-tabs data-aos="fade-up" data-aos-delay="160">
                <div class="field-featured-heading">
                    <span>Lĩnh vực nổi bật</span>
                    <h2>Giải pháp tiêu biểu theo lĩnh vực</h2>
                </div>

                <div class="field-tabs" role="tablist" aria-label="Lọc lĩnh vực nổi bật">
                    <button type="button" class="field-tab is-active" data-field-tab="field-panel-all" role="tab" aria-selected="true">
                        Tất cả
                    </button>
                    @foreach($fieldTabPanels as $panel)
                        <button type="button" class="field-tab" data-field-tab="{{ $panel['id'] }}" role="tab" aria-selected="false">
                            {{ $panel['name'] }}
                        </button>
                    @endforeach
                </div>

                <div class="field-tab-panels">
                    <div class="field-tab-panel is-active" id="field-panel-all" role="tabpanel">
                        <div class="field-featured-grid">
                            @forelse($featuredFieldCards as $card)
                                <a href="{{ $card['url'] }}" class="field-mini-card">
                                    <img src="{{ $card['image'] }}" alt="{{ $card['title'] }}" loading="lazy" decoding="async">
                                    <span>{{ $card['badge'] }}</span>
                                    <strong>{{ $card['title'] }}</strong>
                                    <em>Xem chi tiết <i class="fas fa-arrow-right"></i></em>
                                </a>
                            @empty
                                @foreach($fieldCategoryCards->take(6) as $card)
                                    <a href="{{ $card['url'] }}" class="field-mini-card">
                                        <img src="{{ $card['image'] }}" alt="{{ $card['title'] }}" loading="lazy" decoding="async">
                                        <span>Lĩnh vực</span>
                                        <strong>{{ $card['title'] }}</strong>
                                        <em>Xem chi tiết <i class="fas fa-arrow-right"></i></em>
                                    </a>
                                @endforeach
                            @endforelse
                        </div>
                    </div>

                    @foreach($fieldTabPanels as $panel)
                        <div class="field-tab-panel" id="{{ $panel['id'] }}" role="tabpanel">
                            <div class="field-featured-grid">
                                @foreach($panel['cards'] as $card)
                                    <a href="{{ $card['url'] }}" class="field-mini-card">
                                        <img src="{{ $card['image'] }}" alt="{{ $card['title'] }}" loading="lazy" decoding="async">
                                        <span>{{ $card['badge'] }}</span>
                                        <strong>{{ $card['title'] }}</strong>
                                        <em>Xem chi tiết <i class="fas fa-arrow-right"></i></em>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="field-faq-section" data-aos="fade-up" data-aos-delay="180">
                <h2>Câu hỏi thường gặp</h2>
                <div class="field-faq-grid">
                    @foreach($faqs as $faq)
                        <details>
                            <summary>{{ $faq['question'] ?? '' }}</summary>
                            <p>{{ $faq['answer'] ?? '' }}</p>
                        </details>
                    @endforeach
                </div>
            </section>

            <x-frontend.page-cta
                :title="$pageSettings->fields_cta_title"
                :description="$pageSettings->fields_cta_description"
                :link="$pageSettings->fields_cta_link"
            />
        @else
            <div class="bg-white dark:bg-gray-800 rounded-sm p-16 text-center border border-dashed border-gray-200 dark:border-gray-700 shadow-sm max-w-4xl mx-auto">
                <div class="w-24 h-24 bg-gray-50 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-folder-open text-5xl text-gray-300 dark:text-gray-500"></i>
                </div>
                <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-3">Nội dung đang cập nhật</h3>
                <p class="text-gray-500 dark:text-gray-400 text-lg">Hệ thống đang được cấu hình và làm mới tài liệu kỹ thuật cho các phân hệ lĩnh vực.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-field-tabs]').forEach(function (section) {
        const tabs = section.querySelectorAll('[data-field-tab]');
        const panels = section.querySelectorAll('.field-tab-panel');

        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                const targetId = tab.getAttribute('data-field-tab');
                const targetPanel = section.querySelector('#' + targetId);

                tabs.forEach(function (item) {
                    const isActive = item === tab;
                    item.classList.toggle('is-active', isActive);
                    item.setAttribute('aria-selected', isActive ? 'true' : 'false');
                });

                panels.forEach(function (panel) {
                    panel.classList.toggle('is-active', panel === targetPanel);
                });
            });
        });
    });
});
</script>
@endpush
