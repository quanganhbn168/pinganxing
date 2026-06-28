@extends('layouts.master')

@php
    $siteName = filled($setting->site_name) ? $setting->site_name : config('app.name');
    $phoneDisplay = filled($setting->phone_display) ? $setting->phone_display : $setting->phone;
    $phoneHref = filled($setting->phone) ? preg_replace('/[^0-9+]/', '', $setting->phone) : null;
    $websiteUrl = url('/');
    $websiteLabel = parse_url($websiteUrl, PHP_URL_HOST) ?: $websiteUrl;

    $resolveImage = function (mixed $value): ?string {
        if (blank($value)) {
            return null;
        }

        if (is_string($value) && str_starts_with(trim($value), '[') && str_ends_with(trim($value), ']')) {
            $decoded = json_decode($value, true);
            $value = is_array($decoded) ? $decoded : $value;
        }

        if (is_array($value)) {
            $value = $value[0] ?? null;
        }

        if (is_object($value)) {
            $value = $value->url ?? $value->path ?? null;
        }

        if (is_numeric($value)) {
            $media = \Awcodes\Curator\Models\Media::find((int) $value);
            $value = $media?->url ?? $media?->path;
        }

        if (blank($value)) {
            return null;
        }

        return filter_var($value, FILTER_VALIDATE_URL) ? $value : asset($value);
    };

    $contactBanner = $resolveImage($pageSettings->contact_banner ?: $setting->banner)
        ?: asset('images/setting/lien-he-bg.jpg');

    $placeholderSocialLinks = [
        'http://zalo.me',
        'https://zalo.me',
        'http://m.me',
        'https://m.me',
        'http://youtube.com',
        'https://youtube.com',
        'http://www.youtube.com',
        'https://www.youtube.com',
        'http://tiktok.com',
        'https://tiktok.com',
        'http://www.tiktok.com',
        'https://www.tiktok.com',
    ];

    $socialLinks = collect([
        ['label' => 'Facebook', 'href' => $setting->facebook, 'icon' => 'fab fa-facebook-f', 'color' => 'text-[#1877F2]'],
        ['label' => 'Zalo', 'href' => $setting->zalo, 'image' => asset('images/setting/Icon_of_Zalo.svg'), 'color' => 'text-[#0068FF]'],
        ['label' => 'Messenger', 'href' => $setting->messenger, 'icon' => 'fab fa-facebook-messenger', 'color' => 'text-[#00B2FF]'],
        ['label' => 'WhatsApp', 'href' => $setting->whatsapp, 'icon' => 'fab fa-whatsapp', 'color' => 'text-[#25D366]'],
        ['label' => 'WeChat', 'href' => $setting->wechat, 'icon' => 'fab fa-weixin', 'color' => 'text-[#07C160]'],
        ['label' => 'YouTube', 'href' => $setting->youtube, 'icon' => 'fab fa-youtube', 'color' => 'text-[#FF0000]'],
        ['label' => 'TikTok', 'href' => $setting->tiktok, 'icon' => 'fab fa-tiktok', 'color' => 'text-gray-900'],
    ])->filter(function (array $link) use ($placeholderSocialLinks): bool {
        if (blank($link['href'])) {
            return false;
        }

        return ! in_array(strtolower(rtrim(trim($link['href']), '/')), $placeholderSocialLinks, true);
    })->values();

    $mapValue = filled($setting->map) ? trim($setting->map) : null;
    $mapDirectionsUrl = null;

    if ($mapValue && preg_match('/<iframe[^>]+src=["\']([^"\']+)["\']/i', $mapValue, $mapMatch)) {
        $mapDirectionsUrl = $mapMatch[1];
    } elseif ($mapValue && filter_var($mapValue, FILTER_VALIDATE_URL)) {
        $mapDirectionsUrl = $mapValue;
    }
@endphp

@section('title', $pageSettings->contact_title ?: 'Liên hệ')

@section('content')

