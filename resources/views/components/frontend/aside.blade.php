{{-- resources/views/components/frontend/aside.blade.php --}}

<aside class="space-y-8 font-sans">

    {{-- Danh mục sản phẩm --}}
    <div class="bg-white dark:bg-gray-800 rounded-sm shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="bg-brand-900 border-l-4 border-brand-600 px-5 py-4">
            <h3 class="text-sm font-black tracking-wider uppercase text-white flex items-center gap-2">
                Danh mục sản phẩm
            </h3>
        </div>
        <ul class="divide-y divide-gray-100 dark:divide-gray-700">
            @foreach($productCategories as $category)
                <li>
                    <a href="{{ $category->slug_url }}"
                       class="flex items-center justify-between px-5 py-3 text-sm font-bold uppercase tracking-tight text-gray-700 dark:text-gray-300 hover:bg-brand-50 dark:hover:bg-gray-700 hover:text-brand-600 dark:hover:text-brand-400 transition-colors {{ isset($currentCategory) && $currentCategory->id == $category->id ? 'bg-brand-50 dark:bg-gray-700 text-brand-600 dark:text-brand-400 border-l-4 border-brand-600' : '' }}">
                        <span>{{ $category->name }}</span>
                        @if($category->children->isNotEmpty())
                            <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                        @endif
                    </a>
                    @if($category->children->isNotEmpty())
                        <ul class="bg-gray-50 dark:bg-gray-750">
                            @foreach($category->children as $child)
                                <li>
                                    <a href="{{ $child->slug_url }}"
                                       class="flex items-center px-5 py-2.5 pl-6 text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400 hover:bg-brand-50 dark:hover:bg-gray-700 hover:text-brand-600 dark:hover:text-brand-400 transition-colors {{ isset($currentCategory) && $currentCategory->id == $child->id ? 'text-brand-600 dark:text-brand-400 font-medium' : '' }}">
                                        {{ $child->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>

    {{-- Bài viết mới nhất --}}
    @if($latestPosts->isNotEmpty())
    <div class="bg-white dark:bg-gray-800 rounded-sm shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="bg-brand-900 border-l-4 border-brand-600 px-5 py-4">
            <h3 class="text-sm font-black tracking-wider uppercase text-white flex items-center gap-2">
                Tin tức mới
            </h3>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @foreach($latestPosts as $post)
                <a href="{{ $post->slug_url }}" class="flex items-start gap-4 p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors group">
                    @if($post->image)
                        <img src="{{ $post->image->path }}" alt="{{ $post->title }}" loading="lazy"
                             class="w-16 h-16 rounded-sm object-cover flex-shrink-0 border border-gray-200">
                    @else
                        <div class="w-16 h-16 rounded-sm bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0 border border-gray-200">
                            <i class="far fa-image text-gray-300"></i>
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-300 group-hover:text-brand-600 dark:group-hover:text-brand-400 line-clamp-2 transition-colors">
                            {{ $post->title }}
                        </h4>
                        <time class="text-xs text-gray-400 mt-2 block tracking-wider uppercase font-bold">
                            {{ $post->created_at->format('d/m/Y') }}
                        </time>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- CTA Liên hệ --}}
    <div class="bg-brand-900 border border-brand-800 rounded-sm p-8 text-center text-white relative overflow-hidden group">
        <div class="absolute inset-0 bg-brand-800 scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-500"></div>
        <div class="relative z-10 flex flex-col items-center">
            <h3 class="text-lg font-black uppercase tracking-wider mb-3 relative inline-block">
                Bạn cần tư vấn?
                <div class="w-12 h-1 bg-accent-500 mx-auto mt-2 rounded-sm"></div>
            </h3>
            <p class="text-sm text-brand-100 mb-6 px-2">Nhận giải pháp chuyển đổi số may đo riêng cho mô hình của bạn.</p>
            <a href="{{ route('contact.show') }}"
               class="inline-block w-full text-center bg-accent-500 text-white font-bold uppercase tracking-wider px-6 py-4 rounded-sm hover:bg-white hover:text-accent-500 transition-colors border border-accent-500">
                Gửi yêu cầu ngay
            </a>
        </div>
    </div>

</aside>
