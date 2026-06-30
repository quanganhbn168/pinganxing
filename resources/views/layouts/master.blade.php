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
    <title>@yield('title', $setting->site_name)</title>
    <meta name="description" content="@yield('meta_description', $setting->meta_description ?? '')">
    <meta name="keywords" content="@yield('meta_keywords', $setting->meta_keywords ?? '')">
    <meta name="robots" content="@yield('meta_robots', 'index, follow')">
    {{-- Canonical --}}
    <link rel="canonical" href="{{ url()->current() }}" />
    {{-- Open Graph --}}
    <meta property="og:type" content="@yield('og_type', 'website')" />
    <meta property="og:title" content="@yield('title', $setting->site_name ?? '')" />
    <meta property="og:description" content="@yield('meta_description', $setting->meta_description ?? '')" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:site_name" content="{{ $setting->site_name ?? config('app.name') }}" />
    <meta property="og:image" content="@yield('meta_image', $globalMetaImageUrl)" />
    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="@yield('title', $setting->site_name ?? '')" />
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
    

    @include('partials.frontend.header')

    <main id="main-content" class="min-h-screen">
        @yield('content')
    </main>

    @include('partials.frontend.footer')

    @include('partials.frontend.floating-actions')

    @include('partials.frontend.wechat-qr-modal')

    {{-- Bundle Frontend JS via Vite --}}
    @vite(['resources/js/frontend.js'])

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const backToTopButton = document.getElementById('js-back-to-top');
            if (backToTopButton) {
                window.addEventListener('scroll', function () {
                    if (window.scrollY > 300) {
                        backToTopButton.classList.add('is-visible');
                    } else {
                        backToTopButton.classList.remove('is-visible');
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
    {!! $setting->body_script ?? '' !!}
</body>

</html>
