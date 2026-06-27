@extends('layouts.master')
@section('title', $service->name)
@section('meta_description', Str::limit(strip_tags($service->description ?? $service->content), 160))
@section('meta_image', $serviceImages->first() ?? $serviceCoverImage ?? '')

@push('css')
    <style>
        .service-detail-intro {
            display: grid;
            grid-template-columns: 11rem minmax(0, 1fr);
            align-items: center;
            gap: 1.4rem;
            padding: .9rem;
            border: 1px solid rgba(14, 74, 134, .12);
            border-radius: 1.5rem;
            background: linear-gradient(135deg, #f8fbff 0%, #fff 68%);
            box-shadow: 0 16px 42px rgba(15, 23, 42, .08);
        }

        .service-detail-cover {
            position: relative;
            aspect-ratio: 4 / 3;
            overflow: hidden;
            border: 4px solid #fff;
            border-radius: 1.15rem;
            background: #e8eff7;
            box-shadow: 0 12px 28px rgba(14, 74, 134, .2);
            isolation: isolate;
        }

        .service-detail-cover-backdrop {
            position: absolute;
            inset: -12%;
            z-index: -1;
            background-position: center;
            background-size: cover;
            filter: blur(14px);
            opacity: .42;
            transform: scale(1.08);
        }

        .service-detail-cover img {
            width: 100%;
            height: 100%;
            padding: .38rem;
            border-radius: .95rem;
            object-fit: contain;
        }

        .service-detail-cover-placeholder {
            display: flex;
            width: 100%;
            height: 100%;
            align-items: center;
            justify-content: center;
            background: linear-gradient(145deg, #e0effe, #f8fbff);
            color: #0e4a86;
            font-size: 2rem;
        }

        .service-detail-cover-badge {
            position: absolute;
            z-index: 2;
            right: .35rem;
            bottom: .35rem;
            display: inline-flex;
            width: 2.1rem;
            height: 2.1rem;
            align-items: center;
            justify-content: center;
            border: 4px solid #fff;
            border-radius: 999px;
            background: #f39221;
            color: #fff;
            font-size: .7rem;
            box-shadow: 0 8px 18px rgba(243, 146, 33, .28);
        }

        .service-detail-title {
            color: #0f172a;
            font-size: clamp(1.75rem, 3.2vw, 2.5rem);
            font-weight: 800;
            line-height: 1.25;
            letter-spacing: -.025em;
        }

        .service-detail-gallery-shell {
            position: relative;
            padding: .55rem;
            overflow: hidden;
            border: 1px solid rgba(14, 74, 134, .12);
            border-radius: 1.65rem;
            background: radial-gradient(circle at 100% 0, rgba(243, 146, 33, .12), transparent 34%), linear-gradient(145deg, #f8fbff 0%, #fff 72%);
            box-shadow: 0 24px 65px rgba(15, 23, 42, .12);
        }

        .service-detail-gallery-main {
            overflow: hidden;
            border-radius: 1.25rem;
            background: #e8eff7;
            isolation: isolate;
        }

        .service-detail-gallery-main .swiper-slide {
            aspect-ratio: 16 / 10;
            overflow: hidden;
            background: #e8eff7;
        }

        .service-detail-gallery-main .swiper-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .9s cubic-bezier(.2, .7, .2, 1);
        }

        .service-detail-gallery-main:hover .swiper-slide-active img {
            transform: scale(1.025);
        }

        .service-detail-gallery-shade {
            position: absolute;
            z-index: 2;
            right: 0;
            bottom: 0;
            left: 0;
            height: 38%;
            pointer-events: none;
            background: linear-gradient(180deg, transparent, rgba(4, 24, 43, .78));
        }

        .service-detail-gallery-caption {
            position: absolute;
            z-index: 3;
            right: 1.25rem;
            bottom: 1.15rem;
            left: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            color: #fff;
            font-size: .82rem;
            font-weight: 800;
            letter-spacing: .02em;
        }

        .service-detail-gallery-caption > span:first-child {
            display: inline-flex;
            align-items: center;
            gap: .55rem;
        }

        .service-detail-gallery-count {
            min-width: 3.65rem;
            padding: .42rem .7rem;
            border: 1px solid rgba(255, 255, 255, .3);
            border-radius: 999px;
            background: rgba(7, 35, 61, .46);
            text-align: center;
            font-variant-numeric: tabular-nums;
            backdrop-filter: blur(10px);
        }

        .service-detail-gallery-count strong {
            color: #ffc46a;
        }

        .service-detail-gallery-nav {
            position: absolute;
            z-index: 4;
            top: 50%;
            display: inline-flex;
            width: 2.85rem;
            height: 2.85rem;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255, 255, 255, .54);
            border-radius: 999px;
            background: rgba(255, 255, 255, .9);
            color: #0b3762;
            box-shadow: 0 12px 28px rgba(7, 35, 61, .2);
            transform: translateY(-50%);
            transition: color .2s ease, background .2s ease, transform .2s ease;
            backdrop-filter: blur(10px);
        }

        .service-detail-gallery-prev { left: 1rem; }
        .service-detail-gallery-next { right: 1rem; }

        .service-detail-gallery-nav:hover {
            background: #0e4a86;
            color: #fff;
            transform: translateY(-50%) scale(1.06);
        }

        .service-detail-gallery-nav:focus-visible {
            outline: 3px solid rgba(255, 196, 106, .8);
            outline-offset: 3px;
        }

        .service-detail-gallery-thumbs {
            margin-top: .55rem;
            padding: .25rem;
        }

        .service-detail-gallery-thumbs .swiper-slide {
            height: 4.9rem;
            padding: .2rem;
            overflow: hidden;
            border: 2px solid transparent;
            border-radius: .9rem;
            background: #fff;
            opacity: .58;
            cursor: pointer;
            transition: border-color .2s ease, opacity .2s ease, transform .2s ease;
        }

        .service-detail-gallery-thumbs .swiper-slide:hover {
            opacity: .9;
            transform: translateY(-2px);
        }

        .service-detail-gallery-thumbs .swiper-slide-thumb-active {
            border-color: #f39221;
            opacity: 1;
            box-shadow: 0 6px 16px rgba(243, 146, 33, .2);
        }

        .service-detail-gallery-thumbs img {
            width: 100%;
            height: 100%;
            border-radius: .58rem;
            object-fit: cover;
        }

        .service-detail-gallery-empty {
            display: flex;
            aspect-ratio: 16 / 8;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: .8rem;
            border: 1px dashed rgba(14, 74, 134, .28);
            border-radius: 1.5rem;
            background: radial-gradient(circle at 20% 15%, rgba(124, 193, 253, .26), transparent 32%), linear-gradient(145deg, #f0f7ff, #fff);
            color: #64748b;
            font-size: .9rem;
            font-weight: 700;
        }

        .service-detail-gallery-empty span {
            display: inline-flex;
            width: 4rem;
            height: 4rem;
            align-items: center;
            justify-content: center;
            border-radius: 1.2rem;
            background: #fff;
            color: #0e4a86;
            font-size: 1.5rem;
            box-shadow: 0 14px 32px rgba(14, 74, 134, .12);
        }

        @media (max-width: 639px) {
            .service-detail-intro {
                grid-template-columns: 6.25rem minmax(0, 1fr);
                gap: .85rem;
                padding: .65rem;
                border-radius: 1.2rem;
            }

            .service-detail-cover {
                aspect-ratio: 1;
                border-width: 3px;
                border-radius: .95rem;
            }

            .service-detail-cover img {
                padding: .25rem;
            }

            .service-detail-cover-badge {
                width: 1.75rem;
                height: 1.75rem;
                border-width: 3px;
                font-size: .58rem;
            }

            .service-detail-kicker {
                margin-bottom: .55rem;
                padding: .45rem .65rem;
                font-size: .6rem;
                letter-spacing: .08em;
            }

            .service-detail-title {
                font-size: clamp(1.25rem, 6vw, 1.65rem);
                line-height: 1.3;
            }

            .service-detail-gallery-shell { padding: .4rem; border-radius: 1.3rem; }
            .service-detail-gallery-main { border-radius: 1rem; }
            .service-detail-gallery-main .swiper-slide { aspect-ratio: 4 / 3; }
            .service-detail-gallery-nav { width: 2.45rem; height: 2.45rem; }
            .service-detail-gallery-prev { left: .65rem; }
            .service-detail-gallery-next { right: .65rem; }
            .service-detail-gallery-caption { right: .85rem; bottom: .8rem; left: .85rem; font-size: .75rem; }
            .service-detail-gallery-thumbs .swiper-slide { height: 4.15rem; border-radius: .75rem; }
        }

        @media (prefers-reduced-motion: reduce) {
            .service-detail-gallery-main .swiper-slide img,
            .service-detail-gallery-nav,
            .service-detail-gallery-thumbs .swiper-slide {
                transition: none;
            }
        }
    </style>
@endpush

@section('content')
    <div class="bg-white min-h-screen pt-24 pb-12">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">
            <!-- Breadcrumbs -->
            <div class="text-sm text-slate-500 mb-6">
                <a href="{{ route('home') }}" class="hover:text-primary">Trang chủ</a>
                <span class="mx-2">/</span>
                <a href="{{ route('frontend.services.index') }}" class="hover:text-primary">Dịch vụ</a>
                @if($service->category)
                    <span class="mx-2">/</span>
                    <a href="{{ $service->category->slug_url }}" class="hover:text-primary">{{ $service->category->name }}</a>
                @endif
                <span class="mx-2">/</span>
                <span class="text-slate-900">{{ $service->name }}</span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <section class="service-detail-intro mb-7" data-aos="fade-up">
                        <div class="service-detail-cover">
                            @if($serviceCoverImage)
                                <span class="service-detail-cover-backdrop"
                                    style="background-image: url('{{ $serviceCoverImage }}')" aria-hidden="true"></span>
                                <img src="{{ $serviceCoverImage }}" alt="Ảnh đại diện {{ $service->name }}"
                                    fetchpriority="high" decoding="async">
                            @else
                                <span class="service-detail-cover-placeholder">
                                    <i class="fas fa-concierge-bell" aria-hidden="true"></i>
                                </span>
                            @endif
                            <span class="service-detail-cover-badge" aria-hidden="true">
                                <i class="fas fa-check"></i>
                            </span>
                        </div>

                        <div>
                            <div class="service-detail-kicker mb-3 inline-flex items-center gap-2 rounded-full bg-brand-50 px-3.5 py-2 text-xs font-extrabold uppercase tracking-[0.14em] text-brand-700">
                                <i class="fas fa-compass" aria-hidden="true"></i>
                                <span>{{ $service->category?->name ?? 'Dịch vụ chuyên nghiệp' }}</span>
                            </div>
                            <h1 class="service-detail-title">{{ $service->name }}</h1>
                        </div>
                    </section>

                    @if($serviceImages->isNotEmpty())
                        <section class="service-detail-gallery-shell mb-8" aria-label="Thư viện ảnh {{ $service->name }}"
                            data-aos="fade-up">
                            <div class="swiper service-detail-gallery-main">
                                <div class="swiper-wrapper">
                                    @foreach($serviceImages as $image)
                                        <div class="swiper-slide">
                                            <img src="{{ $image }}"
                                                alt="{{ $service->name }}{{ $serviceImages->count() > 1 ? ' - ảnh ' . ($loop->index + 1) : '' }}"
                                                @if($loop->first) fetchpriority="high" @else loading="lazy" @endif
                                                decoding="async">
                                        </div>
                                    @endforeach
                                </div>

                                <div class="service-detail-gallery-shade" aria-hidden="true"></div>
                                <div class="service-detail-gallery-caption">
                                    <span><i class="far fa-images" aria-hidden="true"></i> Hình ảnh dịch vụ</span>
                                    @if($serviceImages->count() > 1)
                                        <span class="service-detail-gallery-count">
                                            <strong data-gallery-current>1</strong> / {{ $serviceImages->count() }}
                                        </span>
                                    @endif
                                </div>

                                @if($serviceImages->count() > 1)
                                    <button type="button" class="service-detail-gallery-nav service-detail-gallery-prev"
                                        aria-label="Xem ảnh trước">
                                        <i class="fas fa-chevron-left" aria-hidden="true"></i>
                                    </button>
                                    <button type="button" class="service-detail-gallery-nav service-detail-gallery-next"
                                        aria-label="Xem ảnh tiếp theo">
                                        <i class="fas fa-chevron-right" aria-hidden="true"></i>
                                    </button>
                                @endif
                            </div>

                            @if($serviceImages->count() > 1)
                                <div class="swiper service-detail-gallery-thumbs" aria-label="Chọn ảnh muốn xem">
                                    <div class="swiper-wrapper">
                                        @foreach($serviceImages as $image)
                                            <div class="swiper-slide">
                                                <img src="{{ $image }}" alt="Ảnh thu nhỏ {{ $loop->index + 1 }}"
                                                    loading="lazy" decoding="async">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </section>
                    @endif

                    @if($service->description)
                        <div
                            class="text-lg text-slate-600 font-medium leading-relaxed mb-8 bg-slate-50 p-6 rounded-xl border border-slate-100 border-l-4 border-l-primary">
                            {{ $service->description }}
                        </div>
                    @endif

                    <!-- Content -->
                    <div class="prose prose-slate max-w-none prose-img:rounded-xl mb-12">
                        {!! $service->content !!}
                    </div>
                </div>

                <!-- Sidebar / CTA Box -->
                <div class="lg:col-span-1">
                    <div
                        class="sticky top-28 bg-white border border-slate-200 shadow-xl shadow-slate-200/50 rounded-2xl p-6">
                        <div class="text-center mb-6">
                            <h3 class="text-xl font-bold text-slate-900 mb-2">Đăng ký dịch vụ</h3>
                            <p class="text-sm text-slate-500">Để lại thông tin, chuyên viên của chúng tôi sẽ liên hệ tư vấn
                                chi tiết cho bạn.</p>
                        </div>

                        <a href="{{ route('contact.show') }}"
                            class="block w-full py-4 bg-primary text-white text-center font-bold text-lg rounded-xl shadow-lg shadow-primary/30 hover:bg-dark-primary transition-colors mb-4">
                            <i class="fas fa-paper-plane mr-2"></i> Yêu cầu tư vấn
                        </a>
                        <a href="tel:{{ $setting->phone ?? '19001234' }}"
                            class="block w-full py-3.5 bg-slate-50 text-slate-700 text-center font-bold border border-slate-200 rounded-xl hover:bg-slate-100 transition-colors">
                            <i class="fas fa-phone-alt text-primary mr-2"></i> Gọi tư vấn:
                            {{ $setting->phone ?? '1900 1234' }}
                        </a>

                        <hr class="my-6 border-slate-100">
                        <div class="text-sm text-slate-600 space-y-3">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-check-circle text-primary mt-1"></i>
                                <div><b>Uy tín & Chuyên nghiệp</b><br>Dịch vụ chất lượng cao</div>
                            </div>
                            <div class="flex items-start gap-3">
                                <i class="fas fa-headset text-primary mt-1"></i>
                                <div><b>Hỗ trợ tận tâm</b><br>Xử lý nhanh chóng 24/7</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Services -->
            @if(isset($relatedServices) && $relatedServices->count() > 0)
                <div class="mt-16 pt-12 border-t border-slate-100">
                    <h3 class="text-2xl font-bold text-slate-900 mb-6">Các dịch vụ liên quan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach($relatedServices as $relService)
                            <a href="{{ $relService->slug_url }}"
                                class="group block bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:border-primary/30 hover:shadow-xl hover:shadow-primary/5 transition-all">
                                <div class="relative aspect-video bg-slate-100 overflow-hidden">
                                    @if($relatedServiceImageUrls->get($relService->id))
                                        <img src="{{ $relatedServiceImageUrls->get($relService->id) }}" alt="{{ $relService->name }}"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    @else
                                        <div class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200">
                                            <i class="fas fa-concierge-bell text-4xl text-slate-400"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-5">
                                    <h3 class="font-extrabold text-slate-900 mb-2 line-clamp-2">{{ $relService->name }}</h3>
                                    <p class="text-sm text-slate-500 leading-6 line-clamp-2">
                                        {{ Str::limit(strip_tags($relService->description ?? $relService->content), 96) }}</p>
                                    <div class="mt-4 text-sm font-bold text-primary">
                                        Xem chi tiết <i class="fas fa-arrow-right text-xs ml-1"></i>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof Swiper === 'undefined') {
                return;
            }

            const galleryElement = document.querySelector('.service-detail-gallery-main');
            if (!galleryElement || galleryElement.querySelectorAll('.swiper-slide').length < 2) {
                return;
            }

            const thumbsElement = document.querySelector('.service-detail-gallery-thumbs');
            const currentElement = galleryElement.querySelector('[data-gallery-current]');
            const thumbs = thumbsElement ? new Swiper(thumbsElement, {
                slidesPerView: 3.4,
                spaceBetween: 10,
                watchSlidesProgress: true,
                freeMode: true,
                breakpoints: {
                    480: { slidesPerView: 4.3, spaceBetween: 12 },
                    768: { slidesPerView: 5.2, spaceBetween: 14 },
                },
            }) : null;

            const gallery = new Swiper(galleryElement, {
                slidesPerView: 1,
                spaceBetween: 16,
                speed: 650,
                rewind: true,
                grabCursor: true,
                navigation: {
                    nextEl: galleryElement.querySelector('.service-detail-gallery-next'),
                    prevEl: galleryElement.querySelector('.service-detail-gallery-prev'),
                },
                thumbs: thumbs ? { swiper: thumbs } : undefined,
                keyboard: {
                    enabled: true,
                    onlyInViewport: true,
                },
                on: {
                    slideChange: function () {
                        if (currentElement) {
                            currentElement.textContent = this.realIndex + 1;
                        }
                    },
                },
            });

            gallery.update();
        });
    </script>
@endpush
