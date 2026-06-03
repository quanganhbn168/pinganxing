@extends('layouts.master')
@section('title',$setting->company_name)
@section('meta_description', $setting->meta_description)

@section('content')

@if($slides->count())
{{-- 1. FULL-WIDTH HERO SLIDER --}}
<section class="relative md:-mt-20">
    <div class="swiper hero-swiper w-full h-full pb-8 md:pb-0">
        <div class="swiper-wrapper">
            @forelse($slides as $slide)
            @php
                $hasPrimaryButton = filled($slide->button_text) || filled($slide->link);
                $hasSecondaryButton = filled($slide->button_text_2) || filled($slide->link_2);
                $hasSlideOverlay = filled($slide->subtitle)
                    || filled($slide->title)
                    || filled($slide->description)
                    || $hasPrimaryButton
                    || $hasSecondaryButton;
            @endphp
            <div class="swiper-slide hero-slide {{ $slides->count() === 1 ? 'is-single' : '' }}">
                <img
                    src="{{ $slide->image?->url ?? asset('images/placeholder.jpg') }}"
                    alt="{{ $slide->title ?? 'Banner' }}"
                    class="hero-bg"
                    loading="{{ $loop->first ? 'eager' : 'lazy' }}"
                    fetchpriority="{{ $loop->first ? 'high' : 'low' }}"
                    decoding="async"
                    width="{{ $slide->image?->width }}"
                    height="{{ $slide->image?->height }}"
                >

                @if($hasSlideOverlay)
                <div class="hero-shade"></div>
                <div class="hero-content">
                    <div class="hero-copy text-left">
                        @if($slide->subtitle)
                            <span data-hero-anim="1" class="inline-block px-4 py-1.5 bg-accent-500/20 text-accent-400 font-bold rounded text-sm uppercase mb-4 border border-accent-500/30">
                                {{ $slide->subtitle }}
                            </span>
                        @endif

                        @if($slide->title)
                        <h2 data-hero-anim="2" class="text-2xl sm:text-3xl md:text-5xl lg:text-6xl font-extrabold text-white leading-tight mb-4 md:mb-6">
                            {!! nl2br(e($slide->title)) !!}
                        </h2>
                        @endif

                        @if($slide->description)
                        <p data-hero-anim="3" class="text-sm sm:text-base md:text-xl text-gray-200 mb-6 md:mb-8 max-w-2xl leading-relaxed">
                            {!! nl2br(e($slide->description)) !!}
                        </p>
                        @endif

                        @if($hasPrimaryButton || $hasSecondaryButton)
                        <div data-hero-anim="4" class="flex flex-col sm:flex-row items-center gap-3 w-full mt-2">
                            @if($hasPrimaryButton)
                                <a href="{{ filled($slide->link) ? $slide->link : '#' }}" class="w-full sm:w-auto text-center px-10 py-3.5 bg-accent-500 hover:bg-accent-600 text-white font-bold rounded-lg transition-colors shadow-lg shadow-accent-500/30">
                                    {{ $slide->button_text ?: 'Xem chi tiết' }} <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                            @endif

                            @if($hasSecondaryButton)
                                <a href="{{ filled($slide->link_2) ? $slide->link_2 : '#' }}" class="w-full sm:w-auto text-center px-10 py-3.5 bg-white/10 hover:bg-white/20 text-white font-bold rounded-lg border border-white/40 transition-colors backdrop-blur-sm">
                                    {{ $slide->button_text_2 ?: 'Tìm hiểu thêm' }} <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
            @empty
            {{-- Không có slides, không hiển thị gì --}}
            @endforelse
        </div>
        @if($slides->count() > 1)
        <div class="swiper-pagination"></div>
        <div class="hero-custom-next absolute top-1/2 -translate-y-1/2 right-4 z-10 w-12 h-12 bg-white/10 border border-white/20 rounded-full shadow-lg backdrop-blur-sm hover:border-brand-500 hover:bg-brand-600 focus:outline-none hover:text-white text-white transition-all duration-300 hidden md:flex items-center justify-center cursor-pointer">
            <i class="fas fa-chevron-right text-lg"></i>
        </div>
        <div class="hero-custom-prev absolute top-1/2 -translate-y-1/2 left-4 z-10 w-12 h-12 bg-white/10 border border-white/20 rounded-full shadow-lg backdrop-blur-sm hover:border-brand-500 hover:bg-brand-600 focus:outline-none hover:text-white text-white transition-all duration-300 hidden md:flex items-center justify-center cursor-pointer">
            <i class="fas fa-chevron-left text-lg"></i>
        </div>
        @endif
    </div>
</section>
@endif

