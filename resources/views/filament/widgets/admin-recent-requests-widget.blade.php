<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">Việc cần xử lý gần đây</x-slot>
        <x-slot name="description">Tổng hợp nhanh liên hệ, tư vấn, đại lý và đơn hàng mới nhất.</x-slot>

        @if($items->isEmpty())
            <div class="rounded-lg border border-dashed border-gray-200 p-6 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
                Chưa có dữ liệu mới.
            </div>
        @else
            <div class="divide-y divide-gray-100 dark:divide-gray-800">
                @foreach($items as $item)
                    <a
                        href="{{ $item['url'] ?? '#' }}"
                        @class([
                            'flex items-start justify-between gap-4 py-3 transition-colors',
                            'hover:text-primary-600' => filled($item['url']),
                            'pointer-events-none' => blank($item['url']),
                        ])
                    >
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-md bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                                    {{ $item['type'] }}
                                </span>
                                <strong class="truncate text-sm text-gray-950 dark:text-white">{{ $item['title'] }}</strong>
                            </div>
                            <p class="mt-1 truncate text-sm text-gray-500 dark:text-gray-400">{{ $item['description'] ?: 'Chưa có thông tin liên hệ' }}</p>
                            <p class="mt-1 text-xs text-gray-400">{{ $item['time']?->diffForHumans() }}</p>
                        </div>

                        <x-filament::badge :color="$item['badge_color']">
                            {{ $item['badge'] }}
                        </x-filament::badge>
                    </a>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
