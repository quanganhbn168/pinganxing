<div class="swiper main-slider">

    <div class="swiper-wrapper">

        @foreach($slides as $i => $slide)

            <div class="swiper-slide">

                <img

                    src="{{ optional($slide->mainImage())->url() }}"

                    alt="{{ $slide->name }}"

                    title="{{ $slide->name }}"

                    decoding="async"

                    @if($i==0) fetchpriority="high" @endif
                    @if($i>0) loading="lazy" @endif
                >

            </div>

        @endforeach

    </div>

    <div class="swiper-pagination"></div>

    <div class="swiper-button-prev"></div>

    <div class="swiper-button-next"></div>

</div>



@push('css')
@endpush



@push('js')

<script>

(function () {
  if (typeof Swiper === 'undefined') return;

  new Swiper('.main-slider', {
    loop: true,
    speed: 800,
    slidesPerView: 1,
    spaceBetween: 0, 
    threshold: 15, // Kéo mạnh hơn chút để tránh nhạy quá gây nhảy 2 slide
    touchMoveStopPropagation: true,

    autoplay: {
      delay: 5000,
      disableOnInteraction: false,
      pauseOnMouseEnter: true
    },

    pagination: {
      el: '.main-slider .swiper-pagination',
      clickable: true
    },

    navigation: {
      nextEl: '.main-slider .swiper-button-next',
      prevEl: '.main-slider .swiper-button-prev'
    },

    keyboard: { enabled: true }
  });
})();

</script>

@endpush

