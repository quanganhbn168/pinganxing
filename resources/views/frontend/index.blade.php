@extends('layouts.master')
@section('title', 'Trang chủ - ' . $setting->site_name)
@section('meta_description', $setting->meta_description)

@push('css')
<style>
    /* Premium Corporate Styles */
    .hero-slide { position: relative; width: 100%; overflow: hidden; aspect-ratio: 4/3; }
    @media (min-width: 768px) { .hero-slide { aspect-ratio: 16/9; } }
    @media (min-width: 1280px) { .hero-slide { aspect-ratio: 21/9; } }
    .hero-bg { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; z-index: 0; }
    .hero-content { position: absolute; inset: 0; z-index: 2; display: flex; align-items: center; }
    
    .section-title { font-size: 1.75rem; font-weight: 800; color: #111827; margin-bottom: 1rem; position: relative; display: inline-block; line-height: 1.2; }
    @media (min-width: 768px) { .section-title { font-size: 2.25rem; } }
    .section-title::after { content: ''; position: absolute; bottom: -10px; left: 0; width: 60px; height: 4px; background-color: var(--color-accent-500); border-radius: 2px; }
    .section-title.center::after { left: 50%; transform: translateX(-50%); }
    
    .corp-card { background: #fff; border: 1px solid #f3f4f6; border-radius: 12px; transition: all 0.3s ease; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
    .corp-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.1); border-color: var(--color-brand-200); }
    
    .swiper-pagination-bullet { width: 12px; height: 12px; background: white; opacity: 0.5; }
    .swiper-pagination-bullet-active { background: var(--color-accent-500); opacity: 1; }
</style>
@endpush

@section('content')