{{-- 2. PARTNERS BAR --}}
<section class="py-10 bg-white border-b border-gray-100" data-aos="fade-up">
    <div class="container mx-auto px-4">
        <p class="text-center text-sm font-bold text-gray-400 uppercase mb-6">Đối tác & Khách hàng tiêu biểu</p>
        <div class="overflow-hidden whitespace-nowrap relative flex">
            <!-- Fade overlays for Marquee edges -->
            <div class="absolute left-0 top-0 bottom-0 w-20 bg-gradient-to-r from-white to-transparent z-10"></div>
            <div class="absolute right-0 top-0 bottom-0 w-20 bg-gradient-to-l from-white to-transparent z-10"></div>

            <div class="animate-marquee flex gap-16 items-center flex-nowrap pr-16 bg-white shrink-0">
                @if(isset($brands) && $brands->count())
                    @foreach($brands as $brand)
                    <img src="{{ $brand->image?->url }}" alt="{{ $brand->name }}" class="h-10 md:h-12 object-contain transition-all shrink-0 hover:scale-105">
                    @endforeach
                    @foreach($brands as $brand)
                    <img src="{{ $brand->image?->url }}" alt="{{ $brand->name }}" class="h-10 md:h-12 object-contain transition-all shrink-0 hover:scale-105">
                    @endforeach
                @else
                    @for($i=1; $i<=5; $i++)
                    <img src="https://placehold.co/150x50/ffffff/9ca3af?text=Logo+{{$i}}" class="h-10 object-contain shrink-0">
                    @endfor
                @endif
            </div>

            <div class="animate-marquee flex gap-16 items-center flex-nowrap pr-16 bg-white shrink-0" aria-hidden="true">
                @if(isset($brands) && $brands->count())
                    @foreach($brands as $brand)
                    <img src="{{ $brand->image?->url }}" alt="{{ $brand->name }}" class="h-10 md:h-12 object-contain transition-all shrink-0 hover:scale-105">
                    @endforeach
                    @foreach($brands as $brand)
                    <img src="{{ $brand->image?->url }}" alt="{{ $brand->name }}" class="h-10 md:h-12 object-contain transition-all shrink-0 hover:scale-105">
                    @endforeach
                @else
                    @for($i=1; $i<=5; $i++)
                    <img src="https://placehold.co/150x50/ffffff/9ca3af?text=Logo+{{$i}}" class="h-10 object-contain shrink-0">
                    @endfor
                @endif
            </div>
        </div>
    </div>
</section>

{{-- 3. ABOUT / CORE VALUES --}}
@php
    $hasVideo = !empty($homeSettings->video_url) || !empty($homeSettings->video_file);
    $videoLink = !empty($homeSettings->video_url) ? $homeSettings->video_url : (!empty($homeSettings->video_file) ? asset($homeSettings->video_file) : '#');
    $introCounters = isset($homeSettings->counters) && is_array($homeSettings->counters) && count($homeSettings->counters) > 0
        ? array_slice($homeSettings->counters, 0, 4)
        : [
            ['value' => '11+', 'label' => 'Năm phát triển'],
            ['value' => '400+', 'label' => 'Khách hàng tin tưởng'],
            ['value' => '160+', 'label' => 'Nhân sự chuyên môn'],
            ['value' => '24/7', 'label' => 'Hỗ trợ tận tâm'],
        ];
    $introCounterIcons = ['fas fa-chart-line', 'fas fa-users', 'fas fa-user-tie', 'fas fa-headset'];
