@extends('layouts.master')
@section('title', 'Giới thiệu')
@section('meta_image',$setting->share_image)
@section('content')
<section class="section py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h1 class="section-title text-uppercase">Về {{$setting->name}}</h1>
        </div>
        <div class="content">
            <div class="row">
                <div class="col-12 col-md-9">
                    {!!$intro->content!!}
                    <h2 class="contact">Liên hệ ngay cho {{$setting->name}}</h2>
                    {{-- <ul>
                        <li><strong>Địa điểm: </strong>{{$setting->address}}</li>
                        <li><strong>Email: </strong> <a href="mailto:{{$setting->email}}">{{$setting->email}}</a></li>
                        <li><strong>Số điện thoại: </strong> <a href="tel:{{$setting->phone}}">{{$setting->phone}}</a></li>
                        <li><strong>Website: </strong><a href="/">https://maynenkhi-saman.vn</a></li>
                    </ul> --}}
                </div>
                <div class="col-12 col-md-3">
                    @include('partials.frontend.contact_register')
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@push('js')

@endpush