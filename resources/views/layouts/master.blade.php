{{-- resources/views/layouts/master.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    {{-- Basic --}}
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- CSRF --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- Title & SEO --}}
    <title>@yield('title')</title>
    <meta name="description" content="@yield('meta_description', $setting->meta_description)">
    <meta name="keywords" content="@yield('meta_keywords', $setting->meta_keywords)">
    <meta name="robots" content="@yield('meta_robots', 'index, follow')">
    {{-- Canonical --}}
    <link rel="canonical" href="{{ url()->current() }}" />
    {{-- Open Graph --}}
    <meta property="og:type"        content="@yield('og_type','website')" />
    <meta property="og:title"       content="@yield('title', config('app.name')) " />
    <meta property="og:description" content="@yield('meta_description', $setting->meta_description)" />
    <meta property="og:url"         content="{{ url()->current() }}" />
    <meta property="og:site_name"   content="{{ $setting->name }}" />
    <meta property="og:image"       content="@yield('meta_image', asset($setting->meta_image) )" />
    {{-- Twitter Card --}}
    <meta name="twitter:card"        content="summary_large_image" />
    <meta name="twitter:title"       content="@yield('title', config('app.name'))" />
    <meta name="twitter:description" content="@yield('meta_description')" />
    <meta name="twitter:image"       content="@yield('meta_image', asset($setting->meta_image) )" />
    {{-- Fonts, Favicons --}}
    <link rel="icon" href="{{ asset($setting->favicon) }}" type="image/x-icon" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset($setting->favicon) }}" />
    {{-- CSS & JS --}}
    <link rel="stylesheet" href="{{asset('vendor/bootstrap/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('vendor/fontawesome/css/all.min.css')}}">
    <link rel="stylesheet" href="{{asset('vendor/swiper/swiper-bundle.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/sweetalert2/bootstrap-4.min.css')}}">
    <link rel="stylesheet" href="{{ asset('css/slide.css') }}?v={{ filemtime(public_path('css/slide.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/global.css') }}?v={{ filemtime(public_path('css/global.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ filemtime(public_path('css/style.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}?v={{ filemtime(public_path('css/responsive.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/project-slider.css') }}?v={{ filemtime(public_path('css/project-slider.css')) }}">

    @stack('css')
    {!!$setting->head_script!!}
    @stack('jsonld')
    @stack('conversion_script')