@endphp
<section class="intro-section py-16 md:py-20">
    <div class="container mx-auto px-4 max-w-7xl relative z-10">
        <div class="grid lg:grid-cols-[1.06fr_0.94fr] gap-10 lg:gap-16 items-center">
            <div class="intro-media order-2 lg:order-1" data-aos="fade-right">
                <div class="intro-image-frame aspect-[4/3] group">
                    <img src="{{ !empty($homeSettings->intro_image) ? asset($homeSettings->intro_image) : 'https://placehold.co/800x600/1e293b/ffffff?text=Về+Chúng+Tôi' }}" alt="{{ strip_tags($homeSettings->intro_title ?? 'Về Chúng Tôi') }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">

                    @if($hasVideo)
                    <div class="absolute inset-0 bg-black/20 flex items-center justify-center group-hover:bg-black/35 transition-colors duration-300">
                        <a href="{{ $videoLink }}" target="_blank" data-fancybox="video" class="w-16 h-16 md:w-20 md:h-20 bg-white/90 rounded-full flex items-center justify-center text-accent-500 text-xl md:text-2xl shadow-[0_0_30px_rgba(255,255,255,0.3)] hover:scale-110 hover:bg-accent-500 hover:text-white transition-all pl-1">
                            <i class="fas fa-play"></i>
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <div class="intro-panel order-1 lg:order-2" data-aos="fade-left">
                <h2 class="section-title mb-6">{!! $homeSettings->intro_title ?? 'Giới thiệu về <span class="text-brand-700">CNETPOS</span>' !!}</h2>
                <div class="text-gray-600 text-base md:text-lg mb-7 leading-relaxed prose max-w-none">
                    {!! $homeSettings->intro_description ?? 'Thành lập từ năm 2014, CNETPOS đã trở thành đơn vị cung cấp giải pháp công nghệ hàng đầu trong lĩnh vực F&B, bán lẻ, dịch vụ và quản trị vận hành.' !!}
                </div>

                @if(!empty($homeSettings->intro_features))
                <div class="grid sm:grid-cols-2 gap-4 mb-8">
                    @foreach($homeSettings->intro_features as $feature)
                    <div class="flex gap-3" data-aos="fade-up" data-aos-delay="{{ min($loop->index * 80, 240) }}">
                        <div class="text-brand-600 pt-1 text-lg"><i class="{{ $feature['icon'] ?? 'fas fa-check' }}"></i></div>
                        <div>
                            <h4 class="font-bold text-gray-900 text-sm md:text-base mb-1">{{ $feature['title'] ?? '' }}</h4>
                            <p class="text-gray-600 text-sm">{{ $feature['description'] ?? '' }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                <div class="grid grid-cols-2 gap-5 md:gap-6">
                    @foreach($introCounters as $counter)
                    <div class="intro-stat" data-aos="zoom-in" data-aos-delay="{{ min($loop->index * 80, 240) }}">
                        <div class="intro-stat-icon">
                            <i class="{{ $counter['icon'] ?? $introCounterIcons[$loop->index % count($introCounterIcons)] }}"></i>
                        </div>
                        <div>
                            <div class="font-black text-2xl md:text-3xl text-brand-900 leading-none mb-1">{{ $counter['value'] ?? '' }}</div>
                            <div class="text-xs md:text-sm text-gray-600 font-semibold">{{ $counter['label'] ?? '' }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- 3.8. FIELDS (LĨNH VỰC) --}}
@if(isset($homeFields) && $homeFields->count())
<section id="fields" class="fields-section py-16 md:py-20">
    <div class="container mx-auto px-4 max-w-7xl">
        <div class="text-center mb-10 md:mb-14" data-aos="fade-up">
            <h2 class="home-section-title center">{{ $homeSettings->fields_title ?? 'Lĩnh Vực Hoạt Động' }}</h2>
            @if(!empty($homeSettings->fields_description))
                <p class="text-gray-600 max-w-2xl mx-auto mt-4">{{ $homeSettings->fields_description }}</p>
            @endif
        </div>

        <div class="fields-grid" data-aos="fade-up" data-aos-delay="120">
            @foreach($homeFields as $field)
            @php
                $fieldImage = $field->image_id ? ($field->image?->url ?? null) : null;
                $fieldImage = $fieldImage ?: 'https://placehold.co/720x720/eaf4fb/0e4a86?text=Industry';
            @endphp
            <a href="{{ $field->slug_url }}" class="field-card group" data-aos="fade-up" data-aos-delay="{{ min($loop->index * 80, 320) }}">
                <span class="field-card-media">
                    <img src="{{ $fieldImage }}" alt="{{ $field->name }}" loading="lazy" decoding="async">
                </span>
                <span class="field-card-body">
                    <span class="field-card-title">{{ $field->name }}</span>
                    @if(!empty($field->description) || !empty($field->content))
                        <span class="field-card-text">{{ Str::limit(strip_tags($field->description ?? $field->content), 110) }}</span>
                    @endif
                    <span class="field-card-link">Tìm hiểu thêm <i class="fas fa-arrow-right"></i></span>
                </span>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- 4. CORE SERVICES --}}
