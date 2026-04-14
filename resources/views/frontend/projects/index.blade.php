@extends('layouts.master')
@section('title', $pageTitle)
@section('meta_description', isset($setting->projects_description) ? \Illuminate\Support\Str::limit(strip_tags($setting->projects_description), 155) : '')
@section('meta_image', $bannerUrl ?? '')

@section('content')

<x-frontend.page-hero 
    :image="$bannerUrl" 
    :title="$pageTitle" 
    :breadcrumb="$breadcrumbs" 
/>

<section class="py-16 bg-white dark:bg-gray-900">
    <div class="max-w-screen-xl mx-auto px-4">
        <div class="text-center mb-16">
            @if(isset($setting->projects_description) && !empty($setting->projects_description))
                <p class="text-lg text-gray-600 dark:text-gray-400 font-medium">{{ $setting->projects_description }}</p>
            @endif
        </div>

        {{-- Khối dự án tiêu biểu (Hero Project) --}}
        <div class="mb-16">
            @if(isset($projectFeature) && $projectFeature)
                <a href="{{ $projectFeature->slug_url ?? '#' }}" 
                   class="group block bg-white dark:bg-gray-800 rounded-sm shadow-md border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-xl transition-all duration-300 {{ empty($projectFeature->slug_url) ? 'cursor-default pointer-events-none' : '' }}"
                   @if(empty($projectFeature->slug_url)) onclick="return false;" @endif>
                    <div class="flex flex-col lg:flex-row">
                        {{-- Cột hình ảnh --}}
                        <div class="w-full lg:w-3/5 relative aspect-video lg:aspect-auto">
                            <img src="{{ $projectFeature->image ? $projectFeature->image->url : asset('images/setting/no-image.png') }}" alt="{{ $projectFeature->name ?? 'Dự án' }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                            <div class="absolute inset-0 bg-gradient-to-t from-gray-900/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </div>

                        {{-- Cột thông tin --}}
                        <div class="w-full lg:w-2/5 p-8 lg:p-10 flex flex-col justify-center">
                            <h3 class="text-2xl font-bold text-brand-700 dark:text-brand-400 mb-6 group-hover:text-brand-800 transition-colors line-clamp-2">
                                {{ $projectFeature->name ?? 'Hiện đang cập nhật' }}
                            </h3>
                            
                            <div class="space-y-4">
                                <div class="flex flex-col sm:flex-row sm:items-start border-b border-gray-100 dark:border-gray-700 pb-3">
                                    <span class="text-gray-500 dark:text-gray-400 font-medium w-36 mb-1 sm:mb-0"><i class="fas fa-building mr-2"></i> Chủ đầu tư:</span>
                                    <strong class="text-gray-900 dark:text-white flex-1">{{ $projectFeature->investor ?? 'Hiện đang cập nhật' }}</strong>
                                </div>
                                
                                <div class="flex flex-col sm:flex-row sm:items-start border-b border-gray-100 dark:border-gray-700 pb-3">
                                    <span class="text-gray-500 dark:text-gray-400 font-medium w-36 mb-1 sm:mb-0"><i class="fas fa-map-marker-alt mr-2"></i> Địa chỉ:</span>
                                    <strong class="text-gray-900 dark:text-white flex-1 line-clamp-2">{{ $projectFeature->address ?? 'Hiện đang cập nhật' }}</strong>
                                </div>
                                
                                <div class="flex flex-col sm:flex-row sm:items-start border-b border-gray-100 dark:border-gray-700 pb-3">
                                    <span class="text-gray-500 dark:text-gray-400 font-medium w-36 mb-1 sm:mb-0"><i class="fas fa-calendar-alt mr-2"></i> Năm thực hiện:</span>
                                    <strong class="text-gray-900 dark:text-white flex-1">{{ $projectFeature->year ?? 'Hiện đang cập nhật' }}</strong>
                                </div>
                                
                                <div class="flex flex-col sm:flex-row sm:items-start">
                                    <span class="text-gray-500 dark:text-gray-400 font-medium w-36 mb-1 sm:mb-0"><i class="fas fa-dollar-sign mr-2"></i> Gói thầu:</span>
                                    <strong class="text-gray-900 dark:text-white flex-1">
                                        @if(isset($projectFeature->value) && is_numeric($projectFeature->value))
                                            {{ number_format($projectFeature->value, 0, ',', '.') }} VNĐ
                                        @else
                                            Hiện đang cập nhật
                                        @endif
                                    </strong>
                                </div>
                            </div>

                            <div class="mt-8">
                                <span class="inline-flex items-center text-sm font-bold text-brand-600 dark:text-brand-400 group-hover:underline">
                                    Xem chi tiết dự án <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            @endif
        </div>

        <div class="text-center mb-10">
            <h2 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight">
                Các dự án khác
            </h2>
            <div class="w-16 h-1 bg-brand-600 mx-auto mt-4"></div>
        </div>

        @if(isset($projects) && $projects->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                @foreach($projects as $project)
                    <x-frontend.card 
                        :href="$project->slug_url"
                        :image="$project->image ? $project->image->url : null"
                        :title="$project->name"
                        :description="$project->investor ? 'Chủ đầu tư: ' . $project->investor : ''"
                    />
                @endforeach
            </div>
            
            @if(method_exists($projects, 'links'))
                <div class="mt-10 flex justify-center">
                    {{ $projects->links() }}
                </div>
            @endif
        @else
            <div class="bg-white dark:bg-gray-800 rounded-sm p-12 text-center border border-dashed border-gray-200 dark:border-gray-700 shadow-sm">
                <i class="fas fa-folder-open text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Nội dung đang cập nhật</h3>
                <p class="text-gray-500 dark:text-gray-400">Danh sách dự án đang được biên soạn.</p>
            </div>
        @endif
    </div>
</section>

<x-frontend.page-cta 
    :title="$pageSettings->projects_cta_title" 
    :description="$pageSettings->projects_cta_description" 
    :link="$pageSettings->projects_cta_link" 
/>

@endsection