<!-- Hero Section -->
<section class="relative min-h-[500px] lg:min-h-[600px] flex flex-col justify-center pt-28 pb-32 bg-dark-primary">
    <!-- Background -->
    <div class="absolute inset-0 z-0">
        <img src="{{ $contactBanner }}" alt="{{ $pageSettings->contact_title ?: 'Liên hệ' }}" class="w-full h-full object-cover" />
        <div class="absolute inset-0 bg-gradient-to-r from-dark-primary/95 via-dark-primary/70 to-black/40"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 lg:px-8 w-full relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <!-- Left Text -->
            <div data-aos="fade-right">
                <div class="font-script text-3xl md:text-4xl mb-4 text-yellow-brand" style="font-family: 'Pacifico', cursive;">
                    {{ $pageSettings->contact_leaderboard_subline ?: 'Liên hệ với chúng tôi' }}
                </div>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-white leading-tight mb-6">
                    {{ $pageSettings->contact_title ?: $siteName . ' luôn sẵn sàng đồng hành cùng bạn' }}
                </h1>
                <p class="text-white/90 text-lg leading-relaxed max-w-lg">
                    {{ $pageSettings->contact_leaderboard_description ?: ($pageSettings->contact_headline ?: 'Nếu bạn có bất kỳ thắc mắc nào hoặc cần hỗ trợ, đừng ngần ngại liên hệ với chúng tôi. Đội ngũ tư vấn viên của ' . $siteName . ' sẽ phản hồi trong thời gian sớm nhất.') }}
                </p>
            </div>

            <!-- Right Cards -->
            <div data-aos="fade-left" class="bg-dark-primary/60 backdrop-blur-md rounded-2xl p-6 lg:p-8 border border-white/10 shadow-2xl">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Card 1 -->
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white shrink-0 text-2xl mt-1">
                            <i class="fas fa-headset"></i>
                        </div>
                        <div>
                            <h3 class="text-white font-bold text-base mb-1">Tư vấn tận tâm</h3>
                            <p class="text-white/70 text-sm leading-relaxed">Đội ngũ chuyên nghiệp, hỗ trợ tận tâm</p>
                        </div>
                    </div>
                    <!-- Card 2 -->
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white shrink-0 text-2xl mt-1">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <h3 class="text-white font-bold text-base mb-1">Thông tin chính xác</h3>
                            <p class="text-white/70 text-sm leading-relaxed">Cung cấp thông tin rõ ràng, minh bạch</p>
                        </div>
                    </div>
                    <!-- Card 3 -->
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white shrink-0 text-2xl mt-1">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <div>
                            <h3 class="text-white font-bold text-base mb-1">Đặt tour nhanh chóng</h3>
                            <p class="text-white/70 text-sm leading-relaxed">Quy trình đơn giản, xác nhận tức thì</p>
                        </div>
                    </div>
                    <!-- Card 4 -->
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white shrink-0 text-2xl mt-1">
                            <i class="fas fa-hands-helping"></i>
                        </div>
                        <div>
                            <h3 class="text-white font-bold text-base mb-1">Hỗ trợ toàn hành trình</h3>
                            <p class="text-white/70 text-sm leading-relaxed">Đồng hành cùng bạn trước, trong và sau chuyến đi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats / Quick Info -->
