@extends('layouts.master')
@section('title', 'Liên hệ')

@section('content')
{{-- Hero Banner --}}
<div class="relative w-full h-[25vh] md:h-[35vh] overflow-hidden bg-brand-900 border-b border-brand-800">
    <img src="{{ asset($setting->banner ?? 'images/setting/cover01.jpg') }}" alt="Liên hệ" class="w-full h-full object-cover mix-blend-overlay opacity-60">
    <div class="absolute inset-0 flex flex-col items-center justify-center">
        <h1 class="text-3xl md:text-5xl font-black text-white uppercase tracking-wider mb-4">Liên hệ</h1>
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="/" class="inline-flex items-center text-sm font-medium text-brand-200 hover:text-white transition-colors">
                        <i class="fas fa-home mr-2"></i> Trang chủ
                    </a>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-brand-400 mx-2 text-sm"></i>
                        <span class="text-sm font-medium text-white">Liên hệ</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>
</div>

<div class="bg-gray-50 py-16 md:py-24">
    <div class="max-w-screen-xl mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">
            
            {{-- Thông tin liên hệ & Bản đồ --}}
            <div class="lg:col-span-5 flex flex-col gap-8">
                <div class="bg-white border-t-4 border-t-brand-600 rounded-sm p-8 md:p-10 shadow-sm border border-gray-100 flex-1 flex flex-col hover:shadow-lg transition-shadow">
                    <h2 class="text-2xl font-black text-gray-900 mb-6 uppercase tracking-tight">
                        THÔNG TIN LIÊN HỆ
                    </h2>
                    <p class="text-gray-600 mb-8 leading-relaxed font-sans text-sm">
                        Kênh tiếp nhận thông tin từ Quý khách hàng/Đối tác. Đội ngũ <strong>{{ $setting->site_name }}</strong> cam kết phản hồi và hỗ trợ chậm nhất trong vòng 24 giờ làm việc.
                    </p>

                    <ul class="space-y-6 flex-1 text-sm font-sans">
                        <li class="flex items-start">
                            <div class="w-10 h-10 bg-brand-50 rounded-sm flex items-center justify-center text-brand-600 mr-4 flex-shrink-0 mt-1">
                                <i class="fas fa-map-marker-alt text-lg"></i>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 mb-1 uppercase tracking-wider text-xs">Trụ sở chính</p>
                                <p class="text-gray-600 leading-relaxed font-sans">
                                    {{ $setting->address }}
                                </p>
                            </div>
                        </li>
                        
                        @foreach($branches as $branch)
                        <li class="flex items-start">
                            <div class="w-10 h-10 bg-gray-50 border border-gray-100 rounded-sm flex items-center justify-center text-gray-500 mr-4 flex-shrink-0 mt-1">
                                <i class="fas fa-building text-sm"></i>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 mb-1 uppercase tracking-wider text-xs">{{ $branch->name }}</p>
                                <p class="text-gray-600 leading-relaxed font-sans">
                                    {{ $branch->address }}
                                </p>
                            </div>
                        </li>
                        @endforeach

                        <li class="flex items-center">
                            <div class="w-10 h-10 bg-brand-50 rounded-sm flex items-center justify-center text-brand-600 mr-4 flex-shrink-0">
                                <i class="fas fa-phone-alt text-lg"></i>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 mb-0 uppercase tracking-wider text-xs">Hotline</p>
                                <a href="tel:{{ preg_replace('/\s+/', '', $setting->phone) }}" class="text-brand-600 hover:text-accent-500 font-bold text-lg transition-colors font-sans block mt-1">
                                    {{ $setting->phone }}
                                </a>
                            </div>
                        </li>

                        <li class="flex items-center">
                            <div class="w-10 h-10 bg-brand-50 rounded-sm flex items-center justify-center text-brand-600 mr-4 flex-shrink-0">
                                <i class="fas fa-envelope text-lg"></i>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 mb-0 uppercase tracking-wider text-xs">Email</p>
                                <a href="mailto:{{ trim($setting->email) }}" class="text-brand-600 hover:text-accent-500 font-medium transition-colors break-all font-sans block mt-1">
                                    {{ $setting->email }}
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>

                {{-- Bản đồ --}}
                @if($setting->map)
                <div class="bg-white rounded-sm shadow-sm border border-gray-200 h-[300px] w-full relative group overflow-hidden">
                    <style>.map-container iframe { width: 100% !important; height: 100% !important; border: 0; filter: grayscale(20%) contrast(1.1); transition: filter 0.3s ease; } .group:hover .map-container iframe { filter: none; }</style>
                    <div class="map-container w-full h-full">
                        {!! $setting->map !!}
                    </div>
                </div>
                @endif
            </div>

            {{-- Form Liên hệ --}}
            <div class="lg:col-span-7">
                <div class="bg-white border-t-4 border-t-brand-600 rounded-sm p-8 md:p-12 shadow-sm border border-gray-100 h-full flex flex-col hover:shadow-lg transition-shadow">
                    
                    <div class="mb-10 lg:pl-4">
                        <h2 class="text-2xl md:text-3xl lg:text-4xl font-black text-gray-900 mb-4 uppercase tracking-tight relative pb-4">
                            Gửi yêu cầu <span class="text-brand-600">tư vấn</span>
                            <div class="absolute bottom-0 left-0 w-16 h-1 bg-accent-500 rounded"></div>
                        </h2>
                        <p class="text-gray-500 font-sans text-sm max-w-lg mt-6">
                            Đội ngũ chuyên gia của chúng tôi luôn sẵn lòng tư vấn giải pháp chuyển đổi số toàn diện và tối ưu nhất cho doanh nghiệp của bạn.
                        </p>
                    </div>

                    <form id="contact-form" action="{{ route('contact.store') }}" method="POST" class="flex-1 flex flex-col font-sans" x-data="contactForm" @submit.prevent="submitForm">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="name" class="block mb-2 text-xs font-bold text-gray-700 uppercase tracking-wider">Họ và tên đại diện <span class="text-accent-500">*</span></label>
                                <input type="text" id="name" name="name" x-model="name" :class="errors.name ? 'border-accent-500 bg-red-50 focus:border-accent-500 focus:ring-accent-500' : 'border-gray-200 bg-gray-50 focus:border-brand-500 focus:ring-brand-500 hover:border-gray-300'" class="border text-gray-900 text-sm rounded-sm block w-full p-4 transition-colors">
                                <p x-show="errors.name" x-text="errors.name" class="mt-1 text-xs font-bold text-accent-500 tracking-wide block" x-transition></p>
                            </div>
                            <div>
                                <label for="phone" class="block mb-2 text-xs font-bold text-gray-700 uppercase tracking-wider">Điện thoại liên hệ <span class="text-accent-500">*</span></label>
                                <input type="tel" id="phone" name="phone" x-model="phone" :class="errors.phone ? 'border-accent-500 bg-red-50 focus:border-accent-500 focus:ring-accent-500' : 'border-gray-200 bg-gray-50 focus:border-brand-500 focus:ring-brand-500 hover:border-gray-300'" class="border text-gray-900 text-sm rounded-sm block w-full p-4 transition-colors">
                                <p x-show="errors.phone" x-text="errors.phone" class="mt-1 text-xs font-bold text-accent-500 tracking-wide block" x-transition></p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="email" class="block mb-2 text-xs font-bold text-gray-700 uppercase tracking-wider">Email (Tuỳ chọn)</label>
                                <input type="email" id="email" name="email" x-model="email" :class="errors.email ? 'border-accent-500 bg-red-50 focus:border-accent-500 focus:ring-accent-500' : 'border-gray-200 bg-gray-50 focus:border-brand-500 focus:ring-brand-500 hover:border-gray-300'" class="border text-gray-900 text-sm rounded-sm block w-full p-4 transition-colors">
                                <p x-show="errors.email" x-text="errors.email" class="mt-1 text-xs font-bold text-accent-500 tracking-wide block" x-transition></p>
                            </div>
                            <div>
                                <label for="address" class="block mb-2 text-xs font-bold text-gray-700 uppercase tracking-wider">Tên Doanh nghiệp / Cửa hàng</label>
                                <input type="text" id="address" name="address" 
                                       class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-sm focus:ring-brand-500 focus:border-brand-500 block w-full p-4 transition-colors hover:border-gray-300">
                            </div>
                        </div>

                        <div class="mb-10 flex-1 relative">
                            <label for="message" class="block mb-2 text-xs font-bold text-gray-700 uppercase tracking-wider">Nội dung cần hỗ trợ <span class="text-accent-500">*</span></label>
                            <textarea id="message" name="message" rows="5" x-model="message" :class="errors.message ? 'border-accent-500 bg-red-50 focus:border-accent-500 focus:ring-accent-500' : 'border-gray-200 bg-gray-50 focus:border-brand-500 focus:ring-brand-500 hover:border-gray-300'" class="border text-gray-900 text-sm rounded-sm block w-full p-4 h-full min-h-[160px] transition-colors resize-none"></textarea>
                            <p x-show="errors.message" x-text="errors.message" class="absolute -bottom-6 mt-1 text-xs font-bold text-accent-500 tracking-wide block" x-transition></p>
                        </div>

                        <button type="submit" class="w-full text-white bg-brand-600 hover:bg-brand-700 focus:ring-4 focus:outline-none focus:ring-brand-300 font-bold rounded-sm text-sm px-6 py-5 text-center transition-colors uppercase tracking-wider mt-auto group flex items-center justify-center gap-2">
                            Gửi yêu cầu ngay <i class="fas fa-paper-plane group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('contactForm', () => ({
            name: '',
            phone: '',
            email: '',
            message: '',
            errors: {},
            validate() {
                this.errors = {};
                if (!this.name || this.name.length < 2) {
                    this.errors.name = 'Vui lòng cung cấp tên liên hệ';
                }
                const phoneRegex = /^(0[3|5|7|8|9])[0-9]{8}$|^\+84[3|5|7|8|9][0-9]{8}$/;
                if (!this.phone || !phoneRegex.test(this.phone)) {
                    this.errors.phone = 'Số điện thoại chưa đúng định dạng';
                }
                if (this.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email)) {
                    this.errors.email = 'Email không hợp lệ';
                }
                if (!this.message) {
                    this.errors.message = 'Nội dung hỗ trợ không được để trống';
                }
                return Object.keys(this.errors).length === 0;
            },
            submitForm(e) {
                if (this.validate()) {
                    e.target.submit();
                }
            }
        }));
    });
</script>
@endpush
