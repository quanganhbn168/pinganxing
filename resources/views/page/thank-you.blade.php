{{-- resources/views/pages/thank-you.blade.php --}}

@extends('layouts.master')

@section('title', 'Cảm ơn bạn đã liên hệ')
@section('meta_robots', 'noindex, nofollow') {{-- Không cho Google index trang này --}}

{{-- Đẩy mã theo dõi sự kiện vào vị trí @stack('conversion_script') trong master layout --}}
@push('conversion_script')
    <script>
      gtag('event', 'conversion', {'send_to': 'AW-833638621/dioGCP6HwZIYEN2hwY0D'});
    </script>
@endpush


@section('content')
    <div class="container" style="padding: 80px 0;">
        <div class="row">
            <div class="col-md-8 offset-md-2 text-center">
                <h1 style="color: green; font-size: 48px;">✅</h1>
                <h1>Cảm ơn bạn đã gửi thông tin!</h1>
                <p class="lead">Chúng tôi đã nhận được yêu cầu của bạn và sẽ liên hệ lại trong thời gian sớm nhất.</p>
                <a href="{{ route('home') }}" class="btn btn-primary mt-3">Quay về Trang chủ</a>
            </div>
        </div>
    </div>
@endsection
