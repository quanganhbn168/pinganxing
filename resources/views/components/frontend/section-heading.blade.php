{{-- resources/views/components/frontend/section-heading.blade.php --}}
@props([
    'title' => '',
    'subtitle' => '',
    'center' => false,
])

<div class="mb-10 {{ $center ? 'text-center' : '' }}">
    <h2 class="text-2xl md:text-3xl font-black text-gray-900 uppercase tracking-tight relative inline-block pb-4">
        {{ $title }}
        <div class="absolute bottom-0 {{ $center ? 'left-1/2 -translate-x-1/2' : 'left-0' }} w-16 h-1 bg-accent-500 rounded-sm"></div>
    </h2>

    @if($subtitle)
        <p class="mt-4 text-gray-500 font-sans text-sm max-w-2xl {{ $center ? 'mx-auto' : '' }}">
            {{ $subtitle }}
        </p>
    @endif
</div>
