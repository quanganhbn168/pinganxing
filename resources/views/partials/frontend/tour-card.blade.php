@php
    $isSale = $product->price_discount > 0 && $product->price_discount < $product->price;
    $salePercent = $isSale ? round((($product->price - $product->price_discount) / $product->price) * 100) : 0;
    
    // Fake data for demo since these fields might not exist explicitly in DB
    $rating = 4.9; // Or $product->rating if it exists
    $reviewsCount = rand(50, 500);
    $duration = '3N2Đ'; // Should come from $product->specifications or similar
    $subtitle = 'Khởi hành hằng tuần • Bay Vietnam Airlines'; 
    $features = ['Khách sạn 4 sao', 'Buffet sáng', 'Xe đưa đón'];
@endphp

<article class="bg-white rounded-3xl border border-slate-100 shadow-[0_12px_40px_rgba(15,23,42,0.06)] relative group overflow-hidden flex flex-col h-full hover:shadow-[0_20px_50px_rgba(15,23,42,0.12)] transition-shadow duration-300">
    <!-- Image Header -->
    <a href="{{ $product->slug_url ?? '#' }}" class="relative block h-56 overflow-hidden">
        <img src="{{ $product->image?->url ?? 'https://images.unsplash.com/photo-1528127269322-539801943592?q=80&w=900&auto=format&fit=crop' }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
        <div class="absolute inset-0 bg-gradient-to-b from-black/20 via-transparent to-transparent"></div>
        
        <!-- Badges (Top Left) -->
        <div class="absolute top-4 left-4 flex flex-col gap-2">
            @if($isSale)
                <span class="px-3.5 py-1.5 rounded-full text-xs font-extrabold text-white bg-orange-500 shadow-sm">
                    -{{ $salePercent }}%
                </span>
            @elseif($product->is_hot)
                <span class="px-3.5 py-1.5 rounded-full text-xs font-extrabold text-white bg-red-500 shadow-sm">
                    HOT
                </span>
            @else
                @foreach($product->tags?->take(1) ?? [] as $tag)
                    <span class="px-3.5 py-1.5 rounded-full text-xs font-extrabold text-white shadow-sm" style="background-color: {{ $tag->color ?? '#006b63' }}">
                        {{ $tag->name }}
                    </span>
                @endforeach
            @endif
        </div>

        <!-- Favorite Button (Top Right) -->
        <button class="absolute top-4 right-4 w-9 h-9 rounded-full bg-white flex items-center justify-center text-slate-400 hover:text-red-500 hover:bg-red-50 transition-colors shadow-sm">
            <i class="far fa-heart"></i>
        </button>
    </a>

    <!-- Card Body -->
    <div class="p-6 flex flex-col flex-grow">
        <!-- Rating & Duration -->
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-1.5 text-sm">
                <i class="fas fa-star text-yellow-brand text-xs"></i>
                <span class="font-bold text-slate-700">{{ $rating }}</span>
                <span class="text-slate-400 text-xs">({{ $reviewsCount }} đánh giá)</span>
            </div>
            <div class="px-3 py-1 rounded-full bg-teal-50 text-teal-700 text-xs font-extrabold">
                {{ $duration }}
            </div>
        </div>

        <!-- Title -->
        <a href="{{ $product->slug_url ?? '#' }}" class="block mb-2">
            <h3 class="text-[1.35rem] font-extrabold text-slate-900 leading-snug group-hover:text-primary transition-colors line-clamp-2">
                {{ $product->name }}
            </h3>
        </a>

        <!-- Subtitle -->
        <p class="text-sm text-slate-500 mb-5 pb-5 border-b border-slate-100">
            {{ $subtitle }}
        </p>

        <!-- Features -->
        <ul class="space-y-2.5 mb-6 flex-grow">
            @foreach($features as $feature)
                <li class="flex items-start gap-2.5 text-sm text-slate-600">
                    <div class="mt-0.5 text-primary text-[10px]">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <span>{{ $feature }}</span>
                </li>
            @endforeach
        </ul>

        <!-- Footer: Price & CTA -->
        <div class="flex items-end justify-between mt-auto pt-2">
            <div>
                <div class="text-[11px] text-slate-500 font-medium mb-1">Giá từ</div>
                <div class="flex items-baseline gap-2">
                    <div class="text-2xl font-extrabold text-primary">
                        {{ number_format($isSale ? $product->price_discount : $product->price, 0, ',', '.') }}đ
                    </div>
                    @if($isSale)
                        <div class="text-xs line-through text-slate-400 font-medium hidden sm:block">
                            {{ number_format($product->price, 0, ',', '.') }}đ
                        </div>
                    @endif
                </div>
            </div>
            <a href="{{ $product->slug_url ?? '#' }}" class="px-5 py-2.5 rounded-full bg-primary text-white text-sm font-bold hover:bg-dark-primary hover:-translate-y-0.5 transition-all shadow-[0_4px_12px_rgba(0,107,99,0.2)]">
                Đặt ngay
            </a>
        </div>
    </div>
</article>
