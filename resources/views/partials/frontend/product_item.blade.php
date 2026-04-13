<div class="group bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-lg dark:bg-gray-800 dark:border-gray-700 transition-all overflow-hidden flex flex-col h-full">
    <a href="{{ $product->slug_url }}" class="block relative aspect-square overflow-hidden bg-gray-50 dark:bg-gray-900">
        <img src="{{ !empty($product->image) ? asset($product->image) : (optional($product->mainImage())->url() ?: asset('images/setting/no-image.png')) }}" 
             alt="{{ $product->name }}" 
             class="w-full h-full object-contain p-4 group-hover:scale-110 transition-transform duration-500">
        @if(isset($product->is_new) && $product->is_new)
            <span class="absolute top-3 right-3 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-md uppercase tracking-wide">Mới</span>
        @endif
    </a>
    
    <div class="p-4 md:p-5 flex flex-col flex-1">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-blue-600 transition-colors flex-1">
            <a href="{{ $product->slug_url }}">
                {{ $product->name }}
            </a>
        </h3>
        
        <div class="mt-auto border-t border-gray-100 dark:border-gray-700 pt-3 flex items-center justify-between">
            <div class="text-blue-700 dark:text-blue-400 font-bold text-lg">
                @if($product->price > 0)
                    {{ number_format($product->price) }}<span class="text-sm font-normal underline ml-0.5">đ</span>
                @else
                    <span class="text-red-500">Liên hệ</span>
                @endif
            </div>
            <a href="{{ $product->slug_url }}" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-50 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                <i class="fas fa-arrow-right text-sm"></i>
            </a>
        </div>
    </div>
</div>
