@php
    $placeholderContactLinks = ['http://zalo.me', 'https://zalo.me', 'http://m.me', 'https://m.me'];

    $settingValue = function (string|array $keys) use ($setting, $placeholderContactLinks) {
        foreach ((array) $keys as $key) {
            if (isset($setting->{$key}) && filled($setting->{$key})) {
                $value = trim((string) $setting->{$key});

                if (! in_array(rtrim($value, '/'), $placeholderContactLinks, true)) {
                    return $value;
                }
            }
        }

        return null;
    };

    $phone = $settingValue('phone');
    $phoneHref = $phone ? 'tel:' . preg_replace('/[^\d+]/', '', $phone) : null;
    $zaloIcon = asset('images/setting/Icon_of_Zalo.svg');

    $floatingActions = collect([
        [
            'key' => 'phone',
            'label' => $setting->phone_display ?? $phone ?? 'Gọi ngay',
            'href' => $phoneHref,
            'icon' => 'fas fa-phone-alt',
            'external' => false,
        ],
        [
            'key' => 'facebook',
            'label' => 'Facebook',
            'href' => $settingValue('facebook'),
            'icon' => 'fab fa-facebook-f',
            'external' => true,
        ],
        [
            'key' => 'zalo',
            'label' => 'Zalo',
            'href' => $settingValue('zalo'),
            'image' => $zaloIcon,
            'external' => true,
        ],
        [
            'key' => 'messenger',
            'label' => 'Messenger',
            'href' => $settingValue(['messenger', 'mess']),
            'icon' => 'fab fa-facebook-messenger',
            'external' => true,
        ],
        [
            'key' => 'whatsapp',
            'label' => 'WhatsApp',
            'href' => $settingValue('whatsapp'),
            'icon' => 'fab fa-whatsapp',
            'external' => true,
        ],
        [
            'key' => 'wechat',
            'label' => 'WeChat',
            'href' => $settingValue('wechat'),
            'opens_qr' => filled($globalWechatQrUrl ?? null),
            'icon' => 'fab fa-weixin',
            'external' => true,
        ],
    ])->filter(fn (array $action) => filled($action['href']) || !empty($action['opens_qr']))->values();
@endphp

@if($floatingActions->isNotEmpty())
<div class="floating-contact" x-data="{ open: false }" @keydown.escape.window="open = false">
    <div class="floating-contact__panel" :class="{ 'is-open': open }">
        @foreach($floatingActions as $action)
            @if(!empty($action['opens_qr']))
            <button
                type="button"
                data-wechat-qr-trigger
                @click="open = false"
                class="floating-contact__item is-{{ $action['key'] }}"
                aria-label="Hiển thị mã QR {{ $action['label'] }}"
            >
                <span class="floating-contact__icon">
                    <i class="{{ $action['icon'] }}"></i>
                </span>
                <span class="floating-contact__label">Quét QR {{ $action['label'] }}</span>
            </button>
            @else
            <a
                href="{{ $action['href'] }}"
                @if($action['external']) target="_blank" rel="noopener noreferrer" @endif
                class="floating-contact__item is-{{ $action['key'] }}"
                aria-label="{{ $action['label'] }}"
            >
                <span class="floating-contact__icon">
                    @if(!empty($action['image']))
                        <img src="{{ $action['image'] }}" onerror="this.src='{{ asset('images/setting/zalo.png') }}'; this.onerror=null;" alt="" aria-hidden="true">
                    @else
                        <i class="{{ $action['icon'] }}"></i>
                    @endif
                </span>
                <span class="floating-contact__label">{{ $action['label'] }}</span>
            </a>
            @endif
        @endforeach
    </div>

    <button
        type="button"
        class="floating-contact__toggle"
        @click="open = ! open"
        :aria-expanded="open.toString()"
        aria-label="Mở liên hệ nhanh"
    >
        <i class="fas fa-headset" x-show="! open"></i>
        <i class="fas fa-times" x-show="open" x-cloak></i>
        <span>Liên hệ</span>
    </button>
</div>
@endif

<a href="#" class="floating-back-to-top" id="js-back-to-top" aria-label="Lên đầu trang">
    <i class="fas fa-arrow-up"></i>
</a>