<div class="max-w-7xl mx-auto px-4 lg:px-8 relative z-20 -mt-20">
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 lg:p-10">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 divide-y sm:divide-y-0 sm:divide-x divide-gray-100">
            <!-- Hotline -->
            <div class="flex items-center gap-4 pt-4 sm:pt-0 sm:px-4">
                <div class="w-12 h-12 rounded-full border border-gray-200 flex items-center justify-center text-primary shrink-0 text-xl">
                    <i class="fas fa-phone-alt"></i>
                </div>
                <div>
                    <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wider mb-1">Hotline</p>
                    @if($phoneDisplay)
                        <a href="tel:{{ $phoneHref }}" class="text-xl font-extrabold text-gray-900 hover:text-primary">{{ $phoneDisplay }}</a>
                    @endif
                    <p class="text-xs text-gray-500 mt-1">Hỗ trợ tư vấn</p>
                </div>
            </div>
            <!-- Email -->
            <div class="flex items-center gap-4 pt-4 sm:pt-0 sm:px-4">
                <div class="w-12 h-12 rounded-full border border-gray-200 flex items-center justify-center text-primary shrink-0 text-xl">
                    <i class="fas fa-envelope"></i>
                </div>
                <div>
                    <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wider mb-1">Email</p>
                    @if($setting->email)
                        <a href="mailto:{{ $setting->email }}" class="block break-all text-xl font-extrabold text-gray-900 hover:text-primary">{{ $setting->email }}</a>
                    @endif
                    <p class="text-xs text-gray-500 mt-1">Phản hồi trong 24h</p>
                </div>
            </div>
            <!-- Working Hours -->
            <div class="flex items-center gap-4 pt-4 sm:pt-0 sm:px-4">
                <div class="w-12 h-12 rounded-full border border-gray-200 flex items-center justify-center text-primary shrink-0 text-xl">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wider mb-1">Thời gian làm việc</p>
                    <p class="text-lg font-extrabold text-gray-900">{{ $setting->working_hours ?: 'Đang cập nhật' }}</p>
                    <p class="text-xs text-gray-500 mt-1">Thời gian tiếp nhận tư vấn</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content (Form & Info) -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
            <!-- Left: Form -->
            <div data-aos="fade-up">
                <div class="mb-8">
                    <p class="text-yellow-brand font-extrabold text-xs uppercase tracking-wider mb-3">Gửi yêu cầu tư vấn</p>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 leading-tight">
                        Chúng tôi sẽ liên hệ lại với bạn trong thời gian sớm nhất
                    </h2>
                </div>

                <form id="contact-form" action="{{ route('contact.store') ?? '#' }}" method="POST" x-data="contactForm" @submit.prevent="submitForm">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Họ và tên <span class="text-red-500">*</span></label>
                            <input type="text" name="name" x-model="name" placeholder="Nhập họ và tên của bạn" class="w-full px-5 py-4 rounded-xl border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors text-sm" :class="errors.name ? 'border-red-500' : ''">
                            <p x-show="errors.name" x-text="errors.name" class="mt-1 text-xs text-red-500"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Số điện thoại <span class="text-red-500">*</span></label>
                            <input type="tel" name="phone" x-model="phone" placeholder="Nhập số điện thoại" class="w-full px-5 py-4 rounded-xl border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors text-sm" :class="errors.phone ? 'border-red-500' : ''">
                            <p x-show="errors.phone" x-text="errors.phone" class="mt-1 text-xs text-red-500"></p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Email</label>
                            <input type="email" name="email" x-model="email" placeholder="Nhập email của bạn" class="w-full px-5 py-4 rounded-xl border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors text-sm" :class="errors.email ? 'border-red-500' : ''">
                            <p x-show="errors.email" x-text="errors.email" class="mt-1 text-xs text-red-500"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-2">Dịch vụ quan tâm</label>
                            <select name="subject" class="w-full px-5 py-4 rounded-xl border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors text-gray-500 text-sm bg-white">
                                <option value="">Chọn dịch vụ</option>
                                <option value="Tour du lịch">Tour du lịch</option>
                                <option value="Vé máy bay">Vé máy bay</option>
                                <option value="Khách sạn">Khách sạn</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Nội dung yêu cầu</label>
                        <textarea name="message" x-model="message" rows="5" placeholder="Nhập nội dung bạn cần tư vấn..." class="w-full px-5 py-4 rounded-xl border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors resize-none text-sm"></textarea>
                        <p x-show="errors.message" x-text="errors.message" class="mt-1 text-xs text-red-500"></p>
                    </div>

                    <div class="mb-8 flex items-center gap-3">
                        <input type="checkbox" id="policy" checked class="w-5 h-5 rounded border-gray-300 text-yellow-brand focus:ring-yellow-brand">
                        <label for="policy" class="text-sm text-gray-500">Tôi đồng ý với <a href="{{ url('/chinh-sach-bao-mat-thong-tin') }}" class="text-primary font-bold">Chính sách bảo mật</a> của {{ $siteName }}</label>
                    </div>

                    <button type="submit" class="w-full bg-yellow-brand text-slate-900 font-extrabold text-lg py-4 rounded-xl hover:bg-amber-400 transition-colors shadow-lg flex items-center justify-center gap-2">
                        <i class="fas fa-paper-plane"></i> Gửi yêu cầu tư vấn
                    </button>
                </form>
            </div>

            <!-- Right: Info -->
            <div data-aos="fade-up" data-aos-delay="100">
                <div class="mb-8">
                    <p class="text-yellow-brand font-extrabold text-xs uppercase tracking-wider mb-3">Thông tin liên hệ</p>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 leading-tight">
                        {{ $siteName }} - Đồng hành cùng những hành trình đáng nhớ
                    </h2>
                </div>

                <div class="space-y-6">
                    <!-- Trụ sở chính -->
                    @if($setting->address)
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-full bg-orange-50 flex items-center justify-center text-orange-brand shrink-0">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-gray-900 text-base mb-1">Trụ sở chính</h4>
                            <p class="text-gray-600 text-sm leading-relaxed max-w-sm">{{ $setting->address }}</p>
                        </div>
                    </div>
                    @endif
                    
                    @foreach($branches ?? [] as $branch)
                    <!-- Chi nhánh -->
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-full bg-orange-50 flex items-center justify-center text-orange-brand shrink-0">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-gray-900 text-base mb-1">{{ $branch->name }}</h4>
                            <p class="text-gray-600 text-sm leading-relaxed max-w-sm">{{ $branch->address }}</p>
                        </div>
                    </div>
                    @endforeach

                    <!-- Hotline -->
                    @if($phoneDisplay)
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-full bg-orange-50 flex items-center justify-center text-orange-brand shrink-0">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-gray-900 text-base mb-1">Hotline</h4>
                            <p class="text-gray-600 text-sm leading-relaxed">
                                <a href="tel:{{ $phoneHref }}" class="font-extrabold text-gray-900 hover:text-primary">{{ $phoneDisplay }}</a>
                            </p>
                        </div>
                    </div>
                    @endif

                    <!-- Email -->
                    @if($setting->email)
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-full bg-orange-50 flex items-center justify-center text-orange-brand shrink-0">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-gray-900 text-base mb-1">Email</h4>
                            <p class="text-gray-600 text-sm leading-relaxed">
                                <a href="mailto:{{ $setting->email }}" class="break-all hover:text-primary">{{ $setting->email }}</a>
                            </p>
                        </div>
                    </div>
                    @endif

                    <!-- Website -->
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-full bg-orange-50 flex items-center justify-center text-orange-brand shrink-0">
                            <i class="fas fa-globe"></i>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-gray-900 text-base mb-1">Website</h4>
                            <p class="text-gray-600 text-sm leading-relaxed">
                                <a href="{{ $websiteUrl }}" class="hover:text-primary">{{ $websiteLabel }}</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@if($mapValue)
