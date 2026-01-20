<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? View::getSection('title', 'Admin Panel') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon/favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/favicon-180x180.png') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/sweetalert2/bootstrap-4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modal.css') }}">
    
    {{-- Mobile Bottom Nav Styles --}}
    <style>
        @media (max-width: 767.98px) {
            .content-wrapper {
                padding-bottom: 80px !important; /* Chừa chỗ cho bottom nav */
            }
            .mobile-bottom-nav {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                z-index: 1040;
                background: #fff;
                border-top: 1px solid #dee2e6;
                box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
            }
        }
        @media (min-width: 768px) {
            .mobile-bottom-nav {
                display: none !important;
            }
        }
    </style>
    
    @stack('css')
    @livewireStyles
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    @include('partials.admin.navbar')
    @include('partials.admin.sidebar')
    @include('partials.media.modal') 

    <div class="content-wrapper">
        @if(!isset($slot))
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>@yield('content_header_title', 'Bảng điều khiển')</h1>
                    </div>
                    <div class="col-sm-6">
                        <x-breadcrumb :page-title="$pageTitle ?? null" />
                    </div>
                </div>
            </div>
        </section>
    @endif
        <section class="content">
            <div class="container-fluid">
                {{ $slot ?? '' }}
            
            {{-- Sau đó mới đến yield content cũ --}}
                @yield('content')
            </div>
        </section>
    </div>

    @include('partials.admin.footer')

</div>

<script src="{{ asset('vendor/adminlte/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
<script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
<script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('vendor/adminlte/plugins/select2/js/select2.full.min.js') }}"></script>
<script src="{{ asset('js/media-picker.js') }}"></script>
<script src="{{ asset('js/admin/universal-bulk.js') }}"></script>

@livewireScripts
@stack('js')

<script>
  const Toast = Swal.mixin({ toast:true, position:'top-end', showConfirmButton:false, timer:3000, timerProgressBar:true });
  document.addEventListener('livewire:initialized', () => {
    console.log('%cLivewire booted','color:#22c55e');
    if (window.toastr) {
      Livewire.on('notify', ({type, message}) => toastr[type](message));
    }
  });
  $(document).on('click', '.btn-toggle', function () {
    const btn = $(this), data = btn.data();
    $.post('{{ route('admin.toggle') }}', {
      _token: '{{ csrf_token() }}', id: data.id, model: data.model, field: data.field
    }).done(res => {
      if(res.value){ btn.removeClass('btn-secondary').addClass('btn-success').text('Hiện'); }
      else{ btn.removeClass('btn-success').addClass('btn-secondary').text('Ẩn'); }
      Toast.fire({ icon:'success', title: res.message || 'Cập nhật thành công' });
    }).fail(() => Toast.fire({ icon:'error', title:'Đã xảy ra lỗi' }));
  });
</script>

@if(session('success'))
<script>Toast.fire({ icon:'success', title: @json(session('success')) });</script>
@endif
@if(session('error'))
<script>Toast.fire({ icon:'error', title: @json(session('error')) });</script>
@endif
</body>
</html>
