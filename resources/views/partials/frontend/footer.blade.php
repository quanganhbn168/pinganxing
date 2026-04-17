<footer class="text-gray-300 py-12 mt-10 relative bg-gray-900 bg-cover bg-center bg-no-repeat" 
        @if(!empty($globalFooterBackgroundUrl)) style="background-image: linear-gradient(to right, rgba(17, 24, 39, 0.95), rgba(17, 24, 39, 0.85)), url('{{ $globalFooterBackgroundUrl }}');" @endif>
    <div class="max-w-screen-xl mx-auto px-4 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            
            {{-- Cột 1: Thông tin công ty --}}
            <div>
                <a href="/" class="block mb-6">
                    <img src="{{ !empty($globalLogoUrl) ? $globalLogoUrl : asset('images/setting/no-image.png') }}" class="h-12 md:h-14 object-contain" alt="{{ $setting->site_name ?? 'Logo' }}">
                </a>
                <p class="text-sm text-gray-400 mb-6 leading-relaxed">
                    {{ $setting->description ?? 'CnetPOS - Đồng hành cùng bạn trên "Hành trình tới tương lai", mang đến giải pháp hiện đại và đẳng cấp.' }}
                </p>
                
                
                <div class="space-y-3 text-sm">
                    
                    <div class="flex items-start">
                        <i class="fa-solid fa-building mt-1 me-3 text-blue-500 w-4"></i>
                        <span>MST: {{ $setting->tax_code ?? '2301372686' }}</span>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-map-marker-alt mt-1 me-3 text-blue-500 w-4"></i>
                        <span>Địa chỉ: {{ $setting->address ?? 'Tầng 3...' }}</span>
                    </div>

                    <div class="flex items-start">
                        <i class="fas fa-phone-alt mt-1 me-3 text-blue-500 w-4"></i>
                        <span>SĐT: </span>
                        <a href="tel:{{ preg_replace('/\s+/', '', $setting->phone ?? '') }}" class="hover:text-white transition-colors">{{ $setting->phone_display ?? $setting->phone ?? '' }}</a>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-envelope mt-1 me-3 text-blue-500 w-4"></i>
                        <span>Email: </span>
                        <a href="mailto:{{ $setting->email ?? '' }}" class="hover:text-white transition-colors">{{ $setting->email ?? '' }}</a>
                    </div>
                </div>
                
                @if(!empty($setting->bct_link))
                <div class="mt-6">
                    <a href="{{ $setting->bct_link }}" target="_blank" rel="nofollow">
                        <img src="https://theme.hstatic.net/1000026602/1001190558/14/logo-bct.png?v=210" alt="Đã thông báo Bộ Công Thương" class="h-12 object-contain filter drop-shadow-sm">
                    </a>
                </div>
                @endif
            </div>

            {{-- Cột 2: Liên kết (Ưu tiên Menu System → fallback Settings Repeater) --}}
            <div>
                <h4 class="text-white text-lg font-semibold mb-6">{{ $setting->footer_col_2_title ?? 'Về Cnetpos' }}</h4>
                <ul class="space-y-3 text-sm">
                    @if(isset($footerCol2Menu) && $footerCol2Menu->count() > 0)
                        @foreach($footerCol2Menu as $menuItem)
                            <li>
                                <a href="{{ $menuItem->link }}" target="{{ $menuItem->link_target }}" class="hover:text-blue-400 transition-colors flex items-center gap-2">
                                    @if($menuItem->icon)<i class="{{ $menuItem->icon }} text-xs text-gray-500"></i>@endif
                                    {{ $menuItem->title }}
                                </a>
                            </li>
                        @endforeach
                    @elseif(!empty($setting->footer_col_2_links) && is_array($setting->footer_col_2_links))
                        @foreach($setting->footer_col_2_links as $link)
                            <li><a href="{{ $link['url'] ?? '#' }}" class="hover:text-blue-400 transition-colors">{{ $link['label'] ?? '' }}</a></li>
                        @endforeach
                    @else
                        <li><a href="/" class="hover:text-blue-400 transition-colors">Trang chủ</a></li>
                        <li><a href="{{ route('frontend.intro.index') }}" class="hover:text-blue-400 transition-colors">Về chúng tôi</a></li>
                        <li><a href="/du-an" class="hover:text-blue-400 transition-colors">Dự án đã thực hiện</a></li>
                        <li><a href="/lien-he" class="hover:text-blue-400 transition-colors">Liên hệ</a></li>
                    @endif
                </ul>
            </div>

            {{-- Cột 3: Chính sách (Ưu tiên Menu System → fallback Settings Repeater) --}}
            <div>
                <h4 class="text-white text-lg font-semibold mb-6">{{ $setting->footer_col_3_title ?? 'Chính sách & Hướng dẫn' }}</h4>
                <ul class="space-y-3 text-sm">
                    @if(isset($footerCol3Menu) && $footerCol3Menu->count() > 0)
                        @foreach($footerCol3Menu as $menuItem)
                            <li>
                                <a href="{{ $menuItem->link }}" target="{{ $menuItem->link_target }}" class="hover:text-blue-400 transition-colors flex items-center gap-2">
                                    @if($menuItem->icon)<i class="{{ $menuItem->icon }} text-xs text-gray-500"></i>@endif
                                    {{ $menuItem->title }}
                                </a>
                            </li>
                        @endforeach
                    @elseif(!empty($setting->footer_col_3_links) && is_array($setting->footer_col_3_links))
                        @foreach($setting->footer_col_3_links as $link)
                            <li><a href="{{ $link['url'] ?? '#' }}" class="hover:text-blue-400 transition-colors">{{ $link['label'] ?? '' }}</a></li>
                        @endforeach
                    @else
                        <li><a href="#" class="hover:text-blue-400 transition-colors">Chính sách bảo mật</a></li>
                        <li><a href="#" class="hover:text-blue-400 transition-colors">Điều khoản sử dụng</a></li>
                    @endif
                </ul>
            </div>

            {{-- Cột 4: Đăng ký & Social --}}
            <div>
                <h4 class="text-white text-lg font-semibold mb-6">Kết nối với chúng tôi</h4>
                <p class="text-sm text-gray-400 mb-4">Đăng ký để nhận thông tin mới nhất về sản phẩm và khuyến mãi.</p>
                <form class="flex mb-6">
                    <input type="email" placeholder="Email của bạn..." class="bg-gray-800 text-sm border-0 text-white rounded-l-lg focus:ring-blue-500 focus:border-blue-500 w-full p-2.5" required>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white rounded-r-lg px-4 py-2.5 transition-colors">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
                <div class="flex space-x-4">
                    <a href="{{ $setting->facebook ?? '#' }}" target="_blank" class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-800 hover:bg-blue-600 transition-colors text-white">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="{{ $setting->youtube ?? '#' }}" target="_blank" class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-800 hover:bg-red-600 transition-colors text-white">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a href="{{ $setting->zalo ?? '#' }}" target="_blank" class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-800 hover:bg-blue-500 transition-colors text-white">
                        <i class="fas fa-comment-dots"></i>
                    </a>
                </div>
            </div>

        </div>
    </div>
    <div class="border-t border-gray-800 mt-12 pt-6 text-center text-sm text-gray-500">
        <p>© Bản quyền thuộc về <b class="text-white">{{ $setting->company_name ?? ($setting->site_name ?? 'CNETPos') }}</b> | Thiết kế bởi <a href="https://webappbacninh.vn/" target="_blank" class="hover:text-blue-400">Web App Bắc Ninh</a></p>
    </div>
</footer>
