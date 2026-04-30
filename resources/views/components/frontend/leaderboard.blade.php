@props([
    'title' => '',
    'subline' => null,
    'subtitle' => '',
    'description' => null,
    'breadcrumb' => [],
    'image' => null,
    'imageAlt' => null,
    'actions' => [],
    'stats' => [],
])

@php
    $leaderboardImage = $image ? asset($image) : null;
    $leaderboardDescription = filled($description) ? $description : $subtitle;
    $visibleActions = collect($actions ?? [])
        ->filter(fn ($action) => filled($action['label'] ?? null))
        ->take(2)
        ->values();
    $visibleStats = collect($stats ?? [])
        ->filter(fn ($stat) => filled($stat['value'] ?? null) && filled($stat['label'] ?? null))
        ->take(4)
        ->values();
@endphp

<section class="relative isolate overflow-hidden border-b border-blue-100 bg-[#eef6ff]">
    @if($leaderboardImage)
        <img
            src="{{ $leaderboardImage }}"
            alt="{{ $imageAlt ?: $title }}"
            class="absolute inset-0 h-full w-full object-cover object-[72%_center] opacity-90 md:object-center md:opacity-100"
            loading="eager"
            decoding="async"
        >
    @endif

    <div class="absolute inset-0 bg-gradient-to-r from-white/96 via-white/76 to-white/20 md:hidden"></div>
    <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-white/85 to-transparent md:hidden"></div>
    <div class="absolute inset-0 hidden bg-gradient-to-r from-white via-white/88 to-white/8 md:block"></div>
    <div class="absolute inset-y-0 left-0 hidden w-2/3 bg-gradient-to-r from-white via-white/95 to-transparent md:block"></div>
    <div class="absolute inset-x-0 bottom-0 h-12 bg-white/75 md:h-24 md:bg-white/80" style="clip-path: polygon(0 58%, 14% 50%, 29% 66%, 47% 48%, 66% 64%, 82% 52%, 100% 62%, 100% 100%, 0 100%);"></div>

    <div class="relative z-10 mx-auto flex min-h-[360px] max-w-screen-xl flex-col justify-center px-4 py-7 sm:min-h-[400px] sm:py-10 md:min-h-[480px] md:py-14 lg:min-h-[520px] lg:py-16">
        <div class="max-w-3xl text-left">
            @if(count($breadcrumb) > 0)
                <div class="mb-4 flex justify-start md:mb-5">
                    <x-frontend.breadcrumb :items="$breadcrumb" tone="dark" />
                </div>
            @endif

            @if($subline)
                <div class="mb-4 inline-flex max-w-full items-center gap-2 rounded-full border border-blue-200 bg-white/82 px-3 py-2 text-left text-[11px] font-black uppercase leading-snug text-brand-800 shadow-sm backdrop-blur md:mb-6 md:px-4 md:text-xs">
                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-blue-50 text-blue-600">
                        <i class="fas fa-layer-group text-[11px]"></i>
                    </span>
                    <span class="min-w-0">{{ $subline }}</span>
                </div>
            @endif

            <h1 class="break-words text-3xl font-black uppercase leading-[1.08] text-brand-900 sm:text-4xl sm:leading-[1.05] md:text-5xl md:leading-[1.03] lg:text-6xl lg:leading-[1.02]">{!! nl2br(e(trim($title))) !!}</h1>

            @if($leaderboardDescription)
                <p class="mt-3 max-w-2xl text-sm font-semibold leading-6 text-slate-700 sm:text-base md:mt-4 lg:text-lg lg:leading-relaxed">
                    {{ $leaderboardDescription }}
                </p>
            @endif

            @if($visibleActions->isNotEmpty())
                <div class="mt-6 flex flex-col items-stretch gap-3 sm:flex-row sm:items-center md:mt-8 md:justify-start">
                    @foreach($visibleActions as $action)
                        @php
                            $isSecondary = ($action['style'] ?? 'primary') === 'secondary';
                        @endphp
                        <a
                            href="{{ filled($action['url'] ?? null) ? $action['url'] : '#' }}"
                            class="inline-flex min-h-11 w-full items-center justify-center gap-3 rounded-md px-5 py-3 text-center text-sm font-bold transition-all sm:w-auto md:min-h-12 md:px-6 {{ $isSecondary ? 'border border-blue-200 bg-white/85 text-brand-900 hover:border-blue-400 hover:bg-blue-50' : 'bg-blue-700 text-white shadow-lg shadow-blue-700/25 hover:bg-blue-800' }}"
                        >
                            <span>{{ $action['label'] }}</span>
                            @if(filled($action['icon'] ?? null))
                                <i class="{{ $action['icon'] }} text-xs"></i>
                            @endif
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    @if($visibleStats->isNotEmpty())
        <div class="relative z-10 mx-auto max-w-screen-xl px-4 pb-5 sm:pb-6 md:pb-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($visibleStats as $stat)
                    @php
                        $isLast = $loop->last;
                        $isFirstTabletColumn = $loop->index % 2 === 0 && ! $isLast;
                        $hasTabletRowBelow = $visibleStats->count() > 2 && $loop->index < 2;
                    @endphp
                    <div @class([
                        'flex min-h-[74px] items-center gap-3 border-blue-200/80 py-4 text-left sm:px-4 lg:px-6',
                        'border-b' => ! $isLast,
                        'sm:border-b' => $hasTabletRowBelow,
                        'sm:border-b-0' => ! $hasTabletRowBelow,
                        'sm:border-r' => $isFirstTabletColumn,
                        'lg:border-b-0',
                        'lg:border-r' => ! $isLast,
                        'lg:last:border-r-0',
                    ])>
                        <span class="w-10 shrink-0 text-center text-2xl text-blue-700 sm:text-3xl md:text-4xl">
                            <i class="{{ $stat['icon'] ?? 'fas fa-chart-line' }}"></i>
                        </span>
                        <span class="min-w-0 text-left">
                            <span class="block break-words text-lg font-black leading-none text-brand-900 sm:text-xl md:text-2xl">{{ $stat['value'] }}</span>
                            <span class="mt-1 block break-words text-[11px] font-semibold uppercase leading-snug text-slate-500 sm:text-xs">{{ $stat['label'] }}</span>
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</section>
