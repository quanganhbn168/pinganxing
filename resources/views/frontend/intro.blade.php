@extends('layouts.master')
@section('title', ($intro->page_title ?? 'Về Chúng Tôi') . ' - ' . ($setting->site_name ?? config('app.name')))
@section('meta_description', $intro->page_subtitle ?? '')

@section('content')

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- SECTION 1: HERO BANNER                                    --}}
{{-- ══════════════════════════════════════════════════════════ --}}
<div class="relative w-full h-[45vh] md:h-[55vh] overflow-hidden bg-brand-900">
    @if($bannerMedia)
        <img src="{{ $bannerMedia->url }}" alt="{{ $intro->page_title }}"
             class="w-full h-full object-cover opacity-50">
    @endif
    <div class="absolute inset-0 bg-gradient-to-b from-brand-900/60 via-transparent to-brand-900/80"></div>
    <div class="absolute inset-0 flex flex-col items-center justify-center text-white text-center px-4">
        <h1 class="text-4xl md:text-6xl font-black uppercase tracking-widest mb-4 drop-shadow-lg">
            {{ $intro->page_title ?? 'Về Chúng Tôi' }}
        </h1>
        @if($intro->page_subtitle)
        <p class="text-lg md:text-xl text-blue-100 max-w-2xl leading-relaxed">
            {{ $intro->page_subtitle }}
        </p>
        @endif
        {{-- Breadcrumb --}}
        <nav class="mt-6 text-sm text-blue-200 flex items-center gap-2">
            <a href="/" class="hover:text-white transition-colors">Trang chủ</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span class="text-white font-medium">{{ $intro->page_title ?? 'Về chúng tôi' }}</span>
        </nav>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- SECTION 2: STATS — SỐ LIỆU NỔI BẬT                      --}}
{{-- ══════════════════════════════════════════════════════════ --}}
@php $stats = $intro->stats ?? []; @endphp
@if(count($stats))
<div class="bg-brand-900 py-10">
    <div class="max-w-screen-xl mx-auto px-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($stats as $stat)
            @php
                $statIcon = $stat['icon'] ?? 'star';
            @endphp
            <div class="text-center group">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-white/10 mb-3 group-hover:bg-accent-500 transition-colors duration-300">
                    <i class="fas fa-{{ $statIcon }} text-2xl text-accent-400 group-hover:text-white transition-colors"></i>
                </div>
                <div class="text-3xl md:text-4xl font-black text-white">
                    {{ $stat['value'] ?? '' }}<span class="text-accent-400 text-2xl">{{ $stat['suffix'] ?? '' }}</span>
                </div>
                <div class="text-sm text-blue-200 font-medium mt-1 uppercase tracking-wider">
                    {{ $stat['label'] ?? '' }}
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- SECTION 3: CÂU CHUYỆN CÔNG TY                            --}}
{{-- ══════════════════════════════════════════════════════════ --}}
<section class="py-16 md:py-24 bg-white dark:bg-gray-900">
    <div class="max-w-screen-xl mx-auto px-4">
        <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-20">

            {{-- Ảnh + Video --}}
            <div class="w-full lg:w-1/2 relative flex-shrink-0">
                <div class="relative rounded-2xl overflow-hidden shadow-2xl aspect-video bg-gray-200 dark:bg-gray-700">
                    @if($storyMedia)
                        <img src="{{ $storyMedia->url }}" alt="{{ $intro->story_title }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-brand-800 to-brand-600">
                            <i class="fas fa-building-office text-6xl text-white opacity-30"></i>
                        </div>
                    @endif

                    {{-- Nút Play Video --}}
                    @if($videoEmbed)
                    <button onclick="document.getElementById('videoModal').classList.remove('hidden')"
                            class="absolute inset-0 flex items-center justify-center group">
                        <div class="w-20 h-20 rounded-full bg-white/90 hover:bg-white flex items-center justify-center shadow-2xl transform group-hover:scale-110 transition-all duration-300">
                            <i class="fas fa-play text-brand-600 text-2xl ml-1"></i>
                        </div>
                    </button>
                    @endif
                </div>

                {{-- Badge năm thành lập --}}
                @if($intro->founded_year)
                <div class="absolute -bottom-6 -right-4 bg-accent-500 text-white rounded-2xl p-5 shadow-xl text-center">
                    <div class="text-3xl font-black">{{ date('Y') - (int)$intro->founded_year }}+</div>
                    <div class="text-xs font-bold uppercase tracking-widest">Năm<br>kinh nghiệm</div>
                </div>
                @endif
            </div>

            {{-- Nội dung --}}
            <div class="w-full lg:w-1/2">
                <div class="text-xs font-black uppercase tracking-[0.3em] text-accent-500 mb-3">
                    {{ $intro->story_title ?? 'Câu chuyện của chúng tôi' }}
                </div>
                <h2 class="text-3xl md:text-4xl font-black text-brand-900 dark:text-white leading-tight mb-6">
                    {{ $setting->site_name ?? config('app.name') }}
                    @if($intro->founded_year)
                        <span class="block text-xl text-gray-400 font-normal mt-1">Thành lập năm {{ $intro->founded_year }}</span>
                    @endif
                </h2>

                @if($intro->story_description)
                <div class="prose prose-lg dark:prose-invert max-w-none text-gray-600 dark:text-gray-300 leading-relaxed">
                    {!! $intro->story_description !!}
                </div>
                @endif

                {{-- Mission & Vision --}}
                @if($intro->mission_description || $intro->vision_description)
                <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @if($intro->mission_description)
                    <div class="bg-brand-50 dark:bg-brand-900/30 border-l-4 border-brand-600 rounded-r-xl p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <i class="fas fa-rocket-launch text-brand-600 text-sm"></i>
                            <h4 class="font-bold text-brand-800 dark:text-brand-300 text-sm uppercase tracking-wider">
                                {{ $intro->mission_title ?? 'Sứ mệnh' }}
                            </h4>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">{{ $intro->mission_description }}</p>
                    </div>
                    @endif
                    @if($intro->vision_description)
                    <div class="bg-accent-50 dark:bg-accent-900/20 border-l-4 border-accent-500 rounded-r-xl p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <i class="fas fa-eye text-accent-500 text-sm"></i>
                            <h4 class="font-bold text-accent-700 dark:text-accent-300 text-sm uppercase tracking-wider">
                                {{ $intro->vision_title ?? 'Tầm nhìn' }}
                            </h4>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">{{ $intro->vision_description }}</p>
                    </div>
                    @endif
                </div>
                @endif
            </div>

        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- SECTION 4: GIÁ TRỊ CỐT LÕI                              --}}
{{-- ══════════════════════════════════════════════════════════ --}}
@php $coreValues = $intro->core_values ?? []; @endphp
@if(count($coreValues))
<section class="py-16 bg-gray-50 dark:bg-gray-800">
    <div class="max-w-screen-xl mx-auto px-4">
        <div class="text-center mb-12">
            <div class="text-xs font-black uppercase tracking-[0.3em] text-accent-500 mb-2">DNA của chúng tôi</div>
            <h2 class="text-3xl md:text-4xl font-black text-brand-900 dark:text-white">Giá trị cốt lõi</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($coreValues as $i => $value)
            @php
                $colors = ['brand', 'accent', 'emerald', 'violet', 'rose', 'amber', 'cyan', 'orange'];
                $c = $colors[$i % count($colors)];
            @endphp
            <div class="group bg-white dark:bg-gray-900 rounded-2xl p-7 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-gray-100 dark:border-gray-700 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-brand-900 group-hover:bg-accent-500 transition-colors duration-300 mb-5 mx-auto">
                    <i class="fas fa-{{ $value['icon'] ?? 'star' }} text-2xl text-white"></i>
                </div>
                <h3 class="text-lg font-black text-gray-900 dark:text-white mb-2">{{ $value['title'] ?? '' }}</h3>
                @if(!empty($value['description']))
                <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">{{ $value['description'] }}</p>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- SECTION 4.5: LỊCH SỬ PHÁT TRIỂN (TIMELINE)              --}}
{{-- ══════════════════════════════════════════════════════════ --}}
@php $timeline = $intro->timeline ?? []; @endphp
@if(count($timeline))
<section class="py-16 md:py-24 bg-white dark:bg-gray-900 overflow-hidden">
    <div class="max-w-screen-xl mx-auto px-4">
        <div class="text-center mb-16">
            <div class="text-xs font-black uppercase tracking-[0.3em] text-accent-500 mb-2">Hành trình của chúng tôi</div>
            <h2 class="text-3xl md:text-4xl font-black text-brand-900 dark:text-white">Lịch sử phát triển</h2>
        </div>

        <div class="relative">
            {{-- Trục giữa --}}
            <div class="absolute left-4 md:left-1/2 top-0 bottom-0 w-0.5 bg-gray-100 dark:bg-gray-800 -translate-x-1/2 hidden md:block"></div>

            <div class="space-y-12 md:space-y-0">
                @foreach($timeline as $index => $item)
                @php
                    $isEven = $index % 2 === 0;
                    $itemMedia = !empty($item['image_id']) ? \Awcodes\Curator\Models\Media::find($item['image_id']) : null;
                @endphp
                <div class="relative flex flex-col md:flex-row items-center justify-center md:mb-20 last:mb-0">
                    {{-- Marker --}}
                    <div class="absolute left-4 md:left-1/2 w-4 h-4 rounded-full bg-accent-500 border-4 border-white dark:border-gray-900 -translate-x-1/2 z-10 hidden md:block"></div>

                    {{-- Nội dung --}}
                    <div class="w-full md:w-1/2 flex {{ $isEven ? 'md:justify-end md:pr-16' : 'md:justify-start md:pl-16 md:order-2' }}">
                        <div class="bg-gray-50 dark:bg-gray-800/50 p-6 md:p-8 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow w-full max-w-xl">
                            <div class="flex items-center gap-4 mb-4">
                                <span class="text-2xl md:text-3xl font-black text-accent-500 leading-none">{{ $item['year'] ?? '' }}</span>
                                <div class="h-px flex-1 bg-gray-200 dark:bg-gray-700"></div>
                            </div>
                            <h3 class="text-xl font-bold text-brand-900 dark:text-white mb-3">{{ $item['title'] ?? '' }}</h3>
                            @if(!empty($item['description']))
                            <div class="prose prose-sm dark:prose-invert text-gray-600 dark:text-gray-400">
                                {!! $item['description'] !!}
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Ảnh minh họa (nếu có) --}}
                    <div class="w-full md:w-1/2 mt-6 md:mt-0 flex {{ $isEven ? 'md:justify-start md:pl-16' : 'md:justify-end md:pr-16 md:order-1' }}">
                        @if($itemMedia)
                        <div class="relative w-full max-w-md rounded-2xl overflow-hidden shadow-lg aspect-video">
                            <img src="{{ $itemMedia->url }}" alt="{{ $item['title'] }}" class="w-full h-full object-cover">
                        </div>
                        @else
                        <div class="hidden md:block w-full max-w-md h-1"></div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- SECTION 5: ĐỘI NGŨ                                       --}}
{{-- ══════════════════════════════════════════════════════════ --}}
@if($teams->isNotEmpty())
<section class="py-16 md:py-20 bg-white dark:bg-gray-900">
    <div class="max-w-screen-xl mx-auto px-4">
        <div class="text-center mb-12">
            <div class="text-xs font-black uppercase tracking-[0.3em] text-accent-500 mb-2">Con người tạo nên giá trị</div>
            <h2 class="text-3xl md:text-4xl font-black text-brand-900 dark:text-white">Đội ngũ của chúng tôi</h2>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($teams as $member)
            <div class="group text-center">
                <div class="relative w-32 h-32 md:w-40 md:h-40 mx-auto mb-4 rounded-full overflow-hidden border-4 border-gray-100 dark:border-gray-700 group-hover:border-brand-500 transition-colors duration-300 shadow-md">
                    @if($member->image)
                        <img src="{{ $member->image->url }}" alt="{{ $member->name }}"
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-brand-700 to-brand-900 flex items-center justify-center">
                            <span class="text-white text-3xl font-black">{{ strtoupper(substr($member->name, 0, 1)) }}</span>
                        </div>
                    @endif
                </div>
                <h3 class="font-bold text-gray-900 dark:text-white text-sm">{{ $member->name }}</h3>
                @if($member->position)
                <p class="text-xs text-accent-500 font-semibold uppercase tracking-wider mt-0.5">{{ $member->position }}</p>
                @endif
                @if($member->bio)
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 line-clamp-2 px-2">{{ $member->bio }}</p>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- SECTION 6: ĐỐI TÁC                                       --}}
{{-- ══════════════════════════════════════════════════════════ --}}
@if($partners->isNotEmpty())
<section class="py-14 bg-gray-50 dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700">
    <div class="max-w-screen-xl mx-auto px-4">
        <div class="text-center mb-10">
            <div class="text-xs font-black uppercase tracking-[0.3em] text-accent-500 mb-2">Niềm tin từ</div>
            <h2 class="text-3xl font-black text-brand-900 dark:text-white">Đối tác & Khách hàng</h2>
        </div>
        <div class="flex flex-wrap items-center justify-center gap-6 md:gap-10">
            @foreach($partners as $partner)
            <div class="group">
                @if($partner->url)
                <a href="{{ $partner->url }}" target="_blank" rel="noopener">
                @endif
                    <img src="{{ $partner->image ? $partner->image?->url : '' }}"
                         alt="{{ $partner->name }}"
                         class="h-10 md:h-14 object-contain grayscale opacity-60 group-hover:grayscale-0 group-hover:opacity-100 transition-all duration-300">
                @if($partner->url)
                </a>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- SECTION 6.5: NỘI DUNG TÙY CHỈNH (BUILDER)                --}}
{{-- ══════════════════════════════════════════════════════════ --}}
@php $customBlocks = $intro->custom_blocks ?? []; @endphp
@if(count($customBlocks))
<section class="py-16 bg-white dark:bg-gray-900">
    <div class="max-w-screen-xl mx-auto px-4">
        <div class="space-y-20">
            @foreach($customBlocks as $block)
                @php
                    $type = $block['type'] ?? 'text_block';
                    $data = $block;
                @endphp

                @if($type === 'text_block')
                    <div class="prose prose-lg dark:prose-invert max-w-4xl mx-auto">
                        {!! $data['content'] ?? '' !!}
                    </div>

                @elseif($type === 'image_text_block')
                    @php
                        $blockMedia = !empty($data['image_id']) ? \Awcodes\Curator\Models\Media::find($data['image_id']) : null;
                        $isRight = ($data['image_position'] ?? 'left') === 'right';
                    @endphp
                    <div class="flex flex-col {{ $isRight ? 'lg:flex-row-reverse' : 'lg:flex-row' }} items-center gap-12 lg:gap-20">
                        <div class="w-full lg:w-1/2">
                            @if($blockMedia)
                                <div class="rounded-2xl overflow-hidden shadow-xl">
                                    <img src="{{ $blockMedia->url }}" alt="{{ $data['title'] ?? '' }}" class="w-full h-auto">
                                </div>
                            @endif
                        </div>
                        <div class="w-full lg:w-1/2">
                            @if(!empty($data['title']))
                                <h2 class="text-3xl font-black text-brand-900 dark:text-white mb-6 uppercase tracking-tight">{{ $data['title'] }}</h2>
                            @endif
                            <div class="prose prose-lg dark:prose-invert text-gray-600 dark:text-gray-300">
                                {!! $data['content'] ?? '' !!}
                            </div>
                        </div>
                    </div>

                @elseif($type === 'video_block')
                    <div class="max-w-4xl mx-auto">
                        @if(!empty($data['title']))
                            <h2 class="text-3xl font-black text-brand-900 dark:text-white text-center mb-6 uppercase tracking-tight">{{ $data['title'] }}</h2>
                        @endif
                        @if(!empty($data['description']))
                            <p class="text-gray-600 dark:text-gray-400 text-center mb-8 text-lg leading-relaxed">{{ $data['description'] }}</p>
                        @endif
                        @php $blockVideoEmbed = (new \App\Http\Controllers\Frontend\IntroController)->toEmbedUrl($data['video_url'] ?? null); @endphp
                        @if($blockVideoEmbed)
                            <div class="relative aspect-video rounded-2xl overflow-hidden shadow-2xl bg-black ring-8 ring-gray-100 dark:ring-gray-800">
                                <iframe src="{{ $blockVideoEmbed }}" class="absolute inset-0 w-full h-full" frameborder="0" allowfullscreen></iframe>
                            </div>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- SECTION 7: CTA — KÊU GỌI HÀNH ĐỘNG                      --}}
{{-- ══════════════════════════════════════════════════════════ --}}
<section class="relative py-20 bg-brand-900 overflow-hidden">
    {{-- Background decoration --}}
    <div class="absolute top-0 left-0 w-72 h-72 bg-accent-500/10 rounded-full -translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-brand-700/30 rounded-full translate-x-1/3 translate-y-1/3"></div>

    <div class="relative z-10 max-w-3xl mx-auto px-4 text-center">
        <div class="text-xs font-black uppercase tracking-[0.3em] text-accent-400 mb-3">Bắt đầu ngay hôm nay</div>
        <h2 class="text-3xl md:text-5xl font-black text-white leading-tight mb-4">
            {{ $intro->cta_title ?? 'Sẵn sàng chuyển đổi số cùng chúng tôi?' }}
        </h2>
        <p class="text-blue-200 text-lg mb-10 leading-relaxed">
            {{ $intro->cta_subtitle ?? 'Liên hệ ngay để được tư vấn miễn phí và nhận báo giá phù hợp nhất' }}
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="{{ route('contact.show') }}"
               class="inline-flex items-center gap-2 bg-accent-500 hover:bg-accent-600 text-white font-bold uppercase tracking-wider px-8 py-4 rounded-sm transition-all duration-300 shadow-lg hover:shadow-accent-500/30 hover:-translate-y-0.5">
                <i class="fas fa-paper-plane"></i>
                {{ $intro->cta_button_label ?? 'Liên hệ tư vấn' }}
            </a>
            @if($setting->phone)
            <a href="tel:{{ $setting->phone }}"
               class="inline-flex items-center gap-2 border-2 border-white/30 hover:border-white text-white font-bold uppercase tracking-wider px-8 py-4 rounded-sm transition-all duration-300">
                <i class="fas fa-phone"></i>
                {{ $setting->phone_display ?? $setting->phone }}
            </a>
            @endif
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- MODAL VIDEO                                               --}}
{{-- ══════════════════════════════════════════════════════════ --}}
@if($videoEmbed)
<div id="videoModal"
     class="hidden fixed inset-0 z-[9999] bg-black/90 flex items-center justify-center p-4"
     onclick="if(event.target===this) this.classList.add('hidden')">
    <div class="relative w-full max-w-5xl aspect-video">
        <button onclick="document.getElementById('videoModal').classList.add('hidden')"
                class="absolute -top-12 right-0 text-white hover:text-gray-300 transition-colors">
            <i class="fas fa-times text-2xl"></i>
        </button>
        <iframe
            src="{{ $videoEmbed }}"
            class="w-full h-full rounded-xl"
            frameborder="0"
            allow="autoplay; fullscreen; picture-in-picture"
            allowfullscreen>
        </iframe>
    </div>
</div>
@endif

@endsection
