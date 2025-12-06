<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <title>{{ $title ?? 'Kỹ thuật viên' }}</title>
    
    {{-- Khai báo PWA --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#007bff">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    {{-- Vẫn dùng CSS AdminLTE/Bootstrap nhưng custom lại --}}
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    <!-- Toastr -->
    <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
    
    @livewireStyles
    @stack('css')

    <style>
        body {
            background-color: #f4f6f9;
            padding-bottom: 80px; /* Chừa chỗ cho menu dưới */
            padding-top: 60px;    /* Chừa chỗ cho header trên */
            /* Chống chọn văn bản để cảm giác giống App hơn */
            -webkit-user-select: none; 
            user-select: none;
        }

        /* HEADER CỐ ĐỊNH */
        .app-header {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: 60px;
            background: #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 15px;
        }
        .app-header h5 { margin: 0; font-weight: bold; color: #333; }
        .app-header .btn-logout { position: absolute; right: 15px; color: #dc3545; }

        /* BOTTOM NAVIGATION BAR (Thanh điều hướng dưới) */
        .bottom-nav {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            height: 70px;
            background: #fff;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: space-around;
            align-items: center;
            z-index: 1050; /* Higher than footer */
            padding-bottom: env(safe-area-inset-bottom);
        }

        .nav-item-mobile {
            text-align: center;
            color: #999;
            text-decoration: none;
            font-size: 11px;
            flex: 1;
            padding: 10px 0;
        }

        .nav-item-mobile i {
            display: block;
            font-size: 22px;
            margin-bottom: 4px;
        }

        .nav-item-mobile.active {
            color: #007bff;
            font-weight: bold;
        }

        /* Helper for big counters in Dashboard */
        .card-big-counter {
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.2s;
            cursor: pointer;
            height: 100%;
        }
        .card-big-counter:active {
            transform: scale(0.98);
        }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <div class="app-header">
        <h5>KỸ THUẬT VIÊN</h5>
        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn-logout">
            <i class="fas fa-sign-out-alt"></i>
        </a>
    </div>

    {{-- NỘI DUNG CHÍNH --}}
    <div class="container-fluid">
        {{ $slot }}
    </div>

    {{-- BOTTOM MENU (WORKER VERSION) --}}
    @livewire('worker.mobile-bottom-nav')

    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">@csrf</form>

    <script src="{{ asset('vendor/adminlte/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- Toastr -->
    <script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
    @livewireScripts
    @stack('js')
</body>
</html>
