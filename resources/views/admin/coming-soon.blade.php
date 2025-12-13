@extends('layouts.admin')

@section('title', $title ?? 'Coming Soon')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <h1>{{ $title ?? 'Tính năng đang phát triển' }}</h1>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-hard-hat fa-4x text-warning mb-4"></i>
                <h3>Tính năng đang được phát triển</h3>
                <p class="text-muted">Chức năng này sẽ sớm được hoàn thiện trong phiên bản tiếp theo.</p>
                <a href="{{ url()->previous() }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