</head>
<body class="{{ Auth::check() ? 'logged-in' : '' }}">
    {!!$setting->body_script!!}
    @include('partials.frontend.header')
    @yield('content')
    @include('frontend.modal.contact')
    @include('frontend.modal.branch')
    @include('partials.frontend.footer')
    {{-- KHỐI CÁC NÚT HÀNH ĐỘNG CỐ ĐỊNH Ở GÓC MÀN HÌNH --}}
    <div class="contact-pills">

        {{-- Nút gọi điện (với hiệu ứng rung) --}}
        <a href="tel:{{ $setting->phone }}" class="contact-pill phone-pill">
            <div class="phone-icon-wrapper is-animating">
               <i class="fas fa-phone-alt"></i>
           </div>
       </a>

       {{-- Nút Zalo --}}
       <a href="{{ $setting->zalo }}" target="_blank" class="contact-pill zalo-pill">
            <i class="fas fa-comment-dots"></i>
        </a>
        
        {{-- Nút Messenger --}}
        <a href="https://m.me/your-facebook-page-id" target="_blank" class="contact-pill messenger-pill">
            <i class="fab fa-facebook-messenger"></i>
        </a>
        
        {{-- Nút Lên đầu trang (Back to top) --}}
        <a href="#" class="contact-pill back-to-top" id="js-back-to-top">
            <i class="fas fa-arrow-up"></i>
        </a>

    </div>
    <script src="{{asset('/js/jquery-3.7.1.min.js')}}?{{time()}}"></script>
    <script src="{{asset('/vendor/bootstrap/popper.min.js')}}?{{time()}}"></script>
    <script src="{{asset('/vendor/bootstrap/js/bootstrap.min.js')}}?{{time()}}"></script>
    <script src="{{asset('/vendor/swiper/swiper-bundle.min.js')}}?{{time()}}"></script>
    <script src="{{asset('plugins/sweetalert2/sweetalert2.min.js')}}"></script>
    <script src="{{ asset('js/cart.js') }}"></script>
    <script src="{{ asset('js/counter.js') }}"></script>
    <script src="{{ asset('js/TabbedSwiperHandler.js') }}?v={{ filemtime(public_path('js/TabbedSwiperHandler.js')) }}"></script>

    @if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Thành công',
            text: @json(session('success')),
            confirmButtonText: 'OK'
        });
    </script>
    @endif
    @if(session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Lỗi',
            text: @json(session('error')),
            confirmButtonText: 'OK'
        });
    </script>
    @endif
    {{-- Đặt ở cuối file master.blade.php, trước </body> --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Tìm tất cả các nút có class '.btn-crossover'
    const crossoverButtons = document.querySelectorAll('.btn-crossover');

    // 2. Lặp qua từng nút để gán sự kiện
    crossoverButtons.forEach(button => {
        const textElement = button.querySelector('.btn-crossover-text');
        const iconElement = button.querySelector('.btn-crossover-icon');

        // Chỉ thực hiện nếu tìm thấy cả chữ và icon
        if (textElement && iconElement) {
            
            // 3. Khi di chuột VÀO nút
            button.addEventListener('mouseenter', () => {
                // Tính toán khoảng cách cần di chuyển
                const textWidth = textElement.offsetWidth;
                const iconWidth = iconElement.offsetWidth;
                const gap = 8; // Giá trị gap trong CSS

                // Đẩy chữ sang phải (bằng chiều rộng của icon + gap)
                textElement.style.transform = `translateX(${iconWidth + gap}px)`;
                
                // Kéo icon sang trái (bằng chiều rộng của chữ + gap)
                iconElement.style.transform = `translateX(-${textWidth + gap}px)`;
            });

            // 4. Khi di chuột RA khỏi nút
            button.addEventListener('mouseleave', () => {
                // Trả chữ và icon về vị trí ban đầu
                textElement.style.transform = 'translateX(0)';
                iconElement.style.transform = 'translateX(0)';
            });
        }
    });
});
</script>
    <script type="text/javascript">
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'vi',      
                includedLanguages: 'vi,en', 
                autoDisplay: false
            }, 'google_translate_element');
            setActiveFlag();
        }
        function changeLanguage(lang) {
            var a = document.querySelector("#google_translate_element select");
            if (a) {
                a.value = lang;
                a.dispatchEvent(new Event('change'));
            }
        }
        function setActiveFlag() {
            var currentLang = getCookie('googtrans') ? getCookie('googtrans').split('/')[2] : 'vi';
            document.querySelectorAll('.language-switcher-flags a').forEach(function(el) {
                if (el.getAttribute('data-lang') === currentLang) {
                    el.classList.add('active');
                } else {
                    el.classList.remove('active');
                }
            });
        }
        function getCookie(name) {
            var value = "; " + document.cookie;
            var parts = value.split("; " + name + "=");
            if (parts.length == 2) return parts.pop().split(";").shift();
        }
        var originalTranslateElementInit = window.googleTranslateElementInit;
        window.googleTranslateElementInit = function() {
            originalTranslateElementInit();
            var observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if(mutation.type === 'attributes' && mutation.attributeName === 'class' && mutation.target.nodeName === 'BODY') {
                        if(!document.body.classList.contains('google-translating')) {
                            setActiveFlag();
                        }
                    }
                });
            });
            observer.observe(document.body, { attributes: true });
        };
    </script>
    <script>
        $(document).ready(function(){
            $('#contactModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget); 
                var productName = button.data('name'); 
                var modal = $(this);
                var messageContent = "Tôi đang quan tâm đến sản phẩm: " + productName + "\n\n";
                var messageTextarea = modal.find('textarea#message');
                messageTextarea.val(messageContent).focus();
                messageTextarea[0].setSelectionRange(messageContent.length, messageContent.length);
            });

        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
    const backToTopButton = document.getElementById('js-back-to-top');

    if (backToTopButton) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 300) {
                backToTopButton.classList.add('show');
            } else {
                backToTopButton.classList.remove('show');
            }
        });

        backToTopButton.addEventListener('click', function (e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
});
    </script>
    @stack('js')
</body>
</html>