@if(isset($homeServicesCategories) && $homeServicesCategories->count())
<section id="services" class="services-section py-16 md:py-20">
    <div class="container mx-auto px-4 max-w-7xl">

        <div class="services-layout grid grid-cols-1 lg:grid-cols-12 gap-8 relative">

            <div class="services-intro lg:col-span-4 lg:sticky lg:top-24 h-fit" data-aos="fade-right">
                <h2 class="home-section-title">{{ $homeSettings->services_title ?? 'Dịch vụ của chúng tôi' }}</h2>
                <p>{{ $homeSettings->services_description ?? 'Tư vấn, triển khai đến vận hành tối ưu, CNETPOS luôn đồng hành dài hạn cùng doanh nghiệp.' }}</p>
                <a href="{{ route('frontend.services.index') }}" class="services-all-link inline-block mt-4">
                    Xem tất cả dịch vụ <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="services-list lg:col-span-8 flex flex-col gap-4" data-aos="fade-left">
                @foreach($homeServicesCategories as $service)
                @php
                    $serviceImage = $service->banner_id ? ($service->banner?->url ?? null) : null;
                    $serviceImage = $serviceImage ?: ($service->image_id ? ($service->image?->url ?? null) : null);
                    $serviceImage = $serviceImage ?: 'https://placehold.co/1000x360/0b3762/ffffff?text=Service';
                    $serviceSummary = Str::limit(strip_tags($service->description ?? $service->content), 150);
                    $serviceBullets = collect(preg_split('/[\r\n]+|(?<=[.!?])\s+/', strip_tags($service->description ?? $service->content)))
                        ->map(fn ($item) => trim($item))
                        ->filter()
                        ->take(3);
                @endphp
                <a href="{{ $service->slug_url }}" class="service-feature-card group" style="--service-card-image: url('{{ $serviceImage }}');" data-aos="fade-up" data-aos-delay="{{ min($loop->index * 80, 240) }}">
                    <span class="service-feature-content">
                        <span class="service-feature-title">{{ $service->name }}</span>
                        @if($serviceSummary)
                            <span class="service-feature-summary">{{ $serviceSummary }}</span>
                        @endif
                        @if($serviceBullets->count())
                        <span class="service-feature-bullets">
                            @foreach($serviceBullets as $bullet)
                                <span><i class="fas fa-check"></i>{{ $bullet }}</span>
                            @endforeach
                        </span>
                        @endif
                    </span>
                </a>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif
{{-- 5. FEATURED PROJECTS (TABS) --}}
@if(isset($homeProjects) && $homeProjects->count())
@php
    $projectTabs = collect([
        (object) [
            'id' => 'all',
            'name' => 'Tất cả',
            'projects' => $homeProjects->values(),
        ],
    ]);

    if (isset($homeProjectCategories)) {
        $projectTabs = $projectTabs->merge(
            $homeProjectCategories
                ->filter(fn ($category) => $category->projects->count() > 0)
                ->map(fn ($category) => (object) [
                    'id' => $category->id,
                    'name' => $category->name,
                    'projects' => $category->projects->values(),
                ])
        );
    }
@endphp
<section id="projects" class="home-projects-section py-16 md:py-20">
    <div class="container mx-auto px-4 max-w-7xl">
        <div class="home-projects-heading" data-aos="fade-up">
            <h2>{{ $homeSettings->projects_title ?? 'Dự án tiêu biểu' }}</h2>
            <div class="home-project-tabs" role="tablist" aria-label="Danh mục dự án">
                @foreach($projectTabs as $tab)
                <button
                    type="button"
                    class="home-project-tab {{ $loop->first ? 'is-active' : '' }}"
                    data-project-tab="project-panel-{{ $tab->id }}"
                    role="tab"
                    aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                >
                    {{ $tab->name }}
                </button>
                @endforeach
            </div>
        </div>

        <div class="home-project-panels" data-aos="fade-up" data-aos-delay="120">
            @foreach($projectTabs as $tab)
            @php
                $projectList = $tab->projects->take(4)->values();
                $featuredProject = $projectList->first();
                $sideProjects = $projectList->slice(1, 3);
            @endphp
            <div class="home-project-panel {{ $loop->first ? 'is-active' : '' }}" id="project-panel-{{ $tab->id }}" role="tabpanel">
                @if($featuredProject)
                @php
                    $featuredImage = $featuredProject->image_id ? ($featuredProject->image?->url ?? null) : null;
                    $featuredImage = $featuredImage ?: 'https://placehold.co/980x620/0b3762/ffffff?text=Project';
                    $featuredSummary = Str::limit(strip_tags($featuredProject->description ?? $featuredProject->content), 120);
                    $featuredStats = collect([
                        ['value' => $featuredProject->year, 'label' => 'Năm triển khai'],
                        ['value' => $featuredProject->value, 'label' => 'Giá trị dự án'],
                        ['value' => $featuredProject->investor, 'label' => 'Chủ đầu tư'],
                    ])->filter(fn ($item) => filled($item['value']))->take(3);
                @endphp
                <div class="home-project-layout">
                    <a href="{{ $featuredProject->slug_url }}" class="home-project-featured">
                        <img src="{{ $featuredImage }}" alt="{{ $featuredProject->name }}" loading="lazy" decoding="async">
                        <span class="home-project-featured-shade"></span>
                        <span class="home-project-featured-content">
                            @if($featuredProject->category)
                                <span class="home-project-badge">{{ $featuredProject->category->name }}</span>
                            @endif
                            <span class="home-project-featured-title">{{ $featuredProject->name }}</span>
                            @if($featuredSummary)
                                <span class="home-project-featured-text">{{ $featuredSummary }}</span>
                            @endif
                            @if($featuredStats->count())
                            <span class="home-project-stats">
                                @foreach($featuredStats as $stat)
                                <span>
                                    <strong>{{ Str::limit($stat['value'], 16) }}</strong>
                                    <small>{{ $stat['label'] }}</small>
                                </span>
                                @endforeach
                            </span>
                            @endif
                            <span class="home-project-link">Xem chi tiết dự án <i class="fas fa-arrow-right"></i></span>
                        </span>
                    </a>

                    <div class="home-project-side-list">
                        @foreach($sideProjects as $project)
                        @php
                            $projectImage = $project->image_id ? ($project->image?->url ?? null) : null;
                            $projectImage = $projectImage ?: 'https://placehold.co/360x220/eaf4fb/0e4a86?text=Project';
                            $projectSummary = Str::limit(strip_tags($project->description ?? $project->content), 82);
                        @endphp
                        <a href="{{ $project->slug_url }}" class="home-project-side-card">
                            <span class="home-project-side-media">
                                <img src="{{ $projectImage }}" alt="{{ $project->name }}" loading="lazy" decoding="async">
                            </span>
                            <span class="home-project-side-body">
                                @if($project->category)
                                    <span class="home-project-side-category">{{ $project->category->name }}</span>
                                @endif
                                <strong>{{ $project->name }}</strong>
                                @if($projectSummary)
                                    <span>{{ $projectSummary }}</span>
                                @endif
                                <em>Xem chi tiết <i class="fas fa-arrow-right"></i></em>
                            </span>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            @endforeach
        </div>

        <div class="home-project-all">
            <a href="{{ route('frontend.projects.index') }}">Xem tất cả dự án <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>
