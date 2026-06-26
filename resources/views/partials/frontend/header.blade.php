
<header
    @if(request()->is('/'))
    x-data="{ 
        scrolled: false, 
        lastScrollY: 0, 
        hidden: false,
        mobileMenu: false,
        initHeader() {
            window.addEventListener('scroll', () => {
                const currentScrollY = window.scrollY;
                this.scrolled = currentScrollY > 40;
                
                // Hide if scrolling down, show if scrolling up
                if (currentScrollY > this.lastScrollY && currentScrollY > 100 && !this.mobileMenu) {
                    this.hidden = true;
                } else {
                    this.hidden = false;
                }
                
                this.lastScrollY = currentScrollY;
            }, { passive: true });
        }
    }"
    x-init="initHeader()"
    :class="{
        'bg-dark-primary/95 shadow-lg backdrop-blur-xl': scrolled, 
        'glass-header': !scrolled,
        '-translate-y-full': hidden,
        'translate-y-0': !hidden
    }"
    class="fixed top-0 left-0 right-0 z-[100] transition-transform duration-300"
    style="top: auto;" 
    @else
    x-data="{ mobileMenu: false }"
    class="bg-dark-primary shadow-lg z-[100] sticky top-0"
    @endif
    id="main-header"
>
    <div class="max-w-7xl mx-auto px-4 lg:px-8">
        <div class="h-20 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <img src="{{ !empty($globalLogoUrl) ? $globalLogoUrl : asset('images/setting/no-image.png') }}" class="h-12 md:h-14 object-contain" alt="{{ $setting->site_name ?? config('app.name') }}" />
                <div class="leading-tight hidden sm:block">
                    <div class="text-white text-2xl font-extrabold tracking-tight">{{ $setting->site_name ?? config('app.name') }}</div>
                    <div class="text-white/70 text-xs">Tận hưởng từng khoảnh khắc</div>
                </div>
            </a>

            <nav class="hidden lg:flex items-center gap-8 text-sm font-semibold text-white/90">
                @if(isset($headerMenu) && count($headerMenu) > 0)
                    @foreach($headerMenu as $menuItem)
                        <a href="{{ $menuItem->link }}" class="hover:text-yellow-brand transition-colors {{ $menuItem->is_active_route ? 'text-yellow-brand' : '' }}">{{ $menuItem->title }}</a>
                    @endforeach
                @else
                    <a href="/" class="hover:text-yellow-brand transition-colors {{ request()->is('/') ? 'text-yellow-brand' : '' }}">Trang chủ</a>
                    <a href="#tours" class="hover:text-yellow-brand transition-colors">Tour</a>
                    <a href="#services" class="hover:text-yellow-brand transition-colors">Dịch vụ</a>
                    <a href="#destinations" class="hover:text-yellow-brand transition-colors">Điểm đến</a>
                    <a href="#blog" class="hover:text-yellow-brand transition-colors">Kinh nghiệm</a>
                    <a href="#about" class="hover:text-yellow-brand transition-colors">Giới thiệu</a>
                    <a href="#contact" class="hover:text-yellow-brand transition-colors">Liên hệ</a>
                @endif
            </nav>

            <div class="hidden lg:flex items-center gap-5">
                <a href="tel:{{ preg_replace('/\s+/', '', $setting->phone ?? '19001234') }}" class="flex items-center gap-3 text-white group">
                    <div class="w-10 h-10 rounded-full border border-white/30 grid place-items-center group-hover:bg-white/10 transition">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <div class="leading-tight">
                        <div class="font-bold">{{ $setting->phone_display ?? $setting->phone ?? '1900 1234' }}</div>
                        <div class="text-xs text-white/70">Hỗ trợ 24/7</div>
                    </div>
                </a>

                <a href="#booking" class="px-6 py-3 rounded-xl bg-yellow-brand text-slate-900 font-bold hover:bg-amber-300 transition shadow-lg shadow-yellow-brand/20">
                    Đặt tour ngay
                </a>
            </div>

            <!-- Hamburger Button -->
            <button @click="mobileMenu = !mobileMenu" class="lg:hidden w-11 h-11 rounded-xl bg-white/10 text-white border border-white/20 grid place-items-center hover:bg-white/20 transition">
                <i class="fas" :class="mobileMenu ? 'fa-times' : 'fa-bars'"></i>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="mobileMenu" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-4"
         class="lg:hidden bg-dark-primary border-t border-white/10 absolute w-full shadow-2xl" 
         style="display: none;">
        <div class="px-5 py-5 space-y-4 text-white font-semibold">
            @if(isset($headerMenu) && count($headerMenu) > 0)
                @foreach($headerMenu as $menuItem)
                    <a href="{{ $menuItem->link }}" class="block {{ $menuItem->is_active_route ? 'text-yellow-brand' : '' }}">{{ $menuItem->title }}</a>
                @endforeach
            @else
                <a href="/" class="block">Trang chủ</a>
                <a href="#tours" class="block">Tour</a>
                <a href="#services" class="block">Dịch vụ</a>
                <a href="#destinations" class="block">Điểm đến</a>
                <a href="#blog" class="block">Kinh nghiệm</a>
                <a href="#contact" class="block">Liên hệ</a>
            @endif
            <a href="#booking" class="block text-center bg-yellow-brand text-slate-900 rounded-xl py-3 mt-4 shadow-lg">Đặt tour ngay</a>
        </div>
    </div>
</header>

<div id="google_translate_element2"></div>
<style type="text/css">
    #goog-gt-tt { display: none !important; }
    .goog-te-banner-frame { display: none !important; }
    .goog-te-menu-value:hover { text-decoration: none !important; }
    body { top: 0 !important; }
    #google_translate_element2 { display: none !important; }
    
    @if(request()->is('/'))
    /* Fix for fixed header over content */
    main#main-content {
        padding-top: 0 !important; /* because hero section covers top */
    }
    @endif
</style>

@push('js')
    <script type="text/javascript">
        function googleTranslateElementInit2() { new google.translate.TranslateElement({ pageLanguage: 'vi', autoDisplay: false }, 'google_translate_element2'); }
    </script>
    <script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit2"></script>
    <script type="text/javascript">
        function doGTranslate(langPair) {
            if (!langPair || langPair === '') return;
            var lang = langPair.split('|')[1];
            var selectField = document.querySelector('.goog-te-combo');
            if (!selectField || !selectField.options || selectField.options.length === 0) {
                setTimeout(function () { doGTranslate(langPair); }, 500);
                return;
            }
            selectField.value = lang;
            selectField.dispatchEvent(new Event('change', { bubbles: true }));
        }
    </script>
@endpush
