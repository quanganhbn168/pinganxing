@extends('layouts.master')
@section('title', 'Hợp tác Đại lý & Đối tác')

@section('content')
<x-frontend.page-hero 
    title="Hợp tác Đại lý" 
    subtitle="Cùng chúng tôi kiến tạo thành công và chia sẻ giá trị bền vững"
    :breadcrumb="[['label' => 'Đại lý & Đối tác']]" 
/>
<div class="bg-white dark:bg-gray-900 py-12 md:py-20">
    <div class="max-w-screen-xl mx-auto px-4">
        
        {{-- CHÍNH SÁCH HỢP TÁC --}}
        <div class="mb-20">
            <div class="flex items-center justify-between border-b-2 border-gray-100 dark:border-gray-700 mb-10 pb-2">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white uppercase relative inline-block">
                    Chính sách hợp tác
                    <div class="absolute -bottom-[4px] left-0 w-24 h-1 bg-blue-600 rounded-r-full"></div>
                </h2>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-0 items-stretch">
                {{-- Box Thông điệp --}}
                <div class="bg-blue-600 text-white p-8 md:p-12 relative h-full flex flex-col justify-center rounded-2xl lg:rounded-r-none lg:rounded-l-3xl z-10">
                    <span class="absolute -top-10 left-6 text-9xl text-white/20 font-serif leading-none select-none">
                        &ldquo;
                    </span>
                    <div class="prose prose-lg prose-invert max-w-none relative z-10">
                        <h3 class="text-2xl md:text-3xl font-bold mb-6">
                            Cùng {{ $setting->site_name ?? 'chúng tôi' }} kiến tạo thành công!
                        </h3>
                        <p class="text-lg mb-6 text-blue-100">
                            Chúng tôi cam kết mang lại giá trị bền vững cho đối tác với chính sách ưu việt:
                        </p>
                        <ul class="space-y-4 mb-8">
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-orange-400 mt-1.5 mr-3 text-lg"></i>
                                <div>
                                    <strong class="block text-white">Chiết khấu hấp dẫn</strong>
                                    <span class="text-blue-100">Lợi nhuận lên đến 40% tùy theo doanh số cam kết.</span>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-bullhorn text-orange-400 mt-1.5 mr-3 text-lg"></i>
                                <div>
                                    <strong class="block text-white">Hỗ trợ Marketing</strong>
                                    <span class="text-blue-100">Cung cấp biển hiệu, catalogue và data khách hàng khu vực.</span>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-chalkboard-teacher text-orange-400 mt-1.5 mr-3 text-lg"></i>
                                <div>
                                    <strong class="block text-white">Đào tạo chuyên sâu</strong>
                                    <span class="text-blue-100">Hướng dẫn kỹ thuật và kỹ năng bán hàng bài bản.</span>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-shield-alt text-orange-400 mt-1.5 mr-3 text-lg"></i>
                                <div>
                                    <strong class="block text-white">Bảo hành uy tín</strong>
                                    <span class="text-blue-100">Cơ chế bảo hành 1 đổi 1 nhanh chóng.</span>
                                </div>
                            </li>
                        </ul>
                        <div class="border-l-4 border-orange-400 pl-4 py-2 mt-8">
                            <em class="text-xl text-white font-medium">"Thành công của bạn là sứ mệnh của chúng tôi."</em>
                        </div>
                    </div>
                </div>

                {{-- Ảnh Lãnh đạo/Hợp tác --}}
                <div class="relative flex items-end justify-center lg:justify-end lg:-ml-12 lg:-mt-20 z-20">
                    <img src="{{ asset('images/setting/bat-tay.png') }}" onerror="this.src='https://placehold.co/400x420/png?text=Partner'" alt="Hợp tác đại lý" 
                         class="h-[400px] md:h-[500px] object-contain drop-shadow-2xl z-20">
                </div>
            </div>
        </div>

        {{-- FORM ĐĂNG KÝ --}}
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center justify-center mb-10">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white uppercase relative inline-block text-center">
                    Đăng ký đại lý
                    <div class="absolute -bottom-[4px] left-1/2 -translate-x-1/2 w-24 h-1 bg-blue-600 rounded-full"></div>
                </h2>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 md:p-12 shadow-xl border-t-4 border-blue-600 relative overflow-hidden">
                {{-- Background Decoration --}}
                <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-blue-50 dark:bg-gray-700/50 rounded-full blur-2xl opacity-50 pointer-events-none"></div>
                <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-40 h-40 bg-orange-50 dark:bg-gray-700/50 rounded-full blur-2xl opacity-50 pointer-events-none"></div>
                
                <div class="relative z-10">
                    <p class="text-center mb-10 text-gray-600 dark:text-gray-300 text-lg">
                        Vui lòng điền thông tin doanh nghiệp, chúng tôi sẽ gửi chính sách chi tiết qua Email.
                    </p>
                    
                    <form action="{{ route('agency.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wide">Họ và tên <span class="text-red-500">*</span></label>
                                <input type="text" name="name" required placeholder="Ví dụ: Nguyễn Văn A" 
                                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-4 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white transition-colors">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wide">Số điện thoại <span class="text-red-500">*</span></label>
                                <input type="tel" name="phone" required placeholder="Ví dụ: 0987654321" 
                                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-4 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white transition-colors">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wide">Tên Cửa hàng / Công ty</label>
                                <input type="text" name="shop_name" placeholder="Ví dụ: Công ty TNHH ABC" 
                                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-4 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white transition-colors">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wide">Khu vực đăng ký</label>
                                <input type="text" name="area" placeholder="Ví dụ: Hà Nội" 
                                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-4 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white transition-colors">
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wide">Địa chỉ chi tiết</label>
                            <input type="text" name="address" placeholder="Số nhà, Đường, Quận/Huyện..." 
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-4 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white transition-colors">
                        </div>

                        <div class="mb-8">
                            <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wide">Ghi chú thêm</label>
                            <textarea name="details" rows="4" placeholder="Giới thiệu đôi nét về kinh nghiệm kinh doanh của bạn..."
                                      class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-4 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white transition-colors resize-none"></textarea>
                        </div>

                        <div class="text-center mt-8">
                            <button type="submit" 
                                    class="inline-flex items-center justify-center text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-bold rounded-xl text-lg px-10 py-4 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 transition-colors shadow-lg shadow-blue-500/30 uppercase tracking-wider">
                                Gửi Đăng Ký Ngay <i class="fas fa-paper-plane ml-3"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