@if($slides->count())
{{-- 1. FULL-WIDTH HERO SLIDER --}}
<section class="relative md:-mt-20">
    <div class="swiper hero-swiper w-full h-full pb-8 md:pb-0">
        <div class="swiper-wrapper">
            @forelse($slides as $slide)
            <div class="swiper-slide hero-slide">
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
                
                <div class="container mx-auto px-4 hero-content flex flex-col justify-center w-full mt-4 md:mt-0 md:h-full md:pt-20">
                    <div class="max-w-3xl text-left animate__animated animate__fadeInUp">
                        @if($slide->subtitle)
                            <span class="inline-block px-4 py-1.5 bg-accent-500/20 text-accent-400 font-bold rounded text-sm uppercase mb-4 border border-accent-500/30">
                                {{ $slide->subtitle }}
                            </span>
                        @endif
                        
                        @if($slide->title)
                        <h1 class="text-3xl md:text-5xl lg:text-6xl font-extrabold text-white leading-tight mb-4 md:mb-6">
                            {{ $slide->title }}
                        </h1>
                        @endif
                        
                        @if($slide->description)
                        <p class="text-lg md:text-xl text-gray-300 mb-8 max-w-2xl leading-relaxed">
                            {{ $slide->description }}
                        </p>
                        @endif
                        
                        @if($slide->link || $slide->button_text)
                        <div class="flex flex-col sm:flex-row items-center gap-3 w-full mt-2">
                            <a href="{{ $slide->link ?? '#' }}" class="w-full sm:w-auto text-center px-10 py-3.5 bg-accent-500 hover:bg-accent-600 text-white font-bold rounded-lg transition-colors shadow-lg shadow-accent-500/30">
                                {{ $slide->button_text }} <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
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
<section class="py-10 bg-white border-b border-gray-100">
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
<section class="py-20 bg-gray-50">
    <div class="container mx-auto px-4 max-w-7xl overflow-hidden md:overflow-visible">
        <div class="grid md:grid-cols-2 gap-10 md:gap-12 items-center">
            <div>
                <h2 class="section-title mb-6">{!! $setting->intro_title ?? 'Kiến Trúc Đột Phá<br><span class="text-brand-700">Vận Hành Bền Vững</span>' !!}</h2>
                <div class="text-gray-600 text-lg mb-8 leading-relaxed prose max-w-none">
                    {!! $setting->intro_description ?? 'Chúng tôi cung cấp hệ thống quản trị ERP toàn diện, được thiết kế theo chuẩn Enterprise khắt khe nhất. Tối ưu hóa dòng chảy kinh doanh, tự động hóa quy trình và bảo mật dữ liệu tuyệt đối.' !!}
                </div>
                
                <div class="space-y-6">
                    <div class="flex gap-4">
                        <div class="text-brand-600 pt-1 text-2xl"><i class="fas fa-shield-alt"></i></div>
                        <div>
                            <h4 class="font-bold text-gray-900 text-lg mb-1">Bảo Mật Cấp Doanh Nghiệp</h4>
                            <p class="text-gray-600 text-sm">Kiến trúc bảo mật đa lớp chuẩn quốc tế, mã hóa đầu cuối với khả năng sao lưu.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="text-brand-600 pt-1 text-2xl"><i class="fas fa-bolt"></i></div>
                        <div>
                            <h4 class="font-bold text-gray-900 text-lg mb-1">Hiệu Suất Vượt Trội</h4>
                            <p class="text-gray-600 text-sm">Xử lý hàng triệu bản ghi nhờ công nghệ tối ưu hóa truy vấn.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="relative">
                <div class="absolute -inset-4 bg-gray-200 z-0"></div>
                
                @php 
                    $hasVideo = !empty($setting->video_url) || !empty($setting->video_file); 
                    $videoLink = !empty($setting->video_url) ? $setting->video_url : (!empty($setting->video_file) ? asset($setting->video_file) : '#');
                @endphp
                
                <div class="relative z-10 border border-gray-200 overflow-hidden shadow-sm aspect-[4/3] group block">
                    <img src="{{ !empty($setting->intro_image) ? asset($setting->intro_image) : 'https://placehold.co/800x600/1e293b/ffffff?text=Về+Chúng+Tôi' }}" alt="{{ strip_tags($setting->intro_title ?? 'Về Chúng Tôi') }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    
                    @if($hasVideo)
                    <div class="absolute inset-0 bg-black/20 flex items-center justify-center group-hover:bg-black/40 transition-colors duration-300">
                        <a href="{{ $videoLink }}" target="_blank" data-fancybox="video" class="w-20 h-20 bg-white/90 rounded-full flex items-center justify-center text-accent-500 text-2xl shadow-[0_0_30px_rgba(255,255,255,0.3)] hover:scale-110 hover:bg-accent-500 hover:text-white transition-all pl-1">
                            <i class="fas fa-play"></i>
                        </a>
                    </div>
                    @endif
                </div>

                @if(!$hasVideo && (!isset($setting->counters) || count($setting->counters) == 0))
                <div class="absolute -bottom-4 left-4 md:-bottom-6 md:-left-6 bg-white p-4 md:p-6 rounded-lg shadow-xl z-20 flex items-center gap-3 md:gap-4 right-4 md:right-auto">
                    <div class="w-10 h-10 md:w-12 md:h-12 rounded-full bg-accent-100 text-accent-600 flex items-center justify-center text-lg md:text-xl font-bold">10+</div>
                    <div>
                        <div class="font-bold text-gray-900 text-sm md:text-base">Năm Kinh Nghiệm</div>
                        <div class="text-xs md:text-sm text-gray-500">Triển khai phần mềm</div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- 3.5. STATS / COUNTERS --}}
@if(isset($setting->counters) && is_array($setting->counters) && count($setting->counters) > 0)
<section class="py-12 bg-brand-900 relative overflow-hidden">
    <div class="absolute inset-0 w-full h-full opacity-10"><svg class="absolute w-full h-full" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="dots" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="2" cy="2" r="2" fill="#ffffff"/></pattern></defs><rect width="100%" height="100%" fill="url(#dots)"/></svg></div>
    <div class="container mx-auto px-4 max-w-7xl relative z-10">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 divide-x divide-brand-700/50">
            @foreach($setting->counters as $counter)
            <div class="text-center px-4">
                {{-- Corporate Style: Hidden Icons --}}
                <div class="font-black text-5xl text-white mb-2 tracking-tighter">{{ $counter['value'] ?? '' }}</div>
                <div class="text-brand-300 font-bold uppercase tracking-wider text-sm">{{ $counter['label'] ?? '' }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- 4. CORE SERVICES --}}
@if(isset($homeServices) && $homeServices->count())
<section id="services" class="py-20 bg-white">
    <div class="container mx-auto px-4 max-w-7xl">
        <div class="text-center mb-16">
            <h2 class="section-title center">{{ $setting->services_title ?? 'Dịch Vụ Cung Cấp' }}</h2>
            <p class="text-gray-600 max-w-2xl mx-auto mt-4">{{ $setting->services_description ?? 'Hệ sinh thái phần mềm quản trị chuyên sâu, đáp ứng chuẩn mực nghiệp vụ cho đa dạng ngành nghề.' }}</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($homeServices as $service)
            <div class="group relative bg-white border border-gray-200 border-t-4 border-t-transparent hover:border-t-brand-600 rounded-sm p-8 hover:shadow-lg transition-all duration-300 flex flex-col h-full">
                <div class="text-brand-600 text-4xl mb-6 transform group-hover:rotate-3 transition-transform duration-300 origin-bottom-left">
                    <i class="fas fa-cube"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-4 group-hover:text-brand-700 transition-colors">
                    <a href="{{ route('frontend.service.bySlug', $service->slug) }}" class="before:absolute before:inset-0">{{ $service->name }}</a>
                </h3>
                <p class="text-gray-600 mb-6 font-sans text-sm leading-relaxed flex-grow line-clamp-3">
                    {{ Str::limit(strip_tags($service->description ?? $service->content), 120) }}
                </p>
                <div class="mt-auto flex items-center text-brand-600 font-bold uppercase tracking-wider text-xs">
                    Xem chi tiết <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- 3.8. FIELDS (LĨNH VỰC) --}}
@if(isset($homeFields) && $homeFields->count())
<section id="fields" class="py-20 bg-gray-50 border-b border-gray-100">
    <div class="container mx-auto px-4 max-w-7xl">
        <div class="text-center mb-16">
            <h2 class="section-title center">{{ $setting->fields_title ?? 'Lĩnh Vực Hoạt Động' }}</h2>
            <p class="text-gray-600 max-w-2xl mx-auto mt-4">{{ $setting->fields_description ?? 'Nền tảng ERP của chúng tôi được thiết kế linh hoạt, đáp ứng giải pháp chuyên sâu cho từng ngành nghề đặc thù.' }}</p>
        </div>
        <div class="swiper field-swiper pb-12">
            <div class="swiper-wrapper">
                @foreach($homeFields as $field)
                <div class="swiper-slide">
                    <a href="{{ route('frontend.field.bySlug', $field->slug) }}" class="group block bg-brand-900 rounded-sm overflow-hidden shadow-sm hover:shadow-lg border border-brand-800 transition-all relative aspect-[3/4] flex flex-col justify-end text-left">
                        <img src="{{ !empty($field->image) ? asset($field->image) : 'https://placehold.co/400x533/1e293b/ffffff?text=Industry' }}" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105 opacity-50 group-hover:opacity-60">
                        <div class="absolute inset-0 bg-gradient-to-t from-brand-950 via-brand-900/60 to-transparent"></div>
                        <div class="p-6 relative z-10 w-full transform translate-y-2 group-hover:translate-y-0 transition-transform duration-300">
                            <div class="w-12 h-12 bg-white/10 backdrop-blur text-white border border-white/20 rounded-sm flex items-center justify-center text-xl mb-4 group-hover:bg-accent-500 group-hover:border-accent-500 transition-colors shadow-lg">
                                <i class="fas fa-industry"></i>
                            </div>
                            <h3 class="font-bold text-white text-lg group-hover:text-accent-400 transition-colors uppercase tracking-wider">{{ $field->name }}</h3>
                            <div class="h-0 opacity-0 group-hover:h-auto group-hover:opacity-100 transition-all duration-300 mt-2">
                                <span class="text-brand-200 text-sm flex items-center gap-2">Tìm hiểu thêm <i class="fas fa-arrow-right"></i></span>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section>
@endif

