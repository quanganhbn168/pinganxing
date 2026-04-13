@extends('layouts.master')
@section('title','Đăng ký tài khoản')

@push('css')
<link rel="stylesheet" href="{{asset('css/auth.css')}}">
@endpush

@section("content")
<div id="breadcrumb" class="bg-light">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-light m-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Đăng ký tài khoản</li>
            </ol>
        </nav>
    </div>
</div>

<div id="wrapper">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="login-box bg-white p-4 border">
                    <h3 class="text-center mb-4">ĐĂNG KÝ TÀI KHOẢN</h3>
                    <form action="{{ route('register') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="Họ và tên" value="{{ old('name') }}" required>
                            @error('name')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="Email" value="{{ old('email') }}" required>
                             @error('email')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Mật khẩu" required>
                             @error('password')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <input type="password" class="form-control" name="password_confirmation" placeholder="Nhập lại mật khẩu" required>
                        </div>

                        <button type="submit" class="btn btn-login w-100 my-3">ĐĂNG KÝ</button>
                    </form>
                    
                    <div class="text-center">
                        <span class="text-muted">Bạn đã có tài khoản? </span>
                        <a href="{{ route('login') }}">Đăng nhập ngay</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
