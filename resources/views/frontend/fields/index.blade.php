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

@php
    $overviewDescription = $pageSettings->fields_description ?? $setting->fields_description ?? null;
    $featuredCategory = $featuredFieldCategory ?? ($field_categories->first() ?? null);
    $showcaseFields = $featuredCategory?->fields ?? collect();
    $showcaseImage = $featuredCategory?->image?->url ?: 'https://placehold.co/720x720/eaf4fb/0e4a86?text=CNETPOS';
    $showcaseDescription = Str::limit(strip_tags((string) ($featuredCategory?->solution_overview ?: $featuredCategory?->description ?: $featuredCategory?->content ?: $overviewDescription)), 280);
    $businessChallenges = collect($featuredCategory?->business_challenges ?? [])
        ->filter(fn ($item) => is_array($item) && (filled($item['title'] ?? null) || filled($item['description'] ?? null)))
        ->values();
    $cnetposSolutions = collect($featuredCategory?->cnetpos_solutions ?? [])
        ->filter(fn ($item) => is_array($item) && (filled($item['title'] ?? null) || filled($item['description'] ?? null)))
        ->values();
    $keyFeatures = collect($featuredCategory?->key_features ?? [])
        ->filter(fn ($item) => is_array($item) && (filled($item['title'] ?? null) || filled($item['description'] ?? null)))
        ->values();
    $showcaseBullets = $showcaseFields->pluck('name')->filter()->take(5)->values();

    if ($showcaseBullets->isEmpty()) {
        $showcaseBullets = collect([
            'Chuẩn hóa nghiệp vụ theo mô hình vận hành thực tế',
            'Kết nối dữ liệu bán hàng, nhân sự, kho và tài chính',
            'Tối ưu báo cáo quản trị theo thời gian thực',
            'Mở rộng linh hoạt theo quy mô doanh nghiệp',
        ]);
    }

    if ($businessChallenges->isEmpty()) {
        $businessChallenges = $showcaseBullets->take(4)->map(fn ($title) => ['title' => $title, 'description' => null]);
    }

    if ($cnetposSolutions->isEmpty()) {
        $cnetposSolutions = $showcaseBullets->take(4)->map(fn ($title) => ['title' => $title, 'description' => null]);
    }

    if ($keyFeatures->isEmpty()) {
        $keyFeatures = $showcaseBullets->take(5)->map(fn ($title) => [
            'icon' => 'fas fa-layer-group',
            'title' => $title,
            'description' => null,
        ]);
    }

    $processSteps = collect($featuredCategory?->implementation_steps ?? [])
        ->filter(fn ($item) => is_array($item) && (filled($item['title'] ?? null) || filled($item['description'] ?? null)))
        ->values();

    if ($processSteps->isEmpty()) {
        $processSteps = collect([
            ['title' => 'Khảo sát & Tư vấn', 'description' => 'Hiểu đặc thù vận hành và mục tiêu doanh nghiệp.', 'icon' => 'fas fa-clipboard-check'],
            ['title' => 'Đề xuất giải pháp', 'description' => 'Phân tích lộ trình, phạm vi và cấu hình phù hợp.', 'icon' => 'fas fa-lightbulb'],
            ['title' => 'Ký kết & Chuẩn bị', 'description' => 'Thống nhất phương án, kế hoạch triển khai.', 'icon' => 'fas fa-file-signature'],
            ['title' => 'Triển khai & Đào tạo', 'description' => 'Cài đặt, cấu hình và đào tạo đội ngũ sử dụng.', 'icon' => 'fas fa-chalkboard-user'],
            ['title' => 'Chạy thử & Nghiệm thu', 'description' => 'Kiểm thử, tối ưu và nghiệm thu giải pháp.', 'icon' => 'fas fa-circle-check'],
            ['title' => 'Vận hành & Hỗ trợ', 'description' => 'Đồng hành, hỗ trợ 24/7 và phát triển lâu dài.', 'icon' => 'fas fa-headset'],
        ]);
    }

    $impactStats = collect($featuredCategory?->impact_stats ?? [])
        ->filter(fn ($item) => is_array($item) && (filled($item['value'] ?? null) || filled($item['label'] ?? null)))
        ->values();

    if ($impactStats->isEmpty()) {
        $impactStats = collect([
            ['value' => '+35%', 'label' => 'Doanh thu bình quân'],
            ['value' => '-25%', 'label' => 'Thời gian kiểm kho'],
            ['value' => '-30%', 'label' => 'Hao hụt hàng hóa'],
            ['value' => '+50%', 'label' => 'Hiệu suất nhân viên'],
        ]);
    }

    $faqs = collect($featuredCategory?->faqs ?? [])
        ->filter(fn ($item) => is_array($item) && (filled($item['question'] ?? null) || filled($item['answer'] ?? null)))
        ->values();

    if ($faqs->isEmpty()) {
        $faqs = collect([
            ['question' => 'CNETPOS có phù hợp với doanh nghiệp nhỏ không?', 'answer' => 'Có. Giải pháp có thể cấu hình theo quy mô hiện tại và mở rộng khi doanh nghiệp phát triển.'],
            ['question' => 'Chi phí triển khai được tính như thế nào?', 'answer' => 'Chi phí phụ thuộc vào phạm vi nghiệp vụ, số điểm vận hành, thiết bị và mức độ tích hợp cần triển khai.'],
            ['question' => 'Thời gian triển khai giải pháp là bao lâu?', 'answer' => 'Thông thường từ vài tuần tùy mô hình vận hành, dữ liệu hiện có và mức độ tùy biến.'],
            ['question' => 'Doanh nghiệp có được hướng dẫn sử dụng không?', 'answer' => 'Đội ngũ CNETPOS đào tạo, bàn giao tài liệu và hỗ trợ trong quá trình vận hành thực tế.'],
        ]);
    }

    $allFeaturedFields = ($featuredFields ?? collect())->values();
    $relatedProjects = ($relatedProjects ?? collect())->values();
    $tabCategories = $field_categories
        ->filter(fn ($category) => $category->fields->isNotEmpty())
        ->values();
