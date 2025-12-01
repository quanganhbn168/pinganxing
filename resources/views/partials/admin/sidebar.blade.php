<aside class="main-sidebar sidebar-dark-primary elevation-4">
    {{-- Brand --}}
    <a href="{{ route('admin.dashboard') }}" class="brand-link">
        <img src="{{ asset('favicon/icon-192.png') }}"
             alt="{{ $setting->name ?? config('app.name') }}"
             class="brand-image img-circle elevation-3" style="opacity:.8">
        <span class="brand-text font-weight-light">{{ $setting->name ?? config('app.name') }}</span>
    </a>

    {{-- Sidebar --}}
    <div class="sidebar">
        {{-- Menu --}}
        <nav class="mt-2">
            <x-sidebar-menu />
        </nav>
    </div>
</aside>
