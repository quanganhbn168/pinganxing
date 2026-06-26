@extends('layouts.master')
@section('title', 'Danh sách Tour')
@section('content')
    <div class="bg-slate-50 min-h-screen pt-24 pb-12">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">
            <h1 class="text-4xl font-serif font-bold text-slate-900 mb-8" style="font-family: 'Playfair Display', serif;">
                Khám phá các Tour Du Lịch</h1>

            @if(isset($categories) && $categories->count() > 0)
                <div class="mb-10 flex flex-wrap gap-3">
                    <a href="{{ route('frontend.tours.index') }}"
                        class="px-5 py-2 rounded-full bg-primary text-white text-sm font-bold shadow-md">
                        Tất cả
                    </a>
                    @foreach($categories as $cat)
                        <a href="{{ route('frontend.tours.category', $cat->slug) }}"
                            class="px-5 py-2 rounded-full bg-white border border-slate-200 text-slate-600 hover:text-primary hover:border-primary text-sm font-bold transition-all">
                            {{ $cat->name }}
                        </a>
                    @endforeach
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($tours as $product)
                    @include('partials.frontend.tour-card', ['product' => $product])
                @endforeach
            </div>

            <div class="mt-12">
                {{ $tours->links() }}
            </div>
        </div>
    </div>
@endsection