{{-- 5. FEATURED PROJECTS (TABS) --}}
@if(isset($homeProjects) && $homeProjects->count())
<section id="projects" class="py-20 bg-brand-900 border-t border-brand-800">
    <div class="container mx-auto px-4 max-w-7xl">
        <div class="text-center mb-12">
            <h2 class="section-title !text-white center">{{ $setting->projects_title ?? 'Dự Án Tiêu Biểu' }}</h2>
            <p class="text-brand-100 mt-4 max-w-2xl mx-auto">{{ $setting->projects_description ?? 'Những dự án công nghệ và công trình tiêu biểu được tín nhiệm bởi các đối tác.' }}</p>
        </div>
        
        <div class="mb-10 w-full overflow-hidden">
            <ul class="flex whitespace-nowrap justify-start md:justify-center overflow-x-auto pb-1 no-scrollbar border-b border-brand-800 text-sm font-bold text-center space-x-8" id="project-tab" data-tabs-toggle="#project-tab-content" role="tablist">
                <li role="presentation">
                    <button class="inline-block pb-4 text-brand-300 hover:text-white border-b-2 border-transparent aria-selected:border-accent-500 aria-selected:text-white transition-colors" id="all-projects-tab" data-tabs-target="#all-projects" type="button" role="tab" aria-controls="all-projects" aria-selected="true">Tất Cả</button>
                </li>
                @if(isset($homeProjectCategories))
                @foreach($homeProjectCategories as $category)
                <li role="presentation">
                    <button class="inline-block pb-4 text-brand-300 hover:text-white border-b-2 border-transparent aria-selected:border-accent-500 aria-selected:text-white transition-colors" id="cat-{{ $category->id }}-tab" data-tabs-target="#cat-{{ $category->id }}" type="button" role="tab" aria-controls="cat-{{ $category->id }}" aria-selected="false">{{ $category->name }}</button>
                </li>
                @endforeach
                @endif
            </ul>
        </div>

        <div id="project-tab-content">
            <div class="p-0" id="all-projects" role="tabpanel" aria-labelledby="all-projects-tab">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:h-[580px]">
                    @php 
                        $allList = $homeProjects->take(5);
                        $firstProject = $allList->first(); 
                        $restProjects = $allList->slice(1, 4); 
                    @endphp
                    
                    @if($firstProject)
                    <a href="{{ route('frontend.project.bySlug', $firstProject->slug) }}" class="group relative border border-brand-700 bg-brand-800 shadow-lg rounded-sm overflow-hidden flex h-72 lg:h-full">
                        <div class="absolute inset-0">
                            <img src="{{ !empty($firstProject->image) ? asset($firstProject->image) : 'https://placehold.co/800x600/1e293b/ffffff?text=Project' }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                        </div>
                        {{-- Subtle always-visible gradient --}}
                        <div class="absolute inset-0 bg-gradient-to-t from-brand-950/70 via-transparent to-transparent"></div>
                        {{-- Title always visible --}}
                        <div class="absolute bottom-0 left-0 right-0 p-6 md:p-8 transition-all duration-300 group-hover:opacity-0 group-hover:translate-y-2">
                            <h3 class="text-2xl md:text-3xl font-bold text-white">{{ $firstProject->name }}</h3>
                        </div>
                        {{-- Glass hover panel --}}
                        <div class="absolute bottom-0 left-0 right-0 translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-out backdrop-blur-md bg-brand-900/50 border-t border-white/20 p-6 md:p-8">
                            <h3 class="text-2xl md:text-3xl font-bold text-white mb-3">{{ $firstProject->name }}</h3>
                            <span class="text-accent-400 font-bold text-sm tracking-widest uppercase flex items-center gap-2">Trải nghiệm <i class="fas fa-arrow-right"></i></span>
                        </div>
                    </a>
                    @endif

                    @if($restProjects->count() > 0)
                    <div class="grid grid-cols-2 grid-rows-2 gap-3 md:gap-4 h-72 lg:h-full">
                        @foreach($restProjects as $project)
                        <a href="{{ route('frontend.project.bySlug', $project->slug) }}" class="group relative border border-brand-700 bg-brand-800 shadow-lg rounded-sm overflow-hidden flex h-full">
                            <div class="absolute inset-0">
                                <img src="{{ !empty($project->image) ? asset($project->image) : 'https://placehold.co/600x450/1e293b/ffffff?text=Project' }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            </div>
                            <div class="absolute inset-0 bg-gradient-to-t from-brand-950/70 via-transparent to-transparent"></div>
                            {{-- Title always visible --}}
                            <div class="absolute bottom-0 left-0 right-0 p-3 md:p-4 transition-all duration-300 group-hover:opacity-0 group-hover:translate-y-1">
                                <h3 class="text-xs md:text-sm font-bold text-white line-clamp-2">{{ $project->name }}</h3>
                            </div>
                            {{-- Glass hover panel --}}
                            <div class="absolute bottom-0 left-0 right-0 translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-out backdrop-blur-md bg-brand-900/50 border-t border-white/20 p-3 md:p-4">
                                <h3 class="text-xs md:text-sm font-bold text-white mb-2 line-clamp-2">{{ $project->name }}</h3>
                                <span class="text-accent-400 font-bold text-xs tracking-wider uppercase flex items-center gap-1">Xem <i class="fas fa-angle-right"></i></span>
                            </div>
                        </a>
                        @endforeach
                    </div>
                    @endif
                </div>
                <div class="text-center mt-10">
                    <a href="{{ route('frontend.projects.index') }}" class="inline-flex items-center px-6 py-3 bg-white/10 hover:bg-white text-white hover:text-brand-900 border border-white/20 rounded font-bold transition-all text-sm uppercase tracking-wide">Xem toàn bộ hệ thống dự án</a>
                </div>
            </div>
            
            @if(isset($homeProjectCategories))
            @foreach($homeProjectCategories as $category)
            <div class="hidden p-0" id="cat-{{ $category->id }}" role="tabpanel" aria-labelledby="cat-{{ $category->id }}-tab">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 min-h-[400px]">
                    @php 
                        $catList = $category->projects->take(5);
                        $catFirstProject = $catList->first(); 
                        $catRestProjects = $catList->slice(1, 4); 
                    @endphp
                    
                    @if($catFirstProject)
                    <a href="{{ route('frontend.project.bySlug', $catFirstProject->slug) }}" class="group block relative border border-brand-700 bg-brand-800 shadow-lg animate__animated animate__fadeIn lg:row-span-2 flex flex-col h-full rounded-sm overflow-hidden aspect-[4/3] lg:aspect-auto">
                        <div class="w-full h-full absolute inset-0">
                            <img src="{{ !empty($catFirstProject->image) ? asset($catFirstProject->image) : 'https://placehold.co/800x600/1e293b/ffffff?text=Project' }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-brand-950 via-brand-950/60 to-transparent"></div>
                        <div class="p-8 absolute bottom-0 left-0 w-full transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                            <h3 class="text-3xl font-bold text-white mb-2">{{ $catFirstProject->name }}</h3>
                            <div class="h-0 opacity-0 group-hover:h-auto group-hover:opacity-100 transition-all duration-300 delay-100 mt-2">
                                <span class="text-accent-400 font-bold text-sm tracking-widest uppercase flex items-center gap-2">Trải nghiệm <i class="fas fa-arrow-right"></i></span>
                            </div>
                        </div>
                    </a>
                    @else
                    <div class="col-span-full py-12 text-center text-brand-300 bg-brand-800/30 rounded-sm border border-brand-800/50">
                        Đang cập nhật dự án cho danh mục này...
                    </div>
                    @endif

                    @if($catRestProjects->count() > 0)
                    <div class="grid grid-cols-2 gap-6 h-full">
                        @foreach($catRestProjects as $project)
                        <a href="{{ route('frontend.project.bySlug', $project->slug) }}" class="group block relative border border-brand-700 bg-brand-800 shadow-lg animate__animated animate__fadeIn rounded-sm overflow-hidden flex flex-col h-full aspect-[4/3]">
                            <div class="w-full h-full absolute inset-0">
                                <img src="{{ !empty($project->image) ? asset($project->image) : 'https://placehold.co/600x450/1e293b/ffffff?text=Project' }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            </div>
                            <div class="absolute inset-0 bg-gradient-to-t from-brand-950 via-brand-950/50 to-transparent"></div>
                            <div class="p-5 absolute bottom-0 left-0 w-full transform translate-y-2 group-hover:translate-y-0 transition-transform duration-300">
                                <h3 class="text-lg font-bold text-white mb-1 line-clamp-2">{{ $project->name }}</h3>
                                <div class="h-0 opacity-0 group-hover:h-auto group-hover:opacity-100 transition-all duration-300 mt-1">
                                    <span class="text-brand-300 font-bold text-xs tracking-wider uppercase flex items-center gap-1">Xem <i class="fas fa-angle-right"></i></span>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
            @endif
        </div>
    </div>
