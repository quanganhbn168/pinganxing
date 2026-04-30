@extends('layouts.master')
@section('title', $pageTitle)
@section('content')



<x-frontend.leaderboard
    :image="$bannerUrl"
    :title="$pageTitle"
    subline="Lĩnh vực hoạt động"
    :description="$current_category->description"
    :breadcrumb="$breadcrumbs"
/>

<div class="bg-gray-50 dark:bg-gray-900 py-16 md:py-24">
    <div class="max-w-screen-xl mx-auto px-4">
        
        <div class="text-center max-w-3xl mx-auto mb-16">
            @if(!empty($current_category->description))
                <h2 class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white mb-6 uppercase tracking-wider">
                    Tổng quan danh mục
                </h2>
                <div class="w-16 h-1 bg-brand-600 mx-auto mt-6 mb-6"></div>
                <p class="text-lg text-gray-600 dark:text-gray-400 font-medium">{{ $current_category->description }}</p>
            @endif
        </div>

        @if(isset($field_categories) && $field_categories->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($field_categories as $field_category)
                    <x-frontend.card 
                        :href="$field_category->slug_url"
                        :image="$field_category->image ? $field_category->image->url : null"
                        :title="$field_category->name"
                        :description="$field_category->description"
                    />
                @endforeach
            </div>
        @else
            <div class="bg-white dark:bg-gray-800 rounded-sm p-12 text-center border border-dashed border-gray-200 dark:border-gray-700 shadow-sm">
                <i class="fas fa-folder-open text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Nội dung đang cập nhật</h3>
                <p class="text-gray-500 dark:text-gray-400">Danh mục này hiện chưa có danh mục con.</p>
            </div>
        @endif

    </div>
</div>
@endsection