<!-- Map Section -->
<section class="relative h-[400px] lg:h-[450px] bg-gray-100 mt-8">
    @if(str_contains(strtolower($mapValue), '<iframe'))
        <div class="absolute inset-0 w-full h-full [&>iframe]:w-full [&>iframe]:h-full [&>iframe]:border-0">{!! $mapValue !!}</div>
    @else
        <iframe src="{{ $mapValue }}" class="absolute inset-0 w-full h-full border-0" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Bản đồ {{ $siteName }}"></iframe>
    @endif
    
    <!-- Map Overlay Card -->
    <div class="absolute inset-0 flex items-center justify-center pointer-events-none px-4">
        <div class="bg-white rounded-2xl shadow-xl p-4 flex items-center gap-4 max-w-sm pointer-events-auto hover:shadow-2xl transition-shadow cursor-pointer">
            <img src="{{ $contactBanner }}" class="w-16 h-16 rounded-xl object-cover shrink-0" alt="Trụ sở {{ $siteName }}">
            <div>
                <h4 class="font-extrabold text-gray-900 text-sm mb-1">{{ $siteName }} - Trụ sở chính</h4>
                @if($setting->address)
                    <p class="text-xs text-gray-500 mb-2 line-clamp-1">{{ $setting->address }}</p>
                @endif
                @if($mapDirectionsUrl)
                    <a href="{{ $mapDirectionsUrl }}" target="_blank" rel="noopener noreferrer" class="text-primary text-xs font-bold flex items-center gap-1 hover:text-dark-primary">Chỉ đường <i class="fas fa-arrow-right"></i></a>
                @endif
            </div>
        </div>
    </div>