</section>
@endif

{{-- 5.2. CUSTOMER TESTIMONIALS --}}
@if(isset($testimonials) && $testimonials->count())
<section class="home-testimonials-section py-12 md:py-14">
    <div class="container mx-auto px-4 max-w-7xl">
        <div class="swiper home-testimonials-swiper" data-aos="fade-up">
            <div class="swiper-wrapper">
                @foreach($testimonials as $testimonial)
                @php
                    $testimonialImage = $testimonial->image?->url ?? 'https://placehold.co/260x260/e2e8f0/0b3762?text=Khach+hang';
                    $positionParts = collect(explode('-', $testimonial->position ?? ''))
                        ->map(fn ($item) => trim($item))
                        ->filter()
                        ->values();
                    $customerTitle = $positionParts->first() ?: $testimonial->position;
                    $customerCompany = $positionParts->count() > 1 ? $positionParts->slice(1)->implode(' - ') : null;
                @endphp
                <div class="swiper-slide">
                    <article class="home-testimonial-card">
                        <div class="home-testimonial-quote">
                            <i class="fas fa-quote-left"></i>
                            <div>
                                <h2>Khách hàng nói gì</h2>
                                <p>{{ $testimonial->content }}</p>
                            </div>
                        </div>

                        <div class="home-testimonial-person">
                            <img src="{{ $testimonialImage }}" alt="{{ $testimonial->name }}" loading="lazy" decoding="async">
                            <div>
                                <strong>{{ $testimonial->name }}</strong>
                                @if($customerTitle)
                                    <span>{{ $customerTitle }}</span>
                                @endif
                                @if($customerCompany)
                                    <em>{{ $customerCompany }}</em>
                                @endif
                            </div>
                        </div>
                    </article>
                </div>
                @endforeach
            </div>
            @if($testimonials->count() > 1)
                <div class="home-testimonials-pagination swiper-pagination"></div>
            @endif
        </div>
    </div>
</section>
@endif

{{-- 6. PRODUCTS / HARDWARE --}}
@if(isset($homeProducts) && $homeProducts->count())
@php
    $fallbackProductCategories = $homeProducts
        ->pluck('category')
        ->filter()
        ->unique('id')
        ->values();

    $productTabs = (isset($homeCategories) && $homeCategories->count() ? $homeCategories : $fallbackProductCategories)
        ->filter(fn ($category) => $homeProducts->where('category_id', $category->id)->count() > 0)
        ->values();

    if ($productTabs->isEmpty()) {
        $productTabs = collect([(object) [
            'id' => 'featured',
            'name' => 'Sản phẩm nổi bật',
            'description' => $homeSettings->products_description ?? 'Phân phối thiết bị phần cứng, máy chủ và linh kiện mạng chuyên dụng.',
            'slug_url' => route('products.index'),
        ]]);
    }
