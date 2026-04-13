@extends('layouts.master')
@section('title', 'Tuyển dụng - ' . ($setting->site_name ?? config('app.name')))

@section('content')

<x-frontend.page-hero 
    title="Cơ hội nghề nghiệp" 
    subtitle="Gia nhập đội ngũ chuyên gia công nghệ hàng đầu"
    :breadcrumb="[['label' => 'Tuyển dụng']]" 
/>

<section class="py-16 bg-white dark:bg-gray-900">
    <div class="max-w-screen-xl mx-auto px-4">
        
        <x-frontend.section-heading title="Vị trí đang tuyển" center />

        @if($careers->isEmpty())
            <div class="text-center py-20 bg-gray-50 dark:bg-gray-800 rounded-2xl">
                <i class="fas fa-briefcase text-5xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-bold text-gray-500 mb-2">Hiện chưa có vị trí nào</h3>
                <p class="text-gray-400">Vui lòng quay lại sau hoặc gửi CV cho chúng tôi.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">
                @foreach($careers as $career)
                    <div class="group bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                        {{-- Header --}}
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-3">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $career->status ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $career->status ? 'Đang tuyển' : 'Đã đóng' }}
                                </span>
                                <span class="text-xs text-gray-400">
                                    <i class="far fa-clock mr-1"></i>
                                    @if($career->deadline)
                                        Hạn: {{ $career->deadline->format('d/m/Y') }}
                                    @else
                                        Không giới hạn
                                    @endif
                                </span>
                            </div>

                            <h3 class="text-lg font-bold text-gray-900 dark:text-white group-hover:text-blue-600 transition-colors line-clamp-2">
                                {{ $career->name }}
                            </h3>

                            {{-- Meta --}}
                            <div class="mt-4 space-y-2 text-sm text-gray-500 dark:text-gray-400">
                                @if($career->salary)
                                    <div class="flex items-center">
                                        <i class="fas fa-dollar-sign w-5 text-blue-500"></i>
                                        <span class="font-medium text-green-600 dark:text-green-400">{{ $career->salary }}</span>
                                    </div>
                                @endif
                                @if($career->location)
                                    <div class="flex items-center">
                                        <i class="fas fa-map-marker-alt w-5 text-blue-500"></i>
                                        <span>{{ $career->location }}</span>
                                    </div>
                                @endif
                                @if($career->type)
                                    <div class="flex items-center">
                                        <i class="fas fa-briefcase w-5 text-blue-500"></i>
                                        <span>{{ $career->type }}</span>
                                    </div>
                                @endif
                                @if($career->quantity)
                                    <div class="flex items-center">
                                        <i class="fas fa-users w-5 text-blue-500"></i>
                                        <span>Số lượng: {{ $career->quantity }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-750 border-t border-gray-100 dark:border-gray-700">
                            <a href="{{ $career->slug_url }}" 
                               class="flex items-center justify-center w-full px-4 py-2.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-colors">
                                Xem chi tiết & Ứng tuyển
                                <i class="fas fa-arrow-right ml-2 text-xs"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($careers->hasPages())
                <div class="mt-10 flex justify-center">
                    {{ $careers->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        @endif
    </div>
</section>

@endsection
