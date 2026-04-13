@extends('layouts.master')
@section('title', 'Tư vấn triển khai & Dự toán')

@section('content')
<x-frontend.page-hero 
    title="Tư vấn & Giải pháp" 
    subtitle="Giải pháp kỹ thuật toàn diện, tối ưu và chuyên nghiệp"
    :breadcrumb="[['label' => 'Tư vấn triển khai']]" 
/>
<div class="bg-gray-50 dark:bg-gray-900 py-12 md:py-20">
    <div class="max-w-screen-xl mx-auto px-4">
        
        {{-- THÔNG ĐIỆP --}}
        <div class="mb-20">
            <div class="flex items-center justify-between border-b-2 border-gray-100 dark:border-gray-700 mb-10 pb-2">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white uppercase relative inline-block">
                    Giải pháp kỹ thuật toàn diện
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
                            Chuyên gia của chúng tôi sẵn sàng hỗ trợ bạn
                        </h3>
                        <p class="text-lg mb-6 text-blue-100">
                            Quy trình tư vấn chuyên nghiệp giúp tối ưu chi phí và hiệu quả vận hành:
                        </p>
                        <ul class="space-y-4 mb-8">
                            <li class="flex items-start">
                                <i class="fas fa-search-location text-orange-400 mt-1.5 mr-3 text-lg"></i>
                                <div>
                                    <strong class="block text-white">Khảo sát thực tế</strong>
                                    <span class="text-blue-100">Đánh giá hiện trạng và nhu cầu cụ thể.</span>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-pencil-ruler text-orange-400 mt-1.5 mr-3 text-lg"></i>
                                <div>
                                    <strong class="block text-white">Thiết kế giải pháp</strong>
                                    <span class="text-blue-100">Lên bản vẽ và phương án thi công chi tiết.</span>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-file-invoice-dollar text-orange-400 mt-1.5 mr-3 text-lg"></i>
                                <div>
                                    <strong class="block text-white">Dự toán ngân sách</strong>
                                    <span class="text-blue-100">Minh bạch, chi tiết và tối ưu chi phí đầu tư.</span>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-microchip text-orange-400 mt-1.5 mr-3 text-lg"></i>
                                <div>
                                    <strong class="block text-white">Tư vấn công nghệ</strong>
                                    <span class="text-blue-100">Cập nhật xu hướng thiết bị mới nhất.</span>
                                </div>
                            </li>
                        </ul>
                        <div class="border-l-4 border-orange-400 pl-4 py-2 mt-8">
                            <em class="text-xl text-white font-medium">"Giải pháp đúng - Đầu tư thông minh."</em>
                        </div>
                    </div>
                </div>

                {{-- Ảnh Lãnh đạo/Hợp tác --}}
                <div class="relative flex items-end justify-center lg:justify-end lg:-ml-12 lg:-mt-20 z-20">
                    <img src="{{ asset($setting->intro_image ?? 'images/setting/lien-he-bg.jpg') }}" 
                         onerror="this.src='https://placehold.co/400x420/png?text=Solutions'" alt="Tư vấn giải pháp" 
                         class="h-[400px] md:h-[500px] object-contain drop-shadow-2xl z-20">
                </div>
            </div>
        </div>

        {{-- FORM TƯ VẤN --}}
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center justify-center mb-10">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white uppercase relative inline-block text-center">
                    Gửi yêu cầu & Bản vẽ
                    <div class="absolute -bottom-[4px] left-1/2 -translate-x-1/2 w-24 h-1 bg-blue-600 rounded-full"></div>
                </h2>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 md:p-12 shadow-xl border-t-4 border-blue-600 relative overflow-hidden">
                {{-- Background Decoration --}}
                <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-blue-50 dark:bg-gray-700/50 rounded-full blur-2xl opacity-50 pointer-events-none"></div>
                <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-40 h-40 bg-orange-50 dark:bg-gray-700/50 rounded-full blur-2xl opacity-50 pointer-events-none"></div>
                
                <div class="relative z-10">
                    <p class="text-center mb-10 text-gray-600 dark:text-gray-300 text-lg">
                        Vui lòng mô tả yêu cầu hoặc tải lên bản vẽ, chúng tôi sẽ phản hồi trong vòng 24h.
                    </p>
                    
                    <form action="{{ route('consulting.store') }}" method="POST" enctype="multipart/form-data">
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
                                <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wide">Email</label>
                                <input type="email" name="email" placeholder="Ví dụ: abc@domain.com" 
                                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-4 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white transition-colors">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wide">Tên Công ty / Cửa hàng</label>
                                <input type="text" name="company" placeholder="Ví dụ: Công ty TNHH ABC" 
                                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-4 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white transition-colors">
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wide">Địa chỉ triển khai</label>
                            <input type="text" name="address" placeholder="Số nhà, Đường, Quận/Huyện..." 
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-4 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white transition-colors">
                        </div>

                        <div class="mb-6">
                            <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wide">Yêu cầu chi tiết / Mô tả dự án</label>
                            <textarea name="details" rows="5" placeholder="Mô tả nhu cầu, số lượng, quy mô..."
                                      class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-4 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white transition-colors resize-none"></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wide">Ngân sách dự kiến</label>
                                <input type="text" name="budget" placeholder="Ví dụ: 50 triệu - 100 triệu" 
                                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-4 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white transition-colors">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wide">File đính kèm (Bản vẽ, Hồ sơ)</label>
                                <div class="relative">
                                    <input type="file" name="file" id="customFile" 
                                           class="hidden w-full text-sm text-gray-900 border border-gray-300 rounded-xl cursor-pointer bg-gray-50 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                                    <label for="customFile" 
                                           class="flex items-center justify-between w-full p-4 text-gray-500 bg-gray-50 border border-gray-300 rounded-xl cursor-pointer dark:text-gray-400 dark:bg-gray-700 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors custom-file-label">
                                        <span class="file-name truncate">Chọn file đính kèm...</span>
                                        <span class="bg-gray-200 dark:bg-gray-600 px-3 py-1 rounded-lg text-sm font-medium">Duyệt</span>
                                    </label>
                                </div>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Hỗ trợ PDF, DOCX, IMG, CAD. Max 10MB.</p>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" 
                                    class="inline-flex items-center justify-center text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-bold rounded-xl text-lg px-10 py-4 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 transition-colors shadow-lg shadow-blue-500/30 uppercase tracking-wider">
                                <i class="fa fa-paper-plane mr-3"></i> Gửi Yêu Cầu
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('js')
<script>
    document.getElementById('customFile').addEventListener('change', function(e) {
        var fileName = e.target.files[0] ? e.target.files[0].name : 'Chọn file đính kèm...';
        document.querySelector('.file-name').textContent = fileName;
    });
</script>
@endpush
