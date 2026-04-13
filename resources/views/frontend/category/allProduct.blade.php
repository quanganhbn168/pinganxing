@extends('layouts.master')
@section('title', $career->name)

@section('content')
{{-- Header/Hero Nhỏ --}}
<div class="bg-blue-600 dark:bg-gray-800 text-white py-12 md:py-16 relative overflow-hidden">
    {{-- Pattern nền --}}
    <div class="absolute inset-0 opacity-10">
        <svg class="h-full w-full" viewBox="0 0 100 100" preserveAspectRatio="none">
            <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                <path d="M 10 0 L 0 0 0 10" fill="none" stroke="currentColor" stroke-width="0.5"/>
            </pattern>
            <rect width="100" height="100" fill="url(#grid)"/>
        </svg>
    </div>
    
    <div class="max-w-screen-xl mx-auto px-4 relative z-10">
        <h1 class="text-3xl md:text-5xl font-bold mb-4 leading-tight">
            {{ $career->name }}
        </h1>
        <nav aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="/" class="inline-flex items-center text-sm font-medium text-white/80 hover:text-white">
                        <i class="fas fa-home mr-2"></i> Trang chủ
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-white/50 mx-2 text-sm"></i>
                        <a href="{{ route('frontend.careers.index') }}" class="text-sm font-medium text-white/80 hover:text-white">
                            Tuyển dụng
                        </a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-white/50 mx-2 text-sm"></i>
                        <span class="text-sm font-medium text-white line-clamp-1">{{ $career->name }}</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>
</div>

