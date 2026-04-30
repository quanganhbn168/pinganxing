@props([
    'title' => null,
    'description' => null,
    'link' => null,
    'image' => 'images/setting/cnetpos-partner.png',
    'imageAlt' => null,
    'buttonLabel' => 'Liên hệ chuyên gia tư vấn',
])

@php
    $defaultTitle = 'Bạn cần tư vấn giải pháp chuyển đổi số?';
    $defaultDescription = 'Hệ thống CNETPOS sở hữu lõi công nghệ linh hoạt, dễ tùy biến theo quy trình vận hành thực tế của doanh nghiệp.';

    $finalTitle = filled($title) ? $title : $defaultTitle;
    $finalDescription = filled($description) ? $description : $defaultDescription;
    $finalLink = filled($link) ? $link : route('contact.show');
    $finalImage = filled($image)
        ? (filter_var($image, FILTER_VALIDATE_URL) ? $image : asset(ltrim($image, '/')))
        : null;
@endphp

<div class="frontend-cta-wrap">
    <section class="frontend-page-cta">
        @if($finalImage)
            <div class="frontend-cta-media" aria-hidden="true">
                <img src="{{ $finalImage }}" alt="{{ $imageAlt ?: $finalTitle }}" loading="lazy" decoding="async">
            </div>
        @endif

        <div class="frontend-cta-copy">
            <span>Đồng hành cùng CNETPOS</span>
            <h3>{{ $finalTitle }}</h3>
            <p>{!! $finalDescription !!}</p>
            <a href="{{ $finalLink }}">
                {{ $buttonLabel }} <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </section>
</div>
