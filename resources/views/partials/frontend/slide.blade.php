<div class="swiper main-slider">

    <div class="swiper-wrapper">

        @foreach($slides as $i => $slide)

            <div class="swiper-slide">

                <img

                    src="{{ optional($slide->mainImage())->url() }}"

                    alt="{{ $slide->name }}"

                    title="{{ $slide->name }}"

                    decoding="async"

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

  if (!window.Swiper) return;



  new Swiper('.main-slider', {

    loop: true,

    speed: 600,

    centeredSlides: true,

    spaceBetween: 8,



    autoplay: {

      delay: 3500,

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



    keyboard: { enabled: true },

    a11y: {

      enabled: true,

      prevSlideMessage: 'Slide trước',

      nextSlideMessage: 'Slide sau'

    },



    breakpoints: {

      576: { spaceBetween: 10 },

      768: { spaceBetween: 12 },

      992: { spaceBetween: 14 },

      1200:{ spaceBetween: 16 }

    }

  });

})();

</script>

@endpush

