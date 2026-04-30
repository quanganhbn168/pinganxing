{{-- resources/views/components/frontend/breadcrumb.blade.php --}}
@props(['items' => [], 'tone' => 'light'])

@php
    $isDarkTone = $tone === 'dark';
    $linkClass = $isDarkTone
        ? 'text-slate-500 hover:text-brand-800 transition-colors'
        : 'text-white/70 hover:text-white transition-colors';
    $currentClass = $isDarkTone
        ? 'text-brand-900 font-semibold'
        : 'text-white font-medium';
    $separatorClass = $isDarkTone
        ? 'fas fa-chevron-right text-blue-300 text-xs'
        : 'fas fa-chevron-right text-white/40 text-xs';
@endphp

<nav aria-label="Breadcrumb">
    <ol class="flex items-center gap-2 text-sm flex-wrap justify-center md:justify-start">
        <li>
            <a href="{{ route('home') }}" class="{{ $linkClass }}">
                <i class="fas fa-home"></i>
                <span class="ml-1">Trang chủ</span>
            </a>
        </li>

        @foreach ($items as $item)
            <li class="flex items-center gap-2">
                <i class="{{ $separatorClass }}"></i>
                @if ($loop->last)
                    <span class="{{ $currentClass }}">{{ $item['label'] }}</span>
                @else
                    <a href="{{ $item['url'] }}" class="{{ $linkClass }}">
                        {{ $item['label'] }}
                    </a>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
