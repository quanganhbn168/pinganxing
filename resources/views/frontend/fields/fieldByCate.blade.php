@extends('layouts.master')

@section('title', $pageTitle)

@section('content')

<x-frontend.leaderboard
    :image="$bannerUrl"
    :title="$pageTitle"
    subline="Lĩnh vực hoạt động"
    :description="$current_category->description"
    :breadcrumb="$breadcrumbs"
/>

<div class="field-index-page field-detail-page">
    <div class="max-w-screen-xl mx-auto px-4">
        <section class="field-showcase field-detail-showcase {{ $impactStats->isEmpty() ? 'without-impact' : '' }}" data-aos="fade-up">
            <div class="field-showcase-media">
                <img src="{{ $showcaseImage }}" alt="{{ $current_category->name }}" loading="lazy" decoding="async">
            </div>

            <div class="field-showcase-main">
                <span class="field-section-kicker">Lĩnh vực tiêu biểu</span>
                <h2>{{ $current_category->name }}</h2>

                @if($showcaseDescription)
                    <p>{{ $showcaseDescription }}</p>
                @endif

                @if($keyFeatures->isNotEmpty())
                    <div class="field-detail-feature-panel">
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
                @endif

                <div class="field-showcase-actions">
                    <a href="{{ route('contact.show') }}">
                        Tư vấn giải pháp
                    </a>
                    @if($relatedProjectCards->isNotEmpty())
                        <a href="#field-projects" class="is-secondary">
                            Xem dự án tiêu biểu
                        </a>
                    @endif
                </div>
            </div>

            @if($impactStats->isNotEmpty())
                <aside class="field-impact-panel">
                    <h3>Hiệu quả đạt được</h3>
                    @foreach($impactStats as $stat)
                        <div class="field-impact-row">
                            <strong>{{ $stat['value'] ?? '' }}</strong>
                            <span>{{ $stat['label'] ?? '' }}</span>
                        </div>
                    @endforeach
                </aside>
            @endif
        </section>

        @if($businessChallenges->isNotEmpty())
            <section class="field-detail-band is-challenge" data-aos="fade-up" data-aos-delay="80">
                <div class="field-detail-band-copy">
                    <span class="field-section-kicker">Thách thức doanh nghiệp</span>
                    <h2>Những thách thức của ngành {{ $current_category->name }}</h2>
                    @if($current_category->description)
                        <p>{{ $current_category->description }}</p>
                    @endif
                </div>

                <div class="field-detail-band-list">
                    <ul class="field-check-list">
                        @foreach($businessChallenges as $challenge)
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
            </section>
        @endif

        @if($brandSolutions->isNotEmpty())
            <section class="field-detail-band is-solution" data-aos="fade-up" data-aos-delay="100">
                <div class="field-detail-band-media">
                    <img src="{{ $showcaseImage }}" alt="{{ $current_category->name }}" loading="lazy" decoding="async">
                </div>

                <div class="field-detail-band-copy">
                    <span class="field-section-kicker">Giải pháp phù hợp</span>
                    <h2>Giải pháp phù hợp cho {{ $current_category->name }}</h2>
                    <ul class="field-check-list">
                        @foreach($brandSolutions as $solution)
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
            </section>
        @endif

        @if($childCategoryCards->isNotEmpty())
            <section class="field-category-section field-detail-section" data-aos="fade-up" data-aos-delay="120">
                <div class="field-featured-heading">
                    <span>Danh mục trong lĩnh vực</span>
                    <h2>Các nhóm giải pháp liên quan</h2>
                </div>

                <div class="fields-grid">
                    @foreach($childCategoryCards as $card)
                        <a href="{{ $card['url'] }}" class="field-card group" data-aos="fade-up" data-aos-delay="{{ $card['delay'] }}">
                            <span class="field-card-media">
                                <img src="{{ $card['image'] }}" alt="{{ $card['title'] }}" loading="lazy" decoding="async">
                            </span>
                            <span class="field-card-body">
                                <span class="field-card-title">{{ $card['title'] }}</span>
                                @if(!empty($card['description']))
                                    <span class="field-card-text">{{ $card['description'] }}</span>
                                @endif
                                <span class="field-card-link">Xem chi tiết <i class="fas fa-arrow-right"></i></span>
                            </span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        @if($relatedProductCards->isNotEmpty())
            <section class="field-featured-section" data-aos="fade-up" data-aos-delay="140">
                <div class="field-featured-heading">
                    <span>Sản phẩm phù hợp</span>
                    <h2>Sản phẩm hỗ trợ triển khai</h2>
                </div>

                <div class="field-project-grid">
                    @foreach($relatedProductCards as $card)
                        <a href="{{ $card['url'] }}" class="field-project-card">
                            <img src="{{ $card['image'] }}" alt="{{ $card['title'] }}" loading="lazy" decoding="async">
                            <span>{{ $card['badge'] }}</span>
                            <strong>{{ $card['title'] }}</strong>
                            @if(!empty($card['description']))
                                <p>{{ $card['description'] }}</p>
                            @endif
                            <em>Xem sản phẩm <i class="fas fa-arrow-right"></i></em>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        @if($relatedProjectCards->isNotEmpty())
            <section id="field-projects" class="field-project-section" data-aos="fade-up" data-aos-delay="160">
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

        @if($processSteps->isNotEmpty())
            <section class="field-process" data-aos="fade-up" data-aos-delay="180">
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
        @endif

        @if($faqs->isNotEmpty())
            <section class="field-faq-section" data-aos="fade-up" data-aos-delay="200">
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
        @endif

        <x-frontend.page-cta
            :title="$pageSettings->fields_cta_title"
            :description="$pageSettings->fields_cta_description"
            :link="$pageSettings->fields_cta_link"
        />
    </div>
</div>

@endsection
