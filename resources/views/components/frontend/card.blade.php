{{-- resources/views/components/frontend/card.blade.php --}}
{{--
    Reusable card component cho listing pages.
    
    Usage:
    <x-frontend.card :href="$url" :image="$imageUrl" :title="$name" :description="$desc" :date="$date" :badge="$badge" />
--}}
@props([
    'href'        => '#',
    'image'       => null,
    'title'       => '',
    'description' => '',
    'date'        => null,
    'badge'       => null,
    'badgeColor'  => 'blue',
])

<a href="{{ $href }}" class="group block bg-white dark:bg-gray-800 rounded-sm overflow-hidden shadow-sm hover:shadow-md border border-gray-100 hover:border-brand-500 dark:border-gray-700 transition-all duration-300">
    {{-- Image --}}
    @if($image)
        <div class="relative overflow-hidden aspect-[16/10]">
            <img src="{{ $image }}" alt="{{ $title }}" loading="lazy"
                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
            
            @if($badge)
                <span class="absolute top-3 left-3 px-3 py-1 text-[10px] font-bold tracking-wider uppercase rounded-sm bg-brand-600 text-white">
                    {{ $badge }}
                </span>
            @endif
        </div>
    @endif

    {{-- Content --}}
    <div class="p-5">
        @if($date)
            <time class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                <i class="far fa-calendar-alt mr-1"></i> {{ $date }}
            </time>
        @endif
        
        <h3 class="mt-2 text-lg font-bold text-gray-900 dark:text-white group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors line-clamp-2">
            {{ $title }}
        </h3>

        @if($description)
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 line-clamp-3">
                {{ $description }}
            </p>
        @endif

        {{ $slot }}
    </div>
</a>
