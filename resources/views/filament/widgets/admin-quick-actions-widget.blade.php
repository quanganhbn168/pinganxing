<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">Lối tắt quản trị</x-slot>
        <x-slot name="description">Các khu vực thường dùng khi vận hành website.</x-slot>

        <div class="grid gap-3 sm:grid-cols-2">
            @foreach($actions as $action)
                <a href="{{ $action['url'] }}" class="group rounded-lg border border-gray-200 p-4 transition-colors hover:border-primary-300 hover:bg-primary-50 dark:border-gray-700 dark:hover:border-primary-700 dark:hover:bg-primary-950/30">
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-primary-600 transition-colors group-hover:bg-primary-600 group-hover:text-white dark:bg-gray-800">
                            <x-filament::icon :icon="$action['icon']" class="h-5 w-5" />
                        </div>
                        <div class="min-w-0">
                            <strong class="block text-sm text-gray-950 dark:text-white">{{ $action['label'] }}</strong>
                            <span class="mt-1 block text-xs leading-5 text-gray-500 dark:text-gray-400">{{ $action['description'] }}</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
