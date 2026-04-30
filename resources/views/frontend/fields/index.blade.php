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

        @if(isset($field_categories) && $field_categories->isNotEmpty())
            <div class="space-y-20 lg:space-y-32">
                @foreach($field_categories as $index => $field_category)
                    <div class="flex flex-col lg:flex-row {{ $index % 2 !== 0 ? 'lg:flex-row-reverse' : '' }} gap-10 lg:gap-16 items-center">
                        {{-- Cột Hình Ảnh --}}
                        <div class="w-full lg:w-1/2 relative group">
                            <div class="absolute -inset-4 bg-brand-600/10 dark:bg-brand-600/20 transform {{ $index % 2 === 0 ? '-rotate-2' : 'rotate-2' }} rounded-sm group-hover:rotate-0 transition-transform duration-500"></div>
                            <div class="relative rounded-sm overflow-hidden shadow-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 aspect-[4/3]">
                                <img src="{{ $field_category->image ? $field_category->image->url : asset('images/setting/no-image.png') }}" alt="{{ $field_category->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                                <div class="absolute inset-0 bg-gradient-to-t from-gray-900/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            </div>
                        </div>

                        {{-- Cột Nội Dung --}}
                        <div class="w-full lg:w-1/2 flex flex-col justify-center">
                            <div class="inline-flex items-center justify-center w-14 h-14 bg-brand-50 text-brand-600 border border-brand-100 dark:border-gray-700 dark:bg-gray-800 dark:text-brand-400 rounded-full mb-6 text-2xl shadow-sm">
                                <i class="fas fa-industry"></i>
                            </div>
                            <h3 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white mb-6 uppercase tracking-tight leading-tight">
                                {{ $field_category->name }}
                            </h3>
                            <div class="text-lg text-gray-600 dark:text-gray-300 font-medium leading-relaxed mb-8 pl-5 border-l-4 border-brand-500">
                                {{ $field_category->description ?? 'Hệ sinh thái giải pháp số được tùy biến chuyên sâu cho lĩnh vực '.$field_category->name.', giúp tối ưu hóa nghiệp vụ đặc thù và nâng tầm dữ liệu quản trị doanh nghiệp.' }}
                            </div>
                            
                            <div class="flex items-center gap-4 mt-2">
                                <a href="{{ $field_category->slug_url }}" class="inline-flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-700 text-white font-bold uppercase tracking-wider text-sm px-8 py-4 rounded-sm transition-all shadow-lg hover:shadow-brand-500/40 hover:-translate-y-1">
                                    Tìm hiểu giải pháp <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
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
