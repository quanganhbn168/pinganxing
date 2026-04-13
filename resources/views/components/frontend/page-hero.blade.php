{{-- resources/views/components/frontend/page-hero.blade.php --}}
@props([
    'title' => '',
    'subtitle' => '',
    'breadcrumb' => [],
    'image' => null,
])

<section class="relative w-full h-[25vh] md:h-[35vh] overflow-hidden bg-brand-900 border-b border-brand-800">
    {{-- Background Image --}}
    @if($image)
        <img src="{{ asset($image) }}" alt="{{ $title }}" class="w-full h-full object-cover mix-blend-overlay opacity-60">
    @else
        <div class="w-full h-full bg-brand-900"></div>
    @endif

    {{-- Content --}}
    <div class="absolute inset-0 flex flex-col items-center justify-center z-10">
        <div class="text-center px-4">
            <h1 class="text-3xl md:text-5xl font-black text-white uppercase tracking-wider mb-4">
                {{ $title }}
            </h1>

            @if($subtitle)
                <p class="text-base md:text-lg text-white/80 max-w-2xl mx-auto mb-4 font-sans">
                    {{ $subtitle }}
                </p>
            @endif

            @if(count($breadcrumb) > 0)
                <div class="flex justify-center">
                    <x-frontend.breadcrumb :items="$breadcrumb" />
                </div>
            @endif
        </div>
    </div>
</section>
