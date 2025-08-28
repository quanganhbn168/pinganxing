{{-- Giả sử bạn có layout chung là 'frontend.layouts.app' --}}
@extends('layouts.master') 

@section('title', 'Kết quả tìm kiếm cho: ' . $keyword)

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-12">
            <div class="search-header mb-4">
                <h1 class="h2">Kết quả tìm kiếm</h1>
                <p class="lead">
                    Tìm thấy <strong class="text-danger">{{ $results->total() }}</strong> kết quả cho từ khóa: <strong>"{{ $keyword }}"</strong>
                </p>
            </div>

            <div class="search-results">
                @forelse($results as $result)
                    <div class="card mb-3 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <h5 class="card-title">
                                    <a href="{{ $result->url }}" class="text-decoration-none">{{ $result->title }}</a>
                                </h5>
                                @if($result->type == 'Sản phẩm')
                                    <span class="badge bg-primary text-white align-self-start">{{ $result->type }}</span>
                                @elseif($result->type == 'Bài viết')
                                    <span class="badge bg-success text-white align-self-start">{{ $result->type }}</span>
                                @else
                                    <span class="badge bg-info text-white align-self-start">{{ $result->type }}</span>
                                @endif
                            </div>
                            <p class="card-text text-muted">
                                {{-- Lấy một đoạn mô tả ngắn --}}
                                {{ Str::limit(strip_tags($result->description ?? $result->content), 150) }}
                            </p>
                            <a href="{{ $result->url }}" class="btn btn-outline-primary btn-sm">Xem chi tiết &rarr;</a>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-warning text-center">
                        <i class="fas fa-exclamation-triangle"></i> Rất tiếc, không tìm thấy kết quả nào phù hợp với từ khóa của bạn.
                    </div>
                @endforelse
            </div>

            {{-- Hiển thị link phân trang --}}
            <div class="d-flex justify-content-center mt-4">
                {{ $results->links() }}
            </div>

        </div>
    </div>
</div>
@endsection