<div class="bg-gray-50 dark:bg-gray-900 py-12 lg:py-16">
    <div class="max-w-screen-xl mx-auto px-4">
        
        {{-- Thông báo thành công --}}
        @if(session('success_apply'))
            <div class="mb-8 p-4 text-green-800 border border-green-300 rounded-2xl bg-green-50 dark:bg-gray-800 dark:text-green-400 dark:border-green-800 flex items-center shadow-sm">
                <i class="fas fa-check-circle text-2xl mr-3"></i>
                <span class="font-medium text-lg">{{ session('success_apply') }}</span>
            </div>
        @endif

        <div class="flex flex-col lg:flex-row gap-8 lg:gap-12">
            
            {{-- CỘT TRÁI: NỘI DUNG --}}
            <div class="w-full lg:w-2/3">
                
                {{-- Info Box (Mobile only) --}}
                <div class="lg:hidden bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 mb-8">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Mức lương</p>
                            <p class="font-bold text-green-600 dark:text-green-400">{{ $career->salary ?? 'Thỏa thuận' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Hạn nộp</p>
                            <p class="font-bold text-gray-900 dark:text-white">
                                {{ $career->deadline ? $career->deadline->format('d/m/Y') : 'Không giới hạn' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 md:p-10 shadow-sm border border-gray-100 dark:border-gray-700 mb-10">
                    @if($career->description)
                        <div class="mb-10">
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                                <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center text-blue-600 mr-4">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                Mô tả công việc
                            </h3>
                            <div class="prose prose-lg max-w-none prose-blue dark:prose-invert text-gray-600 dark:text-gray-300">
                                {!! $career->description !!}
                            </div>
                        </div>
                    @endif
                    
                    @if($career->requirement)
                        <div class="mb-10">
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                                <div class="w-10 h-10 rounded-xl bg-orange-100 dark:bg-orange-900/50 flex items-center justify-center text-orange-600 mr-4">
                                    <i class="fas fa-clipboard-check"></i>
                                </div>
                                Yêu cầu ứng viên
                            </h3>
                            <div class="prose prose-lg max-w-none prose-blue dark:prose-invert text-gray-600 dark:text-gray-300">
                                {!! $career->requirement !!}
                            </div>
                        </div>
                    @endif

                    @if($career->benefit)
                        <div class="mb-4">
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                                <div class="w-10 h-10 rounded-xl bg-green-100 dark:bg-green-900/50 flex items-center justify-center text-green-600 mr-4">
                                    <i class="fas fa-gift"></i>
                                </div>
                                Quyền lợi được hưởng
                            </h3>
                            <div class="prose prose-lg max-w-none prose-blue dark:prose-invert text-gray-600 dark:text-gray-300">
                                {!! $career->benefit !!}
                            </div>
                        </div>
                    @endif
                </div>

                {{-- FORM ỨNG TUYỂN --}}
                <div id="apply-form-section" class="bg-white dark:bg-gray-800 rounded-3xl overflow-hidden shadow-sm border border-gray-100 dark:border-gray-700 scroll-mt-24">
                    <div class="bg-blue-600 py-4 px-6 md:px-10">
                        <h3 class="text-xl font-bold text-white flex items-center m-0">
                            <i class="fas fa-paper-plane mr-3"></i> ỨNG TUYỂN VỊ TRÍ NÀY
                        </h3>
                    </div>
                    <div class="p-6 md:p-10">
                        <form action="{{ route('frontend.careers.apply', $career->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white tracking-wide uppercase">Họ và tên <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" required placeholder="Ví dụ: Nguyễn Văn A" value="{{ old('name') }}"
                                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white transition-colors">
                                    @error('name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white tracking-wide uppercase">Số điện thoại <span class="text-red-500">*</span></label>
                                    <input type="tel" name="phone" required placeholder="Ví dụ: 0987654321" value="{{ old('phone') }}"
                                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white transition-colors">
                                    @error('phone') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div class="mb-6">
                                <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white tracking-wide uppercase">Email</label>
                                <input type="email" name="email" placeholder="Ví dụ: email@domain.com" value="{{ old('email') }}"
                                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white transition-colors">
                            </div>

                            <div class="mb-6">
                                <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white tracking-wide uppercase">File CV (PDF, DOC, DOCX) <span class="text-red-500">*</span></label>
                                <input type="file" name="cv_file" id="cvFile" required accept=".pdf,.doc,.docx"
                                    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-xl cursor-pointer bg-gray-50 p-2.5 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Dung lượng tối đa 5MB.</p>
                                @error('cv_file') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-8">
                                <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white tracking-wide uppercase">Lời nhắn</label>
                                <textarea name="message" rows="4" placeholder="Giới thiệu ngắn gọn về bản thân hoặc kinh nghiệm của bạn..."
                                          class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white transition-colors">{{ old('message') }}</textarea>
                            </div>

                            <button type="submit" 
                                    class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-bold rounded-xl text-lg px-5 py-4 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 transition-colors shadow-lg shadow-blue-500/30">
                                GỬI HỒ SƠ ỨNG TUYỂN
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- CỘT PHẢI: SIDEBAR THÔNG TIN --}}
            <div class="w-full lg:w-1/3">
                <div class="sticky top-24 space-y-6">
                    
                    {{-- Thông tin chung --}}
                    <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 lg:p-8 shadow-sm border border-gray-100 dark:border-gray-700">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 uppercase border-b border-gray-100 dark:border-gray-700 pb-4">
                            Thông tin chung
                        </h3>
                        
                        <ul class="space-y-5">
                            <li class="flex items-start">
                                <div class="w-10 h-10 rounded-full bg-green-50 dark:bg-green-900/30 flex items-center justify-center text-green-600 mr-4 flex-shrink-0">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-0.5">Mức lương</p>
                                    <p class="font-bold text-green-600 dark:text-green-400 text-lg">
                                        {{ $career->salary ?? 'Thỏa thuận' }}
                                    </p>
                                </div>
                            </li>
                            
                            <li class="flex items-start">
                                <div class="w-10 h-10 rounded-full bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 mr-4 flex-shrink-0">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-0.5">Số lượng tuyển</p>
                                    <p class="font-medium text-gray-900 dark:text-white">
                                        {{ $career->quantity ? $career->quantity . ' người' : 'Không giới hạn' }}
                                    </p>
                                </div>
                            </li>
                            
                            <li class="flex items-start">
                                <div class="w-10 h-10 rounded-full bg-purple-50 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 mr-4 flex-shrink-0">
                                    <i class="fas fa-briefcase"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-0.5">Hình thức làm việc</p>
                                    <p class="font-medium text-gray-900 dark:text-white">
                                        {{ $career->type ?? 'Toàn thời gian' }}
                                    </p>
                                </div>
                            </li>

                            <li class="flex items-start">
                                <div class="w-10 h-10 rounded-full bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 mr-4 flex-shrink-0">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-0.5">Yêu cầu bằng cấp</p>
                                    <p class="font-medium text-gray-900 dark:text-white">
                                        {{ $career->education ?? 'Không yêu cầu' }}
                                    </p>
                                </div>
                            </li>

                            <li class="flex items-start">
                                <div class="w-10 h-10 rounded-full bg-orange-50 dark:bg-orange-900/30 flex items-center justify-center text-orange-600 mr-4 flex-shrink-0">
                                    <i class="far fa-clock"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-0.5">Hạn nộp hồ sơ</p>
                                    <p class="font-medium {{ $career->deadline && $career->deadline->isPast() ? 'text-red-600' : 'text-gray-900 dark:text-white' }}">
                                        {{ $career->deadline ? $career->deadline->format('d/m/Y') : 'Không thời hạn' }}
                                    </p>
                                </div>
                            </li>
                        </ul>

                        <a href="#apply-form-section" 
                           class="mt-8 flex items-center justify-center w-full text-blue-600 bg-blue-50 hover:bg-blue-600 hover:text-white border border-blue-200 dark:border-gray-700 focus:ring-4 focus:ring-blue-100 font-bold rounded-xl text-base px-5 py-3.5 transition-all">
                            ỨNG TUYỂN NGAY
                        </a>
                    </div>

                    {{-- Box Hỗ trợ --}}
                    <div class="bg-blue-600 dark:bg-gray-800 rounded-3xl p-6 lg:p-8 shadow-sm text-white">
                        <h4 class="text-lg font-bold mb-4">
                            Cần hỗ trợ?
                        </h4>
                        <p class="text-white/80 text-sm mb-6">
                            Vui lòng liên hệ bộ phận Tuyển dụng của chúng tôi qua:
                        </p>
                        <div class="space-y-3">
                            <a href="tel:{{ $setting->phone }}" class="flex items-center text-white hover:text-blue-100 transition-colors bg-white/10 p-3 rounded-xl">
                                <i class="fas fa-phone-alt w-6 text-center"></i> 
                                <span class="font-bold ml-2">{{ $setting->phone }}</span>
                            </a>
                            <a href="mailto:{{ $setting->email }}" class="flex items-center text-white hover:text-blue-100 transition-colors bg-white/10 p-3 rounded-xl">
                                <i class="fas fa-envelope w-6 text-center"></i> 
                                <span class="font-medium ml-2 break-all">{{ $setting->email }}</span>
                            </a>
                        </div>
                    </div>
                    
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

