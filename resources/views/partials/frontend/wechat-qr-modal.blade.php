@if(filled($globalWechatQrUrl ?? null))
    @php
        $wechatQrBrandName = $setting->site_name ?? config('app.name');
    @endphp

    <dialog id="wechat-qr-modal" class="wechat-qr-modal" aria-labelledby="wechat-qr-modal-title">
        <div class="wechat-qr-modal__content">
            <button type="button" data-wechat-qr-close class="wechat-qr-modal__close" aria-label="Đóng popup mã QR WeChat">
                <i class="fas fa-times" aria-hidden="true"></i>
            </button>

            <span class="wechat-qr-modal__icon" aria-hidden="true">
                <i class="fab fa-weixin"></i>
            </span>
            <h2 id="wechat-qr-modal-title" class="wechat-qr-modal__title">Quét mã QR WeChat</h2>
            <p class="wechat-qr-modal__description">Mở WeChat và quét mã bên dưới để kết nối với {{ $wechatQrBrandName }}.</p>

            <div class="wechat-qr-modal__image-frame">
                <img
                    src="{{ $globalWechatQrUrl }}"
                    alt="Mã QR WeChat của {{ $wechatQrBrandName }}"
                    class="wechat-qr-modal__image"
                >
            </div>
        </div>
    </dialog>
@endif