@endphp
<section id="products" class="home-product-showcase py-16 md:py-20">
    <div class="container mx-auto px-4 max-w-7xl">
        <div class="home-product-heading" data-aos="fade-up">
            <h2>{{ $homeSettings->products_title ?? 'Sản phẩm' }}</h2>
            <div class="home-product-tabs" role="tablist" aria-label="Danh mục sản phẩm">
                @foreach($productTabs as $category)
                <button
                    type="button"
                    class="home-product-tab {{ $loop->first ? 'is-active' : '' }}"
                    data-product-tab="product-tab-{{ $category->id }}"
                    role="tab"
                    aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                >
                    {{ $category->name }}
                </button>
                @endforeach
            </div>
        </div>

        <div class="home-product-panels" data-aos="fade-up" data-aos-delay="120">
            @foreach($productTabs as $category)
            @php
                $tabProducts = $category->id === 'featured'
                    ? $homeProducts
                    : $homeProducts->where('category_id', $category->id)->values();
            @endphp
            <div class="home-product-panel {{ $loop->first ? 'is-active' : '' }}" id="product-tab-{{ $category->id }}" role="tabpanel">
                <div class="swiper home-product-swiper">
                    <div class="swiper-wrapper">
                        @foreach($tabProducts as $product)
                        @php
                            $productImage = $product->image ? ($product->image?->url ?? null) : null;
                            $productImage = $productImage ?: 'https://placehold.co/760x460/0b3762/ffffff?text=CNETPOS';
                            $productDescription = Str::limit(strip_tags($product->description ?? $product->content), 150);
                            $featureItems = collect(preg_split('/[\\r\\n]+|(?<=[.!?])\\s+/', strip_tags($product->description ?? $product->content)))
                                ->map(fn ($item) => trim($item))
                                ->filter()
                                ->take(4);
                            $defaultFeatures = [
                                ['icon' => 'fas fa-cash-register', 'title' => 'Quản lý bán hàng', 'text' => 'Theo dõi bán hàng, tồn kho và vận hành theo thời gian thực'],
                                ['icon' => 'fas fa-chart-pie', 'title' => 'Báo cáo & Phân tích', 'text' => 'Dữ liệu trực quan hỗ trợ ra quyết định nhanh hơn'],
                                ['icon' => 'fas fa-shield-alt', 'title' => 'Bảo mật cao', 'text' => 'Vận hành ổn định, phân quyền rõ ràng cho từng vai trò'],
                                ['icon' => 'fas fa-plug', 'title' => 'Tích hợp mở', 'text' => 'Kết nối linh hoạt với thiết bị và hệ thống liên quan'],
                            ];
                        @endphp
                        <div class="swiper-slide">
                            <article class="home-product-card">
                                <div class="home-product-copy">
                                    <div class="home-product-brand">
                                        <span><i class="fas fa-cube"></i></span>
                                        {{ $category->name }}
                                    </div>
                                    <h3>{{ $product->name }}</h3>
                                    @if($productDescription)
                                        <p>{{ $productDescription }}</p>
                                    @endif
                                    <a href="{{ $product->slug_url }}" class="home-product-detail">
                                        Xem chi tiết sản phẩm <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                                <a href="{{ $product->slug_url }}" class="home-product-visual" aria-label="{{ $product->name }}">
                                    <img src="{{ $productImage }}" alt="{{ $product->name }}" loading="lazy" decoding="async">
                                </a>

                                <div class="home-product-features">
                                    @if($featureItems->count())
                                        @foreach($featureItems as $feature)
                                        <div class="home-product-feature">
                                            <span><i class="fas fa-check"></i></span>
                                            <div>
                                                <strong>{{ Str::limit($feature, 34) }}</strong>
                                                <p>{{ Str::limit($feature, 72) }}</p>
                                            </div>
                                        </div>
                                        @endforeach
                                    @else
                                        @foreach($defaultFeatures as $feature)
                                        <div class="home-product-feature">
                                            <span><i class="{{ $feature['icon'] }}"></i></span>
                                            <div>
                                                <strong>{{ $feature['title'] }}</strong>
                                                <p>{{ $feature['text'] }}</p>
                                            </div>
                                        </div>
                                        @endforeach
                                    @endif
                                </div>
                            </article>
                        </div>
                        @endforeach
                    </div>
                </div>

                @if($tabProducts->count() > 1)
                <button type="button" class="home-product-nav home-product-prev" aria-label="Sản phẩm trước">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button type="button" class="home-product-nav home-product-next" aria-label="Sản phẩm tiếp theo">
                    <i class="fas fa-chevron-right"></i>
                </button>
                @endif
            </div>
            @endforeach
        </div>

        <div class="home-product-bottom">
            <a href="{{ route('products.index') }}">
                Xem toàn bộ cửa hàng <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>
@endif