</section>
@endif

{{-- 5.5. PORTALS / QUICK LINKS (Tuyển dụng - Đại lý - Báo giá) --}}
<section class="py-16 bg-gray-50 border-t border-gray-200">
    <div class="container mx-auto px-4 max-w-7xl">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <a href="{{ route('frontend.careers.index') }}" class="group block bg-white border border-gray-200 rounded-sm p-8 hover:border-brand-500 hover:shadow-md transition-all">
                <div class="text-brand-600 text-3xl mb-4 group-hover:text-brand-700">
                    <i class="fas fa-user-tie"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Tuyển Dụng</h3>
                <p class="text-gray-600 mb-6 font-sans text-sm">Gia nhập đội ngũ kỹ sư tài năng của chúng tôi để cùng phát triển các hệ thống đẳng cấp.</p>
                <div class="flex items-center text-brand-600 font-bold uppercase tracking-wider text-xs">Xem vị trí <i class="fas fa-arrow-right ml-2"></i></div>
            </a>
            
            <a href="{{ route('agency.index') }}" class="group block bg-white border border-gray-200 rounded-sm p-8 hover:border-brand-500 hover:shadow-md transition-all">
                <div class="text-brand-600 text-3xl mb-4 group-hover:text-brand-700">
                    <i class="fas fa-handshake"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Hợp Tác Đại Lý</h3>
                <p class="text-gray-600 mb-6 font-sans text-sm">Chính sách chiết khấu, hỗ trợ kỹ thuật và đào tạo bán hàng toàn diện từ A-Z.</p>
                <div class="flex items-center text-brand-600 font-bold uppercase tracking-wider text-xs">Đăng ký ngay <i class="fas fa-arrow-right ml-2"></i></div>
            </a>
            
            <a href="{{ route('consulting.index') }}" class="group block bg-white border border-gray-200 rounded-sm p-8 hover:border-brand-500 hover:shadow-md transition-all">
                <div class="text-brand-600 text-3xl mb-4 group-hover:text-brand-700">
                    <i class="fas fa-file-signature"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Nhận Báo Giá</h3>
                <p class="text-gray-600 mb-6 font-sans text-sm">Cung cấp thông tin, chuyên gia sẽ thiết kế lộ trình chuyển đổi và báo giá chi tiết.</p>
                <div class="flex items-center text-brand-600 font-bold uppercase tracking-wider text-xs">Gửi yêu cầu <i class="fas fa-arrow-right ml-2"></i></div>
            </a>
        </div>
    </div>
