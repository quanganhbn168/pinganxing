<!-- Topbar -->
<div class="bg-gray-900 text-white w-full py-2 hidden lg:block border-b border-gray-800 relative z-[101]">
    <div class="max-w-screen-xl mx-auto px-4 flex justify-between items-center text-xs font-semibold tracking-wide h-6">
        <div class="flex items-center gap-6">
            @if(!empty($setting->email))
                <a href="mailto:{{ $setting->email }}"
                    class="flex items-center gap-2 hover:text-blue-400 transition-colors">
                    <i class="fas fa-envelope text-gray-400"></i> {{ $setting->email }}
                </a>
            @endif
            <div class="flex items-center gap-2 text-gray-300">
                <i class="fas fa-clock text-gray-400"></i> {{ $setting->working_hours ?? 'T2 - T7: 08:00 - 17:30' }}
            </div>
        </div>
        <div class="flex items-center gap-4">
            <span class="flex items-center gap-2">
                <i class="fas fa-headset text-blue-400"></i> Hỗ trợ 24/7:
                <a href="tel:{{ preg_replace('/\s+/', '', $setting->phone ?? '') }}"
                    class="text-blue-400 hover:text-white transition-colors">{{ $setting->phone_display ?? $setting->phone ?? '' }}</a>
            </span>
            <div class="h-4 w-px bg-gray-700"></div>
        </div>
    </div>
</div>