{{-- 7. LATEST NEWS --}}
<section class="home-news-section pt-16 md:pt-20 pb-14 md:pb-16">
    <div class="container mx-auto px-4 max-w-7xl">
        @if(isset($allPosts) && $allPosts->count())
        @php
            $featuredPost = $allPosts->first();
            $sidePosts = $allPosts->slice(1, 3);
            $featuredPostImage = $featuredPost->image ? ($featuredPost->image?->url ?? null) : null;
            $featuredPostImage = $featuredPostImage ?: 'https://placehold.co/720x450/eaf4fb/0e4a86?text=News';
        @endphp
        <div class="home-news-heading" data-aos="fade-up">
            <h2>{{ $homeSettings->posts_title ?? 'Tin tức & Blog' }}</h2>
            <a href="{{ route('frontend.posts.index') }}">Xem tất cả bài viết <i class="fas fa-arrow-right"></i></a>
        </div>

        <div class="home-news-layout" data-aos="fade-up" data-aos-delay="100">
            <article class="home-news-feature">
                <a href="{{ $featuredPost->slug_url }}" class="home-news-feature-image">
                    <img src="{{ $featuredPostImage }}" alt="{{ $featuredPost->title }}" loading="lazy" decoding="async">
                </a>
                <div class="home-news-feature-body">
                    @if($featuredPost->category)
                        <span class="home-news-category">{{ $featuredPost->category->name }}</span>
                    @endif
                    <h3><a href="{{ $featuredPost->slug_url }}">{{ $featuredPost->title }}</a></h3>
                    <p>{{ Str::limit(strip_tags($featuredPost->description ?? $featuredPost->content), 145) }}</p>
                    <a href="{{ $featuredPost->slug_url }}" class="home-news-link">Đọc bài viết <i class="fas fa-arrow-right"></i></a>
                </div>
            </article>

            <div class="home-news-list">
                @foreach($sidePosts as $post)
                @php
                    $postImage = $post->image ? ($post->image?->url ?? null) : null;
                    $postImage = $postImage ?: 'https://placehold.co/220x150/eaf4fb/0e4a86?text=News';
                @endphp
                <a href="{{ $post->slug_url }}" class="home-news-row">
                    <span class="home-news-row-image">
                        <img src="{{ $postImage }}" alt="{{ $post->title }}" loading="lazy" decoding="async">
                    </span>
                    <span class="home-news-row-body">
                        <strong>{{ $post->title }}</strong>
                        <span>
                            @if($post->category)
                                <em>{{ $post->category->name }}</em>
                            @endif
                            <time datetime="{{ $post->created_at->toDateString() }}">{{ $post->created_at->format('d/m/Y') }}</time>
                        </span>
                    </span>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</section>

{{-- 8. PORTALS / QUICK LINKS (Tuyển dụng - Tư vấn - Đại lý) --}}
<section class="home-actions-section pt-8 md:pt-10 pb-4 md:pb-6">
    <div class="container mx-auto px-4 max-w-7xl">
        <div class="home-action-grid" data-aos="fade-up" data-aos-delay="160">
            <a href="{{ route('frontend.careers.index') }}" class="home-action-card home-action-careers">
                <span class="home-action-content">
                    <strong>{{ $pageSettings->careers_title ?? 'Tuyển dụng' }}</strong>
                    <span>{{ $pageSettings->careers_description ?? 'Gia nhập CNETPOS để cùng kiến tạo giải pháp công nghệ cho doanh nghiệp.' }}</span>
                    <em>Xem các vị trí đang tuyển <i class="fas fa-arrow-right"></i></em>
                </span>
                <img src="{{ asset('images/setting/lien-he-bg.jpg') }}" alt="Tuyển dụng" loading="lazy" decoding="async">
            </a>

            <div class="home-consult-box">
                <h3>Tư vấn giải pháp</h3>
                <p>Đội ngũ chuyên gia của chúng tôi sẵn sàng lắng nghe và đề xuất giải pháp phù hợp nhất cho doanh nghiệp của bạn.</p>
                <form action="{{ route('consulting.store') }}" method="POST">
                    @csrf
                    <div class="home-consult-fields">
                        <label class="home-consult-field">
                            <span><i class="fas fa-user"></i></span>
                            <input type="text" name="name" required placeholder="Họ và tên*">
                        </label>
                        <label class="home-consult-field">
                            <span><i class="fas fa-phone"></i></span>
                            <input type="tel" name="phone" required placeholder="Số điện thoại*">
                        </label>
                        <label class="home-consult-field">
                            <span><i class="fas fa-envelope"></i></span>
                            <input type="email" name="email" placeholder="Email">
                        </label>
                        <label class="home-consult-field">
                            <span><i class="fas fa-comment-dots"></i></span>
                            <input type="text" name="details" placeholder="Nhu cầu tư vấn*">
                        </label>
                    </div>
                    <button type="submit">Gửi yêu cầu tư vấn</button>
                </form>
            </div>

            <a href="{{ route('agency.index') }}" class="home-action-card home-action-agency">
                <span class="home-action-content">
                    <strong>{{ $pageSettings->agency_title ?? 'Đại lý / Nhà cung cấp' }}</strong>
                    <span>{{ $pageSettings->agency_description ?? 'Cùng hợp tác, mở rộng hệ sinh thái và mang giải pháp CNETPOS đến nhiều doanh nghiệp hơn.' }}</span>
                    <em>Trở thành đối tác <i class="fas fa-arrow-right"></i></em>
                </span>
                <img src="{{ asset('images/setting/bat-tay.png') }}" alt="Đại lý / Nhà cung cấp" loading="lazy" decoding="async">
            </a>
        </div>
    </div>
</section>

@endsection

