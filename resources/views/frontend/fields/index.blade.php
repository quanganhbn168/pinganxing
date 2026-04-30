@extends('layouts.master')
@section('title', $pageTitle)
@section('content')



<x-frontend.leaderboard
    :image="$bannerUrl"
    :title="$pageTitle"
    :subline="$pageSettings->fields_leaderboard_subline"
    :description="$pageSettings->fields_leaderboard_description ?: ($pageSubtitle ?? null)"
    :breadcrumb="$breadcrumbs"
    :actions="$pageSettings->fields_leaderboard_actions"
    :stats="$pageSettings->fields_leaderboard_stats"
/>

<div class="bg-gray-50 dark:bg-gray-900 py-16 md:py-24">
    <div class="max-w-screen-xl mx-auto px-4">

        <div class="text-center max-w-3xl mx-auto mb-20">
            @if(!empty($pageSettings->fields_description) || !empty($setting->fields_description))
                <h2 class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white mb-6 uppercase tracking-wider">
                    Tổng quan lĩnh vực
                </h2>
                <div class="w-16 h-1 bg-brand-600 mx-auto mt-6 mb-6"></div>
                <p class="text-lg text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                    {{ $pageSettings->fields_description ?? $setting->fields_description }}
                </p>
            @endif
        </div>
        <h2 class="text-center">Chúng tôi đồng hành cùng doanh nghiệp trên mọi lĩnh vực</h2>
        @if(isset($field_categories) && $field_categories->isNotEmpty())
            <div class="space-y-20 lg:space-y-32">
                @foreach($field_categories as $index => $field_category)
                    <div class="fields-grid" data-aos="fade-up" data-aos-delay="120">
            @php
                $fieldImage = $field_category->image_id ? ($field_category->image?->url ?? null) : null;
                $fieldImage = $fieldImage ?: 'https://placehold.co/720x720/eaf4fb/0e4a86?text=Industry';
            @endphp
            <a href="{{ $field->slug_url }}" class="field-card group" data-aos="fade-up" data-aos-delay="{{ min($loop->index * 80, 320) }}">
                <span class="field-card-media">
                    <img src="{{ $fieldImage }}" alt="{{ $field->name }}" loading="lazy" decoding="async">
                </span>
                <span class="field-card-body">
                    <span class="field-card-title">{{ $field->name }}</span>
                    @if(!empty($field->description) || !empty($field->content))
                        <span class="field-card-text">{{ Str::limit(strip_tags($field->description ?? $field->content), 110) }}</span>
                    @endif
                    <span class="field-card-link">Tìm hiểu thêm <i class="fas fa-arrow-right"></i></span>
                </span>
            </a>
        </div>
                @endforeach
            </div>

            {{-- Khối Kêu gọi hành động (CTA) --}}
            <x-frontend.page-cta
                :title="$pageSettings->fields_cta_title"
                :description="$pageSettings->fields_cta_description"
                :link="$pageSettings->fields_cta_link"
            />
        @else
            <div class="bg-white dark:bg-gray-800 rounded-sm p-16 text-center border border-dashed border-gray-200 dark:border-gray-700 shadow-sm max-w-4xl mx-auto">
                <div class="w-24 h-24 bg-gray-50 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-folder-open text-5xl text-gray-300 dark:text-gray-500"></i>
                </div>
                <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-3">Nội dung đang cập nhật</h3>
                <p class="text-gray-500 dark:text-gray-400 text-lg">Hệ thống đang được cấu hình và làm mới tài liệu kỹ thuật cho các phân hệ lĩnh vực.</p>
            </div>
        @endif

    </div>
</div>
@endsection