</section>
@endif

<!-- Connect & Newsletter -->
<section class="py-16 bg-white border-t border-gray-100">
    <div class="max-w-7xl mx-auto px-4 lg:px-8">
        <div class="{{ $socialLinks->isNotEmpty() ? 'grid grid-cols-1 md:grid-cols-2 gap-12 lg:gap-20' : 'max-w-2xl mx-auto' }}">
            <!-- Social -->
            @if($socialLinks->isNotEmpty())
            <div>
                <p class="text-gray-500 font-extrabold text-xs uppercase tracking-wider mb-2">KẾT NỐI VỚI CHÚNG TÔI</p>
                <h3 class="text-xl font-extrabold text-gray-900 mb-8">Theo dõi {{ $siteName }} trên các kênh mạng xã hội</h3>

                <div class="flex flex-wrap gap-4 sm:gap-6">
                    @foreach($socialLinks as $link)
                        <a href="{{ $link['href'] }}" target="_blank" rel="noopener noreferrer" class="flex flex-col items-center group" aria-label="{{ $link['label'] }} {{ $siteName }}">
                            <div class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center {{ $link['color'] }} text-xl mb-2 group-hover:-translate-y-1 transition-all">
                                @if(isset($link['image']))
                                    <img src="{{ $link['image'] }}" onerror="this.src='{{ asset('images/setting/zalo.png') }}'; this.onerror=null;" class="w-5 h-5" alt="">
                                @else
                                    <i class="{{ $link['icon'] }}"></i>
                                @endif
                            </div>
                            <span class="text-xs font-bold text-gray-900">{{ $link['label'] }}</span>
                            <span class="text-[10px] text-gray-500 mt-0.5">{{ $siteName }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Newsletter -->
            <div class="{{ $socialLinks->isNotEmpty() ? 'md:pl-12 md:border-l border-gray-100' : '' }}">
                <p class="text-gray-500 font-extrabold text-xs uppercase tracking-wider mb-2">ĐĂNG KÝ NHẬN TIN</p>
                <h3 class="text-xl font-extrabold text-gray-900 mb-8">Nhận ưu đãi và thông tin du lịch hấp dẫn</h3>
                
                <form action="{{ route('contact.store') }}" method="POST" class="flex flex-col sm:flex-row gap-3 mb-4">
                    @csrf
                    <input type="hidden" name="source" value="newsletter">
                    <input type="hidden" name="name" value="Khách đăng ký bản tin">
                    <input type="email" name="email" required placeholder="Nhập email của bạn" class="flex-1 px-5 py-3.5 rounded-xl border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors text-sm">
                    <button type="submit" class="px-8 py-3.5 bg-yellow-brand text-slate-900 font-extrabold rounded-xl hover:bg-amber-400 transition-colors">Đăng ký</button>
                </form>
                <p class="text-xs text-gray-500 flex items-center gap-2"><i class="fas fa-check-circle text-gray-300"></i> Chúng tôi cam kết không spam. Bạn có thể hủy đăng ký bất kỳ lúc nào.</p>
            </div>
        </div>
    </div>
</section>

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
                    this.errors.name = 'Vui lòng cung cấp họ và tên';
                }
                const phoneRegex = /^(0[3|5|7|8|9])[0-9]{8}$|^\+84[3|5|7|8|9][0-9]{8}$/;
                if (!this.phone || !phoneRegex.test(this.phone)) {
                    this.errors.phone = 'Số điện thoại chưa hợp lệ';
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