@push('jsonld')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "WebSite",
      "@id": "{{ url('/') }}#website",
      "url": "{{ url('/') }}",
      "name": "{{ $setting->site_name ?? config('app.name') }}",
      "description": "{{ $setting->meta_description ?? '' }}",
      "potentialAction": {
        "@type": "SearchAction",
        "target": "{{ route('frontend.search') }}?q={search_term_string}",
        "query-input": "required name=search_term_string"
      }
    },
    {
      "@type": "Organization",
      "@id": "{{ url('/') }}#organization",
      "name": "{{ $setting->site_name ?? config('app.name') }}",
      "url": "{{ url('/') }}",
      "logo": {
        "@type": "ImageObject",
        "url": "{{ $globalLogoUrl ?? '' }}"
      },
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "{{ $setting->phone ?? '' }}",
        "contactType": "customer service"
      },
      "sameAs": [
        "{{ $setting->facebook ?? '' }}",
        "{{ $setting->youtube ?? '' }}",
        "{{ $setting->zalo ?? '' }}"
      ]
    }
  ]
}
</script>
@endpush

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Swiper !== 'undefined') {
        const heroSwiperEl = document.querySelector('.hero-swiper');
        const heroSlideCount = heroSwiperEl ? heroSwiperEl.querySelectorAll('.swiper-slide').length : 0;

        if (heroSlideCount > 1) {
            new Swiper(heroSwiperEl, {
                loop: true,
                autoplay: {
                    delay: 7000,
                    disableOnInteraction: false
                },
                effect: 'fade',
                fadeEffect: {
                    crossFade: true
                },
                pagination: {
                    el: '.hero-swiper .swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.hero-custom-next',
                    prevEl: '.hero-custom-prev',
                }
            });
        }

        const projectSections = document.querySelectorAll('.home-projects-section');
        projectSections.forEach((section) => {
            const tabs = section.querySelectorAll('[data-project-tab]');
            const panels = section.querySelectorAll('.home-project-panel');

            tabs.forEach((tab) => {
                tab.addEventListener('click', () => {
                    const targetId = tab.getAttribute('data-project-tab');
                    const targetPanel = section.querySelector(`#${targetId}`);

                    tabs.forEach((item) => {
                        const isActive = item === tab;
                        item.classList.toggle('is-active', isActive);
                        item.setAttribute('aria-selected', isActive ? 'true' : 'false');
                    });

                    panels.forEach((panel) => {
                        panel.classList.toggle('is-active', panel === targetPanel);
                    });
                });
            });
        });

        const testimonialSwiperEl = document.querySelector('.home-testimonials-swiper');
        const testimonialSlideCount = testimonialSwiperEl ? testimonialSwiperEl.querySelectorAll('.swiper-slide').length : 0;

        if (testimonialSlideCount > 0) {
            new Swiper(testimonialSwiperEl, {
                slidesPerView: 1,
                loop: testimonialSlideCount > 1,
                speed: 650,
                autoplay: testimonialSlideCount > 1 ? {
                    delay: 6000,
                    disableOnInteraction: false,
                } : false,
                pagination: {
                    el: '.home-testimonials-pagination',
                    clickable: true,
                },
            });
        }

        const productSections = document.querySelectorAll('.home-product-showcase');
        productSections.forEach((section) => {
            const tabs = section.querySelectorAll('[data-product-tab]');
            const panels = section.querySelectorAll('.home-product-panel');
            const productSwipers = new Map();

            const initProductSwiper = (panel) => {
                if (!panel || productSwipers.has(panel.id)) {
                    return productSwipers.get(panel?.id);
                }

                const slider = panel.querySelector('.home-product-swiper');
                if (!slider) {
                    return null;
                }

                const slideCount = slider.querySelectorAll('.swiper-slide').length;
                const swiper = new Swiper(slider, {
                    slidesPerView: 1,
                    spaceBetween: 24,
                    loop: slideCount > 1,
                    grabCursor: slideCount > 1,
                    speed: 650,
                    navigation: {
                        nextEl: panel.querySelector('.home-product-next'),
                        prevEl: panel.querySelector('.home-product-prev'),
                    },
                });

                productSwipers.set(panel.id, swiper);
                return swiper;
            };

            const activateTab = (tab) => {
                const targetId = tab.getAttribute('data-product-tab');
                const targetPanel = section.querySelector(`#${targetId}`);

                tabs.forEach((item) => {
                    const isActive = item === tab;
                    item.classList.toggle('is-active', isActive);
                    item.setAttribute('aria-selected', isActive ? 'true' : 'false');
                });

                panels.forEach((panel) => {
                    panel.classList.toggle('is-active', panel === targetPanel);
                });

                const swiper = initProductSwiper(targetPanel);
                if (swiper) {
                    setTimeout(() => swiper.update(), 0);
                }
            };

            tabs.forEach((tab) => {
                tab.addEventListener('click', () => activateTab(tab));
            });

            const activePanel = section.querySelector('.home-product-panel.is-active');
            initProductSwiper(activePanel);
        });

        if (window.AOS) {
            window.AOS.refresh();
        }
    }
});
</script>
@endpush
