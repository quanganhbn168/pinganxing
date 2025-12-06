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
    
    @livewireStyles

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
            z-index: 1000;
            padding-bottom: env(safe-area-inset-bottom); /* Cho iPhone X trở lên */
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

        /* Nút Install PWA (Chỉ hiện khi chưa cài) */
        #install-prompt {
            display: none;
            position: fixed;
            bottom: 80px; left: 20px; right: 20px;
            background: #333;
            color: #fff;
            padding: 15px;
            border-radius: 8px;
            z-index: 2000;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <div class="app-header">
        <h5>{{ $title ?? 'CNET TECH' }}</h5>
        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn-logout">
            <i class="fas fa-sign-out-alt"></i>
        </a>
    </div>

    {{-- NỘI DUNG CHÍNH (Slot của Livewire) --}}
    <div class="container-fluid">
        {{ $slot }}
    </div>

    {{-- BOTTOM MENU --}}
    @if(auth('admin')->check() && auth('admin')->user()->hasRole('staff'))
        @livewire('worker.mobile-bottom-nav')
    @else
        @livewire('admin.mobile-bottom-nav')
    @endif

    {{-- POPUP MỜI CÀI ĐẶT APP --}}
    <div id="install-prompt">
        <div class="d-flex justify-content-between align-items-center">
            <span>Cài đặt ứng dụng để truy cập nhanh hơn!</span>
            <button id="install-button" class="btn btn-sm btn-primary">Cài đặt</button>
        </div>
    </div>

    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">@csrf</form>

    <script src="{{ asset('vendor/adminlte/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    @livewireScripts
    @stack('js')

    {{-- SCRIPT XỬ LÝ PWA INSTALL --}}
    <script>
        let deferredPrompt;
        const installPrompt = document.getElementById('install-prompt');
        const installButton = document.getElementById('install-button');

        window.addEventListener('beforeinstallprompt', (e) => {
            // Ngăn Chrome tự hiện popup mặc định (để mình tự hiện cái đẹp hơn)
            e.preventDefault();
            deferredPrompt = e;
            // Hiện popup của mình
            installPrompt.style.display = 'block';
        });

        installButton.addEventListener('click', (e) => {
            installPrompt.style.display = 'none';
            if (deferredPrompt) {
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        console.log('User accepted the install prompt');
                    }
                    deferredPrompt = null;
                });
            }
        });
        
        // Đăng ký Service Worker đơn giản (để PWA hoạt động)
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                // Ta có thể tạo file sw.js sau, nhưng khai báo thế này là đủ để PWA nhận diện
                // navigator.serviceWorker.register('/sw.js'); 
            });
        }
    </script>
</body>
</html>