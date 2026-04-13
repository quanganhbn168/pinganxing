{{-- resources/views/components/frontend/breadcrumb.blade.php --}}
@props(['items' => []])

<nav aria-label="Breadcrumb">
    <ol class="flex items-center gap-2 text-sm flex-wrap justify-center md:justify-start">
        <li>
            <a href="{{ route('home') }}" class="text-white/70 hover:text-white transition-colors">
                <i class="fas fa-home"></i>
                <span class="ml-1">Trang chủ</span>
            </a>
        </li>

        @foreach ($items as $item)
            <li class="flex items-center gap-2">
                <i class="fas fa-chevron-right text-white/40 text-xs"></i>
                @if ($loop->last)
                    <span class="text-white font-medium">{{ $item['label'] }}</span>
                @else
                    <a href="{{ $item['url'] }}" class="text-white/70 hover:text-white transition-colors">
                        {{ $item['label'] }}
                    </a>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
