@extends('layouts.master')
@section('title', $category->name)
@push('css')
<link rel="stylesheet" href="{{asset('css/product.css')}}">
@endpush
@section('content')
<section class="section py-4">
    <div class="container">
        {{-- Breadcrumb --}}
        <x-frontend.breadcrumb :items="[
            ['label' => 'Bài viết', 'url' => route('frontend.slug.handle', $category->slug)],
            ['label' => $category->name]
        ]" />

        <div class="row">
            {{-- Cột trái: danh sách bài viết --}}
            <div class="col-md-9">
                <h1 class="mb-4">{{ $category->name }}</h1>
                @foreach($posts as $post)
                <div class="mb-4 border-bottom pb-3 row">
                    <div class="col-md-4">
                        <a href="{{ route('frontend.slug.handle', $post->slug) }}">
                            <img src="{{ asset($post->image ?? 'images/setting/no-image.png') }}" alt="{{ $post->title }}" class="img-fluid w-100" style="object-fit: cover; max-height: 180px;">
                        </a>
                    </div>
                    <div class="col-md-8">
                        <a href="{{ route('frontend.slug.handle', $post->slug) }}" class="d-block mb-2">
                            <h4 class="mb-1">{{ $post->title }}</h4>
                        </a>
                        <div class="small text-muted mb-2">
                            {{ $post->created_at->format('d/m/Y') }}
                        </div>
                        <p>{{ Str::limit(strip_tags($post->description), 150) }}</p>
                    </div>
                </div>
                @endforeach

                <div class="mt-4">
                    {{ $posts->links() }}
                </div>

            </div>

            {{-- Cột phải: Sidebar --}}
            @include('partials.frontend.aside')
        </div>
    </div>
</section>
@endsection
