<!-- Swiper Slider using Flowbite Carousel or Swiper. Let's use Swiper since it's already there -->
<div class="swiper hero-swiper w-full h-[60vh] md:h-[80vh] relative">
    <div class="swiper-wrapper">
        @foreach($slideHeros as $slide)
        <div class="swiper-slide relative">
            <img src="{{ optional($slide->mainImage())->url() ?: asset($slide->image) }}" alt="{{ $slide->title }}" class="w-full h-full object-cover" />
            <div class="absolute inset-0 bg-gray-900/40"></div>
            <div class="absolute inset-0 flex items-center justify-center p-4">
                <div class="text-center text-white max-w-4xl">
                    <h1 class="text-4xl md:text-6xl font-extrabold mb-4 drop-shadow-lg tracking-tight">{{ $slide->title }}</h1>
                    <p class="text-lg md:text-2xl mb-8 drop-shadow-md">{{ $slide->description }}</p>
                    @if($slide->link)
                    <a href="{{ $slide->link }}" class="inline-flex items-center justify-center px-8 py-4 text-base font-medium text-white bg-blue-700 hover:bg-blue-800 rounded-full focus:ring-4 focus:ring-blue-300 shadow-xl transition-all hover:scale-105">
                        Xem chi tiết
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="swiper-pagination"></div>
    <div class="swiper-button-prev text-white"></div>
    <div class="swiper-button-next text-white"></div>
</div>

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var heroSwiper = new Swiper('.hero-swiper', {
            loop: true,
            autoplay: {
                delay: 5000,
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
    });
</script>
@endpush
