<footer id="contact" class="bg-dark-primary text-white pt-16 border-t border-white/10">
    <div class="max-w-7xl mx-auto px-4 lg:px-8">
        <div class="grid md:grid-cols-2 lg:grid-cols-5 gap-10 pb-12">
            <div class="lg:col-span-2">
                <a href="{{ url('/') }}" class="flex items-center gap-3 mb-5">
                    <img src="{{ !empty($globalLogoUrl) ? $globalLogoUrl : asset('images/setting/no-image.png') }}" class="h-12 md:h-14 object-contain" alt="{{ $setting->site_name ?? config('app.name') }}" />
                    <div class="hidden sm:block">
                        <div class="text-2xl font-extrabold">{{ $setting->site_name ?? config('app.name') }}</div>
                        <div class="text-white/60 text-xs">Tận hưởng từng khoảnh khắc</div>
                    </div>
                </a>
                <p class="text-white/65 leading-7 max-w-md">
                    {{ $setting->description ?? 'VietJourney – Người bạn đồng hành trên mọi hành trình khám phá Việt Nam và thế giới.' }}
                </p>

                @php
                    $footerPlaceholderSocialLinks = ['http://zalo.me', 'https://zalo.me', 'http://m.me', 'https://m.me'];

                    $footerSocialValue = function (string|array $keys) use ($setting, $footerPlaceholderSocialLinks) {
                        foreach ((array) $keys as $key) {
                            if (isset($setting->{$key}) && filled($setting->{$key})) {
                                $value = trim((string) $setting->{$key});

                                if (! in_array(rtrim($value, '/'), $footerPlaceholderSocialLinks, true)) {
                                    return $value;
                                }
                            }
                        }

                        return null;
                    };

                    $footerSocialLinks = collect([
                        ['label' => 'Facebook', 'href' => $footerSocialValue('facebook'), 'class' => 'hover:bg-[#1877F2]', 'icon' => 'fab fa-facebook-f'],
                        ['label' => 'Zalo', 'href' => $footerSocialValue('zalo'), 'class' => 'hover:bg-[#0068FF]', 'image' => asset('images/setting/Icon_of_Zalo.svg')],
                        ['label' => 'Messenger', 'href' => $footerSocialValue(['messenger', 'mess']), 'class' => 'hover:bg-[#00B2FF]', 'icon' => 'fab fa-facebook-messenger'],
                        ['label' => 'WhatsApp', 'href' => $footerSocialValue('whatsapp'), 'class' => 'hover:bg-[#25D366]', 'icon' => 'fab fa-whatsapp'],
                        ['label' => 'WeChat', 'href' => $footerSocialValue('wechat'), 'class' => 'hover:bg-[#07C160]', 'icon' => 'fab fa-weixin'],
                        ['label' => 'Youtube', 'href' => $footerSocialValue('youtube'), 'class' => 'hover:bg-[#FF0000]', 'icon' => 'fab fa-youtube'],
                        ['label' => 'Tiktok', 'href' => $footerSocialValue('tiktok'), 'class' => 'hover:bg-black', 'icon' => 'fab fa-tiktok'],
                    ])->filter(fn (array $link) => filled($link['href']))->values();
                @endphp

                <div class="flex flex-wrap gap-3 mt-6">
                    @foreach($footerSocialLinks as $link)
                        <a href="{{ $link['href'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $link['label'] }}" class="w-10 h-10 rounded-full bg-white/10 {{ $link['class'] }} transition-colors flex items-center justify-center p-2.5">
                            @if(!empty($link['image']))
                                <img src="{{ $link['image'] }}" onerror="this.src='{{ asset('images/setting/zalo.png') }}'; this.onerror=null;" alt="{{ $link['label'] }}" class="w-full h-full object-contain">
                            @else
                                <i class="{{ $link['icon'] }} text-white"></i>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>

            <div>
                <h3 class="font-extrabold text-yellow-brand mb-5">{{ $setting->footer_col_2_title ?? 'Về chúng tôi' }}</h3>
                <ul class="space-y-3 text-white/65">
                    @if(isset($footerCol2Menu) && $footerCol2Menu->count() > 0)
                        @foreach($footerCol2Menu as $menuItem)
                            <li><a href="{{ $menuItem->link }}" target="{{ $menuItem->link_target }}" class="hover:text-yellow-brand transition-colors">{{ $menuItem->title }}</a></li>
                        @endforeach
                    @elseif(!empty($setting->footer_col_2_links) && is_array($setting->footer_col_2_links))
                        @foreach($setting->footer_col_2_links as $link)
                            <li><a href="{{ $link['url'] ?? '#' }}" class="hover:text-yellow-brand transition-colors">{{ $link['label'] ?? '' }}</a></li>
                        @endforeach
                    @else
                        <li><a href="#" class="hover:text-yellow-brand transition-colors">Giới thiệu</a></li>
                        <li><a href="#" class="hover:text-yellow-brand transition-colors">Tuyển dụng</a></li>
                        <li><a href="#" class="hover:text-yellow-brand transition-colors">Tin tức</a></li>
                        <li><a href="#" class="hover:text-yellow-brand transition-colors">Liên hệ</a></li>
                    @endif
                </ul>
            </div>

            <div>
                <h3 class="font-extrabold text-yellow-brand mb-5">{{ $setting->footer_col_3_title ?? 'Chính sách' }}</h3>
                <ul class="space-y-3 text-white/65">
                    @if(isset($footerCol3Menu) && $footerCol3Menu->count() > 0)
                        @foreach($footerCol3Menu as $menuItem)
                            <li><a href="{{ $menuItem->link }}" target="{{ $menuItem->link_target }}" class="hover:text-yellow-brand transition-colors">{{ $menuItem->title }}</a></li>
                        @endforeach
                    @elseif(!empty($setting->footer_col_3_links) && is_array($setting->footer_col_3_links))
                        @foreach($setting->footer_col_3_links as $link)
                            <li><a href="{{ $link['url'] ?? '#' }}" class="hover:text-yellow-brand transition-colors">{{ $link['label'] ?? '' }}</a></li>
                        @endforeach
                    @else
                        <li><a href="#" class="hover:text-yellow-brand transition-colors">Chính sách bảo mật</a></li>
                        <li><a href="#" class="hover:text-yellow-brand transition-colors">Điều khoản dịch vụ</a></li>
                        <li><a href="#" class="hover:text-yellow-brand transition-colors">Hướng dẫn đặt tour</a></li>
                        <li><a href="#" class="hover:text-yellow-brand transition-colors">Chính sách hoàn hủy</a></li>
                    @endif
                </ul>
            </div>

            <div>
                <h3 class="font-extrabold text-yellow-brand mb-5">Thông tin liên hệ</h3>
                <ul class="space-y-3 text-white/65 text-sm">
                    <li class="flex items-start gap-2">
                        <i class="fas fa-map-marker-alt mt-1 text-yellow-brand"></i>
                        <span>{{ $setting->address ?? '123 Trần Phú, Ba Đình, Hà Nội' }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-phone-alt mt-1 text-yellow-brand"></i>
                        <span>{{ $setting->phone_display ?? $setting->phone ?? '1900 1234' }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-envelope mt-1 text-yellow-brand"></i>
                        <span>{{ $setting->email ?? 'info@vietjourney.vn' }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-clock mt-1 text-yellow-brand"></i>
                        <span>{{ $setting->working_hours ?? '08:00 - 22:00' }}</span>
                    </li>
                </ul>
                @if(!empty($setting->bct_link))
                <div class="mt-4">
                    <a href="{{ $setting->bct_link }}" target="_blank" rel="nofollow">
                        <img src="https://theme.hstatic.net/1000026602/1001190558/14/logo-bct.png?v=210" alt="Đã thông báo Bộ Công Thương" class="h-10 object-contain filter brightness-0 invert opacity-80">
                    </a>
                </div>
                @endif
            </div>
        </div>

        <div class="border-t border-white/10 py-6 flex flex-col md:flex-row justify-between items-center gap-4 text-white/50 text-sm">
            <div>© {{ date('Y') }} {{ $setting->company_name ?? ($setting->site_name ?? 'VietJourney') }}. All rights reserved.</div>
            <div class="flex gap-3">
                <span class="px-3 py-1 rounded bg-white/10 font-bold text-xs flex items-center">VISA</span>
                <span class="px-3 py-1 rounded bg-white/10 font-bold text-xs flex items-center">MASTER</span>
                <span class="px-3 py-1 rounded bg-white/10 font-bold text-xs flex items-center">ATM</span>
                <span class="px-3 py-1 rounded bg-white/10 font-bold text-xs flex items-center">QR</span>
            </div>
        </div>
    </div>
</footer>
