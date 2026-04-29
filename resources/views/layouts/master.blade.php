{{-- resources/views/layouts/master.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    {{-- Basic --}}
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- CSRF --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- Title & SEO --}}
    @php
        $siteName = $setting->site_name ?? config('app.name');
    @endphp
    <title>@hasSection('title') @yield('title') - {{ $siteName }} @else {{ $siteName }} @endif</title>
    <meta name="description" content="@yield('meta_description', $setting->meta_description ?? '')">
    <meta name="keywords" content="@yield('meta_keywords', $setting->meta_keywords ?? '')">
    <meta name="robots" content="@yield('meta_robots', 'index, follow')">
    {{-- Canonical --}}
    <link rel="canonical" href="{{ url()->current() }}" />
    {{-- Open Graph --}}
    <meta property="og:type" content="@yield('og_type', 'website')" />
    <meta property="og:title"
        content="@hasSection('title') @yield('title') - {{ $siteName }} @else {{ $siteName }} @endif" />
    <meta property="og:description" content="@yield('meta_description', $setting->meta_description ?? '')" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:site_name" content="{{ $setting->site_name ?? config('app.name') }}" />
    <meta property="og:image" content="@yield('meta_image', $globalMetaImageUrl)" />
    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title"
        content="@hasSection('title') @yield('title') - {{ $siteName }} @else {{ $siteName }} @endif" />
    <meta name="twitter:description" content="@yield('meta_description', $setting->meta_description ?? '')" />
    <meta name="twitter:image" content="@yield('meta_image', $globalMetaImageUrl)" />
    {{-- Fonts, Favicons --}}
    <link rel="icon" href="{{ rtrim($globalFaviconUrl, '?') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ rtrim($globalFaviconUrl, '?') }}" />

    {{-- Bundle Frontend CSS via Vite (Tailwind v4) --}}
    @vite(['resources/css/frontend.css'])

    @stack('css')
    {!! $setting->head_script ?? '' !!}
    @stack('jsonld')
    @stack('conversion_script')
</head>

<body
    class="bg-white text-gray-900 font-sans antialiased dark:bg-gray-900 dark:text-gray-100 {{ Auth::check() ? 'logged-in' : '' }}">
    {!! $setting->body_start_script ?? '' !!}
    {!! $setting->body_script ?? '' !!}

    @include('partials.frontend.header')

    <main id="main-content" class="min-h-screen">
        @yield('content')
    </main>

    @include('partials.frontend.footer')

    {{-- KHỐI CÁC NÚT HÀNH ĐỘNG CỐ ĐỊNH Ở GÓC MÀN HÌNH --}}
    <div class="fixed bottom-6 right-6 flex flex-col gap-3 z-50">
        {{-- Nút gọi điện (với hiệu ứng rung bg-color Tailwind) --}}
        <a href="tel:{{ $setting->phone ?? '' }}"
            class="w-12 h-12 flex items-center justify-center bg-green-500 hover:bg-green-600 text-white rounded-full shadow-lg transition-transform hover:scale-110">
            <i class="fas fa-phone-alt animate-pulse"></i>
        </a>
        {{-- Nút Zalo --}}
        <a href="{{ $setting->zalo ?? '' }}" target="_blank"
            class="w-12 h-12 flex items-center justify-center bg-blue-500 hover:bg-blue-600 text-white rounded-full shadow-lg transition-transform hover:scale-110">
            <i class="fas fa-comment-dots"></i>
        </a>
        {{-- Nút Lên đầu trang (Back to top) --}}
        <a href="#"
            class="w-12 h-12 hidden items-center justify-center bg-gray-800 hover:bg-gray-700 text-white rounded-full shadow-lg transition-transform hover:scale-110"
            id="js-back-to-top">
            <i class="fas fa-arrow-up"></i>
        </a>
    </div>

    {{-- Bundle Frontend JS via Vite --}}
    @vite(['resources/js/frontend.js'])

    <script>
        // Back to top logic
        document.addEventListener('DOMContentLoaded', function () {
            const backToTopButton = document.getElementById('js-back-to-top');
            if (backToTopButton) {
                window.addEventListener('scroll', function () {
                    if (window.scrollY > 300) {
                        backToTopButton.classList.remove('hidden');
                        backToTopButton.classList.add('flex');
                    } else {
                        backToTopButton.classList.add('hidden');
                        backToTopButton.classList.remove('flex');
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
