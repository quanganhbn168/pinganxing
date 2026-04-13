{{-- resources/views/frontend/services/service-item.blade.php --}}
<article class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden group flex flex-col md:flex-row h-full md:h-56">
    {{-- Image --}}
    <a href="{{ $service->slug_url }}" class="block relative md:w-2/5 aspect-[4/3] md:aspect-auto overflow-hidden">
        <img src="{{ asset($service->image ?? 'images/setting/no-image.png') }}" 
             alt="{{ $service->name }}" 
             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
        <div class="absolute inset-0 bg-gray-900/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
    </a>
    
    {{-- Content --}}
    <div class="p-6 md:p-8 flex-1 flex flex-col justify-center border-l-0 md:border-l border-gray-100 dark:border-gray-700">
        <p class="text-xs text-blue-600 dark:text-blue-400 font-bold uppercase tracking-wider mb-2">
            <i class="far fa-clock mr-1"></i> {{ $service->created_at->format('d/m/Y') }}
        </p>
        <h3 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white mb-3 line-clamp-2 group-hover:text-blue-600 transition-colors leading-tight">
            <a href="{{ $service->slug_url }}">
                {{ $service->name }}
            </a>
        </h3>
        <p class="text-gray-600 dark:text-gray-300 line-clamp-2 md:line-clamp-3 mb-4 flex-1">
            {{ Str::limit(strip_tags($service->description), 150) }}
        </p>
        <a href="{{ $service->slug_url }}" class="inline-flex items-center text-sm font-bold text-blue-600 dark:text-blue-400 group-hover:underline w-max">
            Xem chi tiết <i class="fas fa-arrow-right ml-1.5 text-xs group-hover:translate-x-1 transition-transform"></i>
        </a>
    </div>
</article>
