@extends('layouts.master')
@section('title','Đăng nhập tài khoản')

@push('css')
<link rel="stylesheet" href="{{asset('css/auth.css')}}">
@endpush

@section("content")
<div id="breadcrumb" class="bg-light">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-light m-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Đăng nhập tài khoản</li>
            </ol>
        </nav>
    </div>
</div>

<div id="wrapper">
  <div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="login-box bg-white p-4 border">
                <h3 class="text-center mb-4">ĐĂNG NHẬP</h3>
                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    
                    {{-- Đổi từ email sang số điện thoại --}}
                    <div class="form-group mb-3">
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" placeholder="Số điện thoại" value="{{ old('phone') }}" required autofocus>
                        
                        {{-- Hiển thị lỗi validation chung hoặc lỗi sai thông tin --}}
                        @error('phone')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Mật khẩu" required>
                         @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    
                    <div class="form-group form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember">
                            Ghi nhớ đăng nhập
                        </label>
                    </div>

                    <button type="submit" class="btn btn-login w-100 mb-3">ĐĂNG NHẬP</button>
                </form>
                
                <div class="d-flex justify-content-between">
                    <a href="#">Quên mật khẩu?</a>
                    <a href="{{ route('register') }}">Đăng ký tại đây</a>
                </div>
            </div>
        </div>
    </div>
  </div>
</div>
@endsection
