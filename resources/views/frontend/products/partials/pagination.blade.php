@if ($paginator->hasPages())
    <nav class="flex flex-col items-center gap-4 sm:flex-row sm:justify-between" role="navigation" aria-label="Pagination Navigation">
        <div class="text-sm font-semibold text-gray-500 dark:text-gray-400">
            Trang {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
        </div>

        <div class="flex flex-wrap items-center justify-center gap-2">
            @php
                $buttonClass = 'inline-flex h-10 min-w-10 items-center justify-center rounded-lg border px-3 text-sm font-bold transition-colors';
                $activeClass = 'border-blue-600 bg-blue-600 text-white shadow-sm shadow-blue-600/20';
                $normalClass = 'border-gray-200 bg-white text-gray-700 hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:border-blue-800 dark:hover:bg-blue-950 dark:hover:text-blue-300';
                $disabledClass = 'pointer-events-none border-gray-100 bg-gray-50 text-gray-300 dark:border-gray-800 dark:bg-gray-800 dark:text-gray-600';
            @endphp

            @if ($paginator->onFirstPage())
                <span class="{{ $buttonClass }} {{ $disabledClass }}" aria-disabled="true">Đầu</span>
                <span class="{{ $buttonClass }} {{ $disabledClass }}" aria-disabled="true" aria-label="Trang trước">
                    <i class="fas fa-chevron-left text-xs"></i>
                </span>
            @else
                <a class="{{ $buttonClass }} {{ $normalClass }}" href="{{ $paginator->url(1) }}">Đầu</a>
                <a class="{{ $buttonClass }} {{ $normalClass }}" href="{{ $paginator->previousPageUrl() }}" aria-label="Trang trước">
                    <i class="fas fa-chevron-left text-xs"></i>
                </a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="{{ $buttonClass }} {{ $disabledClass }}">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="{{ $buttonClass }} {{ $activeClass }}" aria-current="page">{{ $page }}</span>
                        @else
                            <a class="{{ $buttonClass }} {{ $normalClass }}" href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a class="{{ $buttonClass }} {{ $normalClass }}" href="{{ $paginator->nextPageUrl() }}" aria-label="Trang sau">
                    <i class="fas fa-chevron-right text-xs"></i>
                </a>
                <a class="{{ $buttonClass }} {{ $normalClass }}" href="{{ $paginator->url($paginator->lastPage()) }}">Cuối</a>
            @else
                <span class="{{ $buttonClass }} {{ $disabledClass }}" aria-disabled="true" aria-label="Trang sau">
                    <i class="fas fa-chevron-right text-xs"></i>
                </span>
                <span class="{{ $buttonClass }} {{ $disabledClass }}" aria-disabled="true">Cuối</span>
            @endif
        </div>
    </nav>
@endif
