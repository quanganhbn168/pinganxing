<div class="bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-lg dark:bg-gray-800 dark:border-gray-700 transition-shadow overflow-hidden group">
    <a href="{{ $project->slug_url }}" class="block relative aspect-w-16 aspect-h-10 overflow-hidden">
        <img class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" src="{{ !empty($project->image) ? asset($project->image) : (optional($project->mainImage())->url() ?: asset('images/setting/no-image.png')) }}" alt="{{ $project->name }}" />
    </a>
    <div class="p-5">
        <a href="{{ $project->slug_url }}">
            <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white line-clamp-2 hover:text-blue-600 transition-colors">{{ $project->name }}</h5>
        </a>
        <p class="mb-3 font-normal text-gray-500 dark:text-gray-400 line-clamp-3">{{ strip_tags($project->description ?? '') }}</p>
        <a href="{{ $project->slug_url }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium dark:text-blue-400 dark:hover:text-blue-300 group-hover:underline">
            Xem chi tiết
            <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
            </svg>
        </a>
    </div>
</div>
