@extends('layouts.master')
@section('title', $pageTitle ?? 'Tin tức')

@section('content')

<x-frontend.page-hero 
    :title="$pageTitle ?? 'Tin tức'" 
    subtitle="Cập nhật thông tin mới nhất về công nghệ và doanh nghiệp"
    :breadcrumb="$breadcrumbItems ?? [['label' => 'Tin tức']]" 
/>

<section class="py-16 bg-white dark:bg-gray-900">
    <div class="max-w-screen-xl mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 lg:gap-12">
            
            <div class="lg:col-span-3">
                @if(isset($category))
                    <x-frontend.section-heading :title="$category->name" />
                @else
                    <x-frontend.section-heading title="Bài viết mới nhất" />
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @forelse($posts as $post)
                        <x-frontend.card 
                            :href="$post->slug_url"
                            :image="$post->image ? $post->image->path : null"
                            :title="$post->title"
                            :description="$post->description"
                            :date="$post->created_at->format('d/m/Y')"
                            :badge="$post->category->name ?? null"
                        />
                    @empty
                        <div class="col-span-full text-center py-16 bg-gray-50 dark:bg-gray-800 rounded-2xl">
                            <i class="far fa-newspaper text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500">Chưa có bài viết nào.</p>
                        </div>
                    @endforelse
                </div>

                @if($posts->hasPages())
                    <div class="mt-10 flex justify-center">
                        {{ $posts->links('vendor.pagination.tailwind') }}
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="lg:col-span-1">
                <div class="sticky top-24">
                    <x-frontend.aside />
                </div>
            </div>

        </div>
    </div>
</section>

@endsection