@endphp

<div class="field-index-page">
    <div class="max-w-screen-xl mx-auto px-4">
        @if(!empty($overviewDescription))
            <div class="field-index-heading" data-aos="fade-up">
                <span>Tổng quan lĩnh vực</span>
                <h2>Chúng tôi đồng hành cùng doanh nghiệp trên mọi lĩnh vực</h2>
                <p>{{ $overviewDescription }}</p>
            </div>
        @endif

        @if(isset($field_categories) && $field_categories->isNotEmpty())
            <section class="field-showcase" data-aos="fade-up" data-aos-delay="80">
                <div class="field-showcase-media">
                    <img src="{{ $showcaseImage }}" alt="{{ $featuredCategory?->name ?? $pageTitle }}" loading="lazy" decoding="async">

                    @if($showcaseFields->first())
                        @php $storyField = $showcaseFields->first(); @endphp
                        <a href="{{ $storyField->slug_url }}" class="field-showcase-story">
                            <span>{{ $storyField->category?->name ?? $featuredCategory?->name }}</span>
                            <strong>{{ $storyField->name }}</strong>
                            <em>Xem chi tiết <i class="fas fa-arrow-right"></i></em>
                        </a>
                    @endif
                </div>

                <div class="field-showcase-main">
                    <span class="field-section-kicker">Lĩnh vực tiêu biểu</span>
                    <h2>{{ $featuredCategory?->name ?? 'Giải pháp theo lĩnh vực' }}</h2>

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
                        <a href="{{ $featuredCategory?->slug_url ?? route('frontend.fields.index') }}">
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
                            <span class="field-process-number">{{ str_pad((string) ($loop->index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                            <i class="{{ $step['icon'] ?? 'fas fa-circle-check' }}"></i>
                            <h3>{{ $step['title'] ?? $step['description'] ?? '' }}</h3>
                            <p>{{ $step['description'] ?? '' }}</p>
                        </article>
                    @endforeach
                </div>
            </section>

            @if($relatedProjects->isNotEmpty())
                <section class="field-project-section" data-aos="fade-up" data-aos-delay="140">
                    <div class="field-featured-heading">
                        <span>Dự án nổi bật</span>
                        <h2>Dự án tiêu biểu theo lĩnh vực</h2>
                    </div>

                    <div class="field-project-grid">
                        @foreach($relatedProjects as $project)
                            @php
                                $projectImage = $project->image?->url ?: 'https://placehold.co/520x360/eaf4fb/0e4a86?text=Project';
                            @endphp
                            <a href="{{ $project->slug_url }}" class="field-project-card">
                                <img src="{{ $projectImage }}" alt="{{ $project->name }}" loading="lazy" decoding="async">
                                <span>{{ $project->category?->name ?? 'Dự án' }}</span>
                                <strong>{{ $project->name }}</strong>
                                @if($project->description)
                                    <p>{{ Str::limit(strip_tags($project->description), 90) }}</p>
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
                    @foreach($tabCategories as $field_category)
                        <button type="button" class="field-tab" data-field-tab="field-panel-{{ $field_category->id }}" role="tab" aria-selected="false">
                            {{ $field_category->name }}
                        </button>
                    @endforeach
                </div>

                <div class="field-tab-panels">
                    <div class="field-tab-panel is-active" id="field-panel-all" role="tabpanel">
                        <div class="field-featured-grid">
                            @forelse($allFeaturedFields as $field)
                                @php
                                    $fieldImage = $field->image?->url ?: ($field->category?->image?->url ?? 'https://placehold.co/420x420/eaf4fb/0e4a86?text=CNETPOS');
                                @endphp
                                <a href="{{ $field->slug_url }}" class="field-mini-card">
                                    <img src="{{ $fieldImage }}" alt="{{ $field->name }}" loading="lazy" decoding="async">
                                    <span>{{ $field->category?->name ?? 'Lĩnh vực' }}</span>
                                    <strong>{{ $field->name }}</strong>
                                    <em>Xem chi tiết <i class="fas fa-arrow-right"></i></em>
                                </a>
                            @empty
                                @foreach($field_categories->take(6) as $field_category)
                                    @php
                                        $categoryImage = $field_category->image?->url ?: 'https://placehold.co/420x420/eaf4fb/0e4a86?text=CNETPOS';
                                    @endphp
                                    <a href="{{ $field_category->slug_url }}" class="field-mini-card">
                                        <img src="{{ $categoryImage }}" alt="{{ $field_category->name }}" loading="lazy" decoding="async">
                                        <span>Lĩnh vực</span>
                                        <strong>{{ $field_category->name }}</strong>
                                        <em>Xem chi tiết <i class="fas fa-arrow-right"></i></em>
                                    </a>
                                @endforeach
                            @endforelse
                        </div>
                    </div>

                    @foreach($tabCategories as $field_category)
                        <div class="field-tab-panel" id="field-panel-{{ $field_category->id }}" role="tabpanel">
                            <div class="field-featured-grid">
                                @foreach($field_category->fields->take(6) as $field)
                                    @php
                                        $fieldImage = $field->image?->url ?: ($field_category->image?->url ?? 'https://placehold.co/420x420/eaf4fb/0e4a86?text=CNETPOS');
                                    @endphp
                                    <a href="{{ $field->slug_url }}" class="field-mini-card">
                                        <img src="{{ $fieldImage }}" alt="{{ $field->name }}" loading="lazy" decoding="async">
                                        <span>{{ $field_category->name }}</span>
                                        <strong>{{ $field->name }}</strong>
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