<header
    class="bg-white border-b border-gray-100 dark:bg-gray-900 dark:border-gray-800 w-full transition-all duration-300 sticky top-0 z-[100] shadow-sm"
    id="main-header">
    <nav class="max-w-screen-xl mx-auto flex flex-wrap items-center justify-between p-4">
        <!-- Logo -->
        <a href="{{ url('/') }}" class="flex items-center space-x-3 rtl:space-x-reverse">
            <img src="{{ !empty($globalLogoUrl) ? $globalLogoUrl : asset('images/setting/no-image.png') }}"
                class="h-12 md:h-14 object-contain" alt="{{ $setting->site_name ?? 'Logo' }}" />
        </a>

        <!-- Mobile Toggle & Actions -->
        <div class="flex md:order-2 space-x-3 md:space-x-4 rtl:space-x-reverse items-center">

            <!-- Language Switcher -->
            <x-frontend.language-switcher type="desktop" />

            <!-- Hamburger Button (Triggers Drawer) -->
            <button type="button" data-drawer-target="drawer-navigation" data-drawer-show="drawer-navigation"
                aria-controls="drawer-navigation"
                class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-600 rounded-xl md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-800 dark:focus:ring-gray-700 transition-colors">
                <span class="sr-only">Mở menu chính</span>
                <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 17 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M1 1h15M1 7h15M1 13h15" />
                </svg>
            </button>
        </div>

        <!-- Desktop Menu -->
        <div class="hidden w-full md:block md:w-auto md:order-1" id="navbar-desktop">
            <ul
                class="flex flex-col font-medium p-4 md:p-0 mt-4 border border-gray-100 rounded-xl bg-gray-50 md:space-x-4 lg:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-white dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700">
                @if(isset($headerMenu) && count($headerMenu) > 0)
                    @foreach($headerMenu as $menuItem)
                        @if($menuItem->children && $menuItem->children->count() > 0)
                            <li>
                                <button id="dropdownNavbarLink-{{ $loop->index }}"
                                    data-dropdown-toggle="dropdownNavbar-{{ $loop->index }}"
                                    class="flex items-center justify-between w-full py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-600 md:p-0 md:w-auto dark:text-white md:dark:hover:text-blue-500 dark:focus:text-white dark:border-gray-700 dark:hover:bg-gray-700 md:dark:hover:bg-transparent font-semibold uppercase tracking-wide text-sm transition-colors">
                                    {{ $menuItem->title }}
                                    <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 10 6">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="m1 1 4 4 4-4" />
                                    </svg>
                                </button>
                                <!-- Dropdown menu -->
                                <div id="dropdownNavbar-{{ $loop->index }}"
                                    class="z-50 hidden font-normal bg-white divide-y divide-gray-100 rounded-xl shadow-xl w-48 dark:bg-gray-800 dark:divide-gray-700 border border-gray-100 dark:border-gray-700">
                                    <ul class="py-2 text-sm text-gray-700 dark:text-gray-300"
                                        aria-labelledby="dropdownNavbarLink-{{ $loop->index }}">
                                        @foreach($menuItem->children as $childItem)
                                            <li>
                                                <a href="{{ $childItem->link }}" target="{{ $childItem->link_target }}"
                                                    class="block px-4 py-2.5 hover:bg-blue-50 dark:hover:bg-blue-900/40 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors">
                                                    @if($childItem->icon)<i
                                                    class="{{ $childItem->icon }} text-gray-400 text-xs mr-2"></i>@else<i
                                                        class="fas fa-angle-right text-gray-400 text-xs mr-2"></i>@endif
                                                    {{ $childItem->title }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </li>
                        @else
                            <li>
                                <a href="{{ $menuItem->link }}" target="{{ $menuItem->link_target }}"
                                    class="block py-2 px-3 rounded md:hover:bg-transparent md:border-0 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent font-semibold uppercase tracking-wide text-sm transition-colors {{ $menuItem->is_active_route ? 'text-blue-600 border-b-2 border-blue-600 md:pb-1' : 'text-gray-900 hover:bg-gray-100 md:hover:text-blue-600' }}">
                                    @if($menuItem->icon)<i class="{{ $menuItem->icon }} mr-1"></i>@endif {{ $menuItem->title }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                @else
                    <li><a href="/"
                            class="block py-2 px-3 rounded md:border-0 md:p-0 font-semibold uppercase tracking-wide text-sm transition-colors {{ request()->is('/') ? 'text-blue-600 border-b-2 border-blue-600 md:pb-1' : 'text-gray-900 hover:text-blue-600 dark:text-white md:dark:hover:text-blue-500' }}">Trang
                            chủ</a></li>
                    <li><a href="/dich-vu"
                            class="block py-2 px-3 rounded md:border-0 md:p-0 font-semibold uppercase tracking-wide text-sm transition-colors {{ request()->is('dich-vu*') ? 'text-blue-600 border-b-2 border-blue-600 md:pb-1' : 'text-gray-900 hover:text-blue-600 dark:text-white md:dark:hover:text-blue-500' }}">Dịch
                            vụ</a></li>
                    <li><a href="/linh-vuc"
                            class="block py-2 px-3 rounded md:border-0 md:p-0 font-semibold uppercase tracking-wide text-sm transition-colors {{ request()->is('linh-vuc*') ? 'text-blue-600 border-b-2 border-blue-600 md:pb-1' : 'text-gray-900 hover:text-blue-600 dark:text-white md:dark:hover:text-blue-500' }}">Lĩnh
                            vực</a></li>
                    <li><a href="/du-an"
                            class="block py-2 px-3 rounded md:border-0 md:p-0 font-semibold uppercase tracking-wide text-sm transition-colors {{ request()->is('du-an*') ? 'text-blue-600 border-b-2 border-blue-600 md:pb-1' : 'text-gray-900 hover:text-blue-600 dark:text-white md:dark:hover:text-blue-500' }}">Dự
                            án</a></li>
                    <li><a href="/san-pham"
                            class="block py-2 px-3 rounded md:border-0 md:p-0 font-semibold uppercase tracking-wide text-sm transition-colors {{ request()->is('san-pham*') ? 'text-blue-600 border-b-2 border-blue-600 md:pb-1' : 'text-gray-900 hover:text-blue-600 dark:text-white md:dark:hover:text-blue-500' }}">Sản
                            phẩm</a></li>
                @endif
            </ul>
        </div>
    </nav>
</header>

<x-frontend.mobile-drawer :headerMenu="$headerMenu ?? []" :setting="$setting" />

<div id="google_translate_element2"></div>
<style type="text/css">
    #goog-gt-tt {
        display: none !important;
    }

    .goog-te-banner-frame {
        display: none !important;
    }

    .goog-te-menu-value:hover {
        text-decoration: none !important;
    }

    body {
        top: 0 !important;
    }

    #google_translate_element2 {
        display: none !important;
    }
</style>

@push('js')
    <script type="text/javascript">
        function googleTranslateElementInit2() { new google.translate.TranslateElement({ pageLanguage: 'vi', autoDisplay: false }, 'google_translate_element2'); }
    </script>
    <script type="text/javascript"
        src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit2"></script>
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

    <script>
        // Auto update flag img based on cookie
        function updateFlag(flagSrc) {
            document.querySelectorAll('.current-flag-img').forEach(img => {
                img.src = flagSrc;
            });
        }
        function getCookie(name) {
            var v = document.cookie.match('(^|;) ?' + name + '=([^;]*)(;|$)');
            return v ? v[2] : null;
        }
        var langCookie = getCookie('googtrans');
        if (langCookie) {
            var lang = langCookie.split('|').pop().split('/').pop();
            var flag = '{{ asset("images/flags/vn.png") }}';
            if (lang == 'en') flag = '{{ asset("images/flags/us.png") }}';
            else if (lang == 'zh-CN') flag = '{{ asset("images/flags/cn.png") }}';
            else if (lang == 'ko') flag = '{{ asset("images/flags/kr.png") }}';
            updateFlag(flag);
        }
    </script>
@endpush