</section>

{{-- 6. PRODUCTS / HARDWARE --}}
@if(isset($homeProducts) && $homeProducts->count())
<section class="py-20 bg-gray-50">
    <div class="container mx-auto px-4 max-w-7xl">
        <div class="text-center mb-16">
            <h2 class="section-title center">Sản Phẩm & Thiết Bị</h2>
            <p class="text-gray-600 max-w-2xl mx-auto mt-4">Phân phối thiết bị phần cứng, máy chủ và linh kiện mạng chuyên dụng.</p>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($homeProducts->take(4) as $product)
            <div class="corp-card flex flex-col h-full bg-white group overflow-hidden p-1">
                <div class="relative aspect-square bg-gray-50 flex items-center justify-center p-4 rounded-t-[11px] overflow-hidden">
                    <img src="{{ $product->image ? asset($product->image) : 'https://placehold.co/400x400/f8fafc/0e4a86?text=Product' }}" class="max-w-full max-h-full object-contain mix-blend-multiply group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0 bg-brand-900/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <a href="{{ route('frontend.product.bySlug', $product->slug) }}" class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-brand-900 hover:bg-accent-500 hover:text-white transition-colors">
                            <i class="fas fa-search"></i>
                        </a>
                    </div>
                </div>
                <div class="p-5 flex flex-col flex-grow text-center">
                    <h3 class="font-bold text-gray-900 mb-2 text-sm md:text-base line-clamp-2 group-hover:text-brand-600 transition-colors">{{ $product->name }}</h3>
                    <div class="mt-auto flex flex-col items-center justify-center">
                        @if($product->price)
                            <span class="text-brand-700 font-bold text-lg">{{ number_format($product->price) }}₫</span>
                        @endif
                        @if($product->compare_at_price)
                            <span class="text-gray-400 text-xs line-through mt-1">{{ number_format($product->compare_at_price) }}₫</span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="text-center mt-10">
            <a href="{{ route('products.index') }}" class="inline-flex items-center text-brand-700 font-bold hover:text-brand-900 transition-colors">
                Xem toàn bộ cửa hàng <i class="fas fa-arrow-right ml-2 text-sm"></i>
            </a>
        </div>
    </div>
</section>
@endif

{{-- 7. LATEST NEWS --}}
@if(isset($allPosts) && $allPosts->count())
<section class="py-20 bg-white border-t border-gray-100">
    <div class="container mx-auto px-4 max-w-7xl">
        <div class="flex flex-col md:flex-row justify-between items-end mb-12">
            <div>
                <h2 class="section-title">Tin Tức Mới Nhất</h2>
            </div>
            <a href="{{ route('frontend.posts.index') }}" class="mt-6 md:mt-0 px-6 py-2 border border-gray-200 hover:border-brand-500 text-gray-600 hover:text-brand-700 rounded transition-colors text-sm font-medium">
                Xem tất cả bài viết
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($allPosts->take(3) as $post)
            <div class="corp-card overflow-hidden group">
                <a href="{{ $post->slug_url }}" class="block aspect-[16/10] overflow-hidden relative">
                    <img src="{{ $post->image ? asset($post->image) : 'https://placehold.co/600x400/e2e8f0/0e4a86?text=News' }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute top-4 left-4 bg-white/90 backdrop-blur text-brand-900 px-3 py-1 rounded text-xs font-bold shadow-sm">
                        {{ $post->created_at->format('d/m/Y') }}
                    </div>
                </a>
                <div class="p-6 text-left">
                    <h3 class="text-xl font-bold text-gray-900 mb-3 line-clamp-2 group-hover:text-brand-600 transition-colors">
                        <a href="{{ $post->slug_url }}">{{ $post->title }}</a>
                    </h3>
                    <p class="text-gray-600 text-sm line-clamp-3 mb-4">{{ Str::limit(strip_tags($post->description ?? $post->content), 120) }}</p>
                    <a href="{{ $post->slug_url }}" class="text-brand-700 text-sm font-bold flex items-center group-hover:text-brand-900">
                        Đọc tiếp <i class="fas fa-angle-right ml-2"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- 8. SIMPLE CTA BLOCK --}}
<section class="py-16 bg-gradient-to-r from-brand-900 to-brand-800 text-center">
    <div class="container mx-auto px-4">
        <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-white mb-4">Sẵn Sàng Chuyển Đổi Số Cùng CNET?</h2>
        <p class="text-brand-100 mb-8 max-w-2xl mx-auto">Liên hệ ngay để nhận giải pháp công nghệ được thiết kế riêng cho doanh nghiệp của bạn.</p>
        <a href="{{ route('contact.show') }}" class="inline-flex items-center px-8 py-3 bg-white text-brand-900 font-bold rounded hover:bg-gray-50 transition-colors">
            Nhận Báo Giá Tư Vấn <i class="fas fa-paper-plane ml-2"></i>
        </a>
    </div>
</section>

@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Swiper !== 'undefined') {
        new Swiper('.hero-swiper', { 
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
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.hero-custom-next',
                prevEl: '.hero-custom-prev',
            }
        });
        
        new Swiper('.field-swiper', {
            slidesPerView: 1.2,
            spaceBetween: 16,
            autoplay: { delay: 4000 },
            pagination: { el: '.field-swiper .swiper-pagination', clickable: true },
            breakpoints: {
                480: { slidesPerView: 2, spaceBetween: 20 },
                640: { slidesPerView: 3, spaceBetween: 24 },
                1024: { slidesPerView: 4, spaceBetween: 32 }
            }
        });
    }
});
</script>
@endpush
