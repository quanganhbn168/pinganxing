@extends('layouts.master')
@section('title', $pageTitle ?? 'Dịch vụ')

@section('content')

<x-frontend.page-hero 
    :title="$pageTitle ?? 'Dịch vụ'" 
    subtitle="Giải pháp công nghệ toàn diện cho doanh nghiệp"
    :breadcrumb="$breadcrumbItems ?? [['label' => 'Dịch vụ']]" 
/>

<section class="py-16 bg-white dark:bg-gray-900">
    <div class="max-w-screen-xl mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 lg:gap-12">
            
            <div class="lg:col-span-3">
                @if (isset($category))
                    {{-- Trang danh mục cụ thể --}}
                    <x-frontend.section-heading :title="$category->name" />
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @forelse($services as $service)
                            <x-frontend.card 
                                :href="$service->slug_url"
                                :image="$service->image ? $service->image->path : null"
                                :title="$service->name"
                                :description="$service->description"
                            >
                                <span class="inline-flex items-center text-sm text-blue-600 font-medium mt-2">
                                    Xem chi tiết <i class="fas fa-arrow-right ml-2 text-xs"></i>
                                </span>
                            </x-frontend.card>
                        @empty
                            <div class="col-span-full text-center py-16 bg-gray-50 dark:bg-gray-800 rounded-2xl">
                                <i class="fas fa-box-open text-4xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500 dark:text-gray-400">Chưa có dịch vụ nào trong danh mục này.</p>
                            </div>
                        @endforelse
                    </div>

                    @if(method_exists($services, 'links') && $services->hasPages())
                        <div class="mt-10 flex justify-center">
                            {{ $services->links('vendor.pagination.tailwind') }}
                        </div>
                    @endif

                @else
                    {{-- Trang dịch vụ tổng --}}
                    <x-frontend.section-heading title="Dịch vụ của chúng tôi" subtitle="Đồng hành cùng doanh nghiệp trên hành trình chuyển đổi số" />

                    @foreach($serviceCategories as $cat)
                        <div class="mt-10 first:mt-0">
                            <h3 class="text-xl font-bold text-brand-900 mb-6 flex items-center border-l-4 border-brand-600 pl-3">
                                <span class="w-8 h-8 rounded-sm bg-brand-50 flex items-center justify-center mr-3 text-brand-600">
                                    <i class="fas fa-layer-group text-sm"></i>
                                </span>
                                <a href="{{ $cat->slug_url }}" class="hover:text-brand-600 transition-colors uppercase tracking-tight">{{ $cat->name }}</a>
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @forelse($cat->services as $service)
                                    <x-frontend.card 
                                        :href="$service->slug_url"
                                        :image="$service->image ? $service->image->path : null"
                                        :title="$service->name"
                                        :description="$service->description"
                                    >
                                        <span class="inline-flex items-center text-sm text-blue-600 font-medium mt-2">
                                            Xem chi tiết <i class="fas fa-arrow-right ml-2 text-xs"></i>
                                        </span>
                                    </x-frontend.card>
                                @empty
                                    <div class="col-span-full bg-gray-50 dark:bg-gray-800 rounded-xl p-8 text-center">
                                        <p class="text-gray-500 dark:text-gray-400">Chưa có dịch vụ nào.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @endforeach

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
