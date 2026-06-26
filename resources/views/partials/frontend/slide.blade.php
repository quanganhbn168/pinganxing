<!-- Swiper Hero Slider -->
<div class="swiper hero-swiper w-full aspect-[4/3] md:aspect-[16/9] relative bg-dark-primary">
    <div class="swiper-wrapper h-full">
        @if(isset($slides) && $slides->count())
            @foreach($slides as $slide)
            <div class="swiper-slide relative h-full">
                <!-- Background Image & Overlay -->
                <div class="absolute inset-0 z-0">
                    <img src="{{ $slide->image?->url ?? 'https://images.unsplash.com/photo-1528127269322-539801943592?q=80&w=2200&auto=format&fit=crop' }}" alt="{{ $slide->title }}" class="w-full h-full object-cover" />
                    <div class="absolute inset-0 bg-gradient-to-r from-dark-primary/95 via-dark-primary/60 to-black/30"></div>
                </div>

                <!-- Content -->
                <div class="absolute inset-0 z-10 flex flex-col justify-center pt-20">
                    <div class="max-w-7xl mx-auto px-4 lg:px-8 w-full">
                        <div class="max-w-3xl text-white" data-aos="fade-up">
                            @if($slide->subtitle)
                            <div class="font-script text-3xl md:text-4xl mb-3 text-yellow-brand" style="font-family: 'Pacifico', cursive;">
                                {{ $slide->subtitle }}
                            </div>
                            @endif

                            <h2 class="text-5xl md:text-7xl font-extrabold leading-tight drop-shadow-lg mb-6">
                                {{ $slide->title }}
                            </h2>

                            @if($slide->description)
                            <div class="text-lg md:text-2xl text-white/90 mb-8 max-w-2xl drop-shadow-md">
                                {!! strip_tags($slide->description) !!}
                            </div>
                            @endif

                            <div class="flex flex-wrap gap-4">
                                @if($slide->link)
                                <a href="{{ $slide->link }}" class="inline-flex items-center gap-2 px-8 py-4 rounded-xl bg-yellow-brand text-slate-900 font-extrabold hover:bg-amber-300 transition shadow-[0_10px_25px_rgba(251,191,36,0.4)] text-lg hover:-translate-y-1">
                                    {{ $slide->button_text ?? 'Xem chi tiết' }} <i class="fas fa-arrow-right text-sm"></i>
                                </a>
                                @endif

                                @if($slide->link_2)
                                <a href="{{ $slide->link_2 }}" class="inline-flex items-center gap-2 px-8 py-4 rounded-xl bg-white/10 text-white font-extrabold hover:bg-white/20 border border-white/20 transition backdrop-blur-md text-lg hover:-translate-y-1">
                                    {{ $slide->button_text_2 ?? 'Tìm hiểu thêm' }}
                                </a>
                                @endif
                            </div>

                            <!-- Features -->
                            <div class="flex flex-wrap gap-5 mt-12 text-sm font-bold">
                                <div class="flex items-center gap-2">
                                    <span class="w-8 h-8 rounded-full bg-yellow-brand/20 text-yellow-brand grid place-items-center"><i class="fas fa-check"></i></span>
                                    Tour chất lượng
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-8 h-8 rounded-full bg-yellow-brand/20 text-yellow-brand grid place-items-center"><i class="fas fa-check"></i></span>
                                    Giá tốt nhất
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-8 h-8 rounded-full bg-yellow-brand/20 text-yellow-brand grid place-items-center"><i class="fas fa-check"></i></span>
                                    Hỗ trợ 24/7
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-8 h-8 rounded-full bg-yellow-brand/20 text-yellow-brand grid place-items-center"><i class="fas fa-check"></i></span>
                                    Thanh toán an toàn
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        @endif
    </div>
    
    <!-- Default Navigation & Pagination -->
    <div class="swiper-button-prev !text-yellow-brand !w-12 !h-12 !after:text-2xl drop-shadow-md hover:scale-110 transition-transform"></div>
    <div class="swiper-button-next !text-yellow-brand !w-12 !h-12 !after:text-2xl drop-shadow-md hover:scale-110 transition-transform"></div>
    <div class="swiper-pagination !bottom-6"></div>
</div>

<style>
    /* Custom pagination bullets */
    .hero-swiper .swiper-pagination-bullet {
        width: 10px;
        height: 10px;
        background: rgba(255, 255, 255, 0.5);
        opacity: 1;
        transition: all 0.3s ease;
    }
    .hero-swiper .swiper-pagination-bullet-active {
        width: 24px;
        border-radius: 5px;
        background: var(--color-yellow-brand, #fbbf24);
    }
</style>

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if(typeof Swiper !== 'undefined') {
            var heroSwiper = new Swiper('.hero-swiper', {
                loop: true,
                effect: 'fade',
                fadeEffect: { crossFade: true },
                autoplay: {
                    delay: 6000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.hero-swiper .swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.hero-swiper .swiper-button-next',
                    prevEl: '.hero-swiper .swiper-button-prev',
                },
            });
        }
    });
</script>
@endpush
