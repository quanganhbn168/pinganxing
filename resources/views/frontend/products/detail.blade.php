@extends('layouts.master')
@section('title', $product->meta_title ?? $product->name)
@section('meta_description', $product->meta_description ?? Str::limit(strip_tags($product->description), 155))
@section('meta_image', $product->image?->url ?: ($product->image ? $product->image?->url : ''))

@push('jsonld')
<script type="application/ld+json">
{
  "@context": "https://schema.org/",
  "@type": "Product",
  "name": "{{ $product->name }}",
  "image": [
    "{{ $product->image?->url ?: ($product->image ? $product->image?->url : asset('images/setting/no-image.png')) }}"
   ],
  "description": "{{ $product->meta_description ?? Str::limit(strip_tags($product->description), 155) }}",
  "sku": "{{ $product->code ?? '' }}",
  "brand": {
    "@type": "Brand",
    "name": "{{ $product->brand->name ?? $setting->site_name }}"
  },
  "offers": {
    "@type": "Offer",
    "url": "{{ url()->current() }}",
    "priceCurrency": "VND",
    "price": "{{ $product->price }}",
    "availability": "{{ $product->stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock' }}",
    "itemCondition": "https://schema.org/NewCondition"
  }
}
</script>
@endpush

@push('css')
<style>
    .gallery-thumbs .swiper-slide {
        opacity: 0.5;
        cursor: pointer;
        transition: all 0.2s;
        border: 2px solid transparent;
        border-radius: 6px;
    }
    .gallery-thumbs .swiper-slide-thumb-active {
        opacity: 1;
        border-color: #2563eb; /* blue-600 */
    }
</style>
@endpush

@section('content')
@php
    $productBreadcrumbs = [
        ['label' => 'Sản phẩm', 'url' => route('products.index')],
    ];

    if ($product->category) {
        $productBreadcrumbs[] = ['label' => $product->category->name, 'url' => $product->category->slug_url];
    }

    $productBreadcrumbs[] = ['label' => $product->name];
@endphp

<div id="product-detail" class="bg-white dark:bg-gray-900 py-10 scroll-mt-24">
    <div class="max-w-screen-xl mx-auto px-4">
        <nav class="mb-8 text-sm font-semibold text-gray-500 dark:text-gray-400" aria-label="Breadcrumb">
            <ol class="flex flex-wrap items-center gap-2">
                <li>
                    <a href="{{ url('/') }}" class="hover:text-brand-700 dark:hover:text-brand-400">Trang chủ</a>
                </li>
                @foreach($productBreadcrumbs as $breadcrumb)
                    <li class="text-gray-300 dark:text-gray-600">/</li>
                    <li>
                        @if(!empty($breadcrumb['url']))
                            <a href="{{ $breadcrumb['url'] }}" class="hover:text-brand-700 dark:hover:text-brand-400">
                                {{ $breadcrumb['label'] }}
                            </a>
                        @else
                            <span class="text-gray-900 dark:text-white">{{ $breadcrumb['label'] }}</span>
                        @endif
                    </li>
                @endforeach
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16">

            {{-- CỘT TRÁI: ẢNH SẢN PHẨM --}}
            <div>
                <div class="bg-gray-50 dark:bg-gray-800 rounded-2xl p-4 border border-gray-200 dark:border-gray-700 mb-4">
                    <div class="swiper main-slider aspect-square">
                        <div class="swiper-wrapper">
                            @php
                                $images = collect($product->gallery)->filter();
                                if($images->isEmpty()){
                                    if($product->image) $images->push($product->image);
                                    elseif($product->banner) $images->push($product->banner);
                                }
                            @endphp

                            @if($images->isEmpty())
                                <div class="swiper-slide flex items-center justify-center">
                                    <img src="{{ asset('images/setting/no-image.png') }}" alt="No Image" class="max-w-full max-h-full object-contain mix-blend-multiply dark:mix-blend-normal">
                                </div>
                            @else
                                @foreach ($images as $image)
                                    <div class="swiper-slide flex items-center justify-center">
                                        <img src="{{ (is_string($image) ? asset($image) : optional($image)->url) ?? asset('images/setting/no-image.png') }}"
                                             alt="{{ $product->name }}"
                                             class="max-w-full max-h-full object-contain mix-blend-multiply dark:mix-blend-normal">
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <div class="gallery-custom-next absolute top-1/2 -translate-y-1/2 right-4 z-10 w-10 h-10 bg-white/80 border border-gray-200 rounded-full shadow-md hover:bg-brand-600 focus:outline-none hover:text-white text-gray-600 transition-colors flex items-center justify-center cursor-pointer">
                            <i class="fas fa-chevron-right text-sm"></i>
                        </div>
                        <div class="gallery-custom-prev absolute top-1/2 -translate-y-1/2 left-4 z-10 w-10 h-10 bg-white/80 border border-gray-200 rounded-full shadow-md hover:bg-brand-600 focus:outline-none hover:text-white text-gray-600 transition-colors flex items-center justify-center cursor-pointer">
                            <i class="fas fa-chevron-left text-sm"></i>
                        </div>
                    </div>
                </div>

                {{-- Thumbnail Slider --}}
                @if($images->count() > 1)
                <div class="swiper gallery-thumbs h-20 md:h-24">
                    <div class="swiper-wrapper">
                        @foreach ($images as $image)
                            <div class="swiper-slide bg-gray-50 dark:bg-gray-800 p-2">
                                <img src="{{ (is_string($image) ? asset($image) : optional($image)->url) ?? asset('images/setting/no-image.png') }}"
                                     alt="Thumb" class="w-full h-full object-contain mix-blend-multiply dark:mix-blend-normal">
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            {{-- CỘT PHẢI: THÔNG TIN SẢN PHẨM --}}
            <div class="flex flex-col">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    {{ $product->name }}
                </h1>

                <div class="text-sm text-gray-500 dark:text-gray-400 mb-6 flex items-center">
                    <span class="bg-gray-100 dark:bg-gray-800 px-3 py-1 rounded-full font-medium">
                        Mã SKU: <strong class="text-gray-900 dark:text-white ml-1">{{ $product->code ?? 'Đang cập nhật' }}</strong>
                    </span>
                </div>

                <div id="product-price-box" class="text-4xl font-bold text-blue-700 dark:text-blue-500 mb-8">
                    @if($product->price > 0)
                        <span id="product-current-price">{{ number_format($product->price) }}</span> <span class="text-2xl font-normal underline">đ</span>

                    @else
                        <span id="product-current-price" class="text-red-500">Liên hệ báo giá</span>
                    @endif
                    <div id="product-compare-price" class="text-base font-normal text-gray-500 line-through mt-1 hidden"></div>
                    <div class="text-sm font-normal text-gray-500 italic mt-1">
    (giá chưa bao gồm VAT)
</div>
                </div>

                <div class="mb-8">
                    @if($product->has_variants && ! empty($variantOptions))
                        <div class="space-y-4 mb-5">
                            @foreach($variantOptions as $attributeId => $meta)
                                <div>
                                    <div class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ $meta['name'] }}</div>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($meta['values'] as $value)
                                            <button
                                                type="button"
                                                class="variant-option px-3 py-1.5 rounded-lg border border-gray-300 text-sm text-gray-700 hover:border-blue-500 hover:text-blue-600 transition-colors"
                                                data-attribute-id="{{ $attributeId }}"
                                                data-value="{{ $value }}"
                                            >
                                                {{ $value }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div id="variant-status" class="text-sm text-gray-500 mb-4">Vui lòng chọn biến thể phù hợp.</div>
                    @endif

                    <div class="flex items-center gap-3 mb-4">
                        <label for="product-qty" class="text-sm font-semibold text-gray-700 dark:text-gray-300">Số lượng</label>
                        <div class="inline-flex items-center border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                            <button type="button" class="px-3 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800" id="product-qty-minus">-</button>
                            <input id="product-qty" type="number" min="1" value="1" class="w-16 text-center border-0 focus:ring-0 bg-transparent text-gray-900 dark:text-white">
                            <button type="button" class="px-3 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800" id="product-qty-plus">+</button>
                        </div>
                    </div>
                    <button
                        type="button"
                        class="btn-add-to-cart inline-flex items-center justify-center px-6 py-3 text-base font-bold text-white bg-brand-600 hover:bg-brand-700 rounded-xl transition-colors shadow-md"
                        data-id="{{ $product->id }}"
                        data-quantity="1"
                        data-variant-id=""
                    >
                        <i class="fas fa-cart-plus mr-2"></i>
                        Thêm vào giỏ hàng
                    </button>
                </div>

                <div class="prose prose-sm md:prose-base text-gray-600 dark:text-gray-300 dark:prose-invert mb-8">
                    {!! nl2br($product->description) !!}
                </div>

                {{-- Khối Liên hệ --}}
                <div class="bg-blue-50 dark:bg-gray-800/50 rounded-2xl p-6 border border-blue-100 dark:border-gray-700 mt-auto">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                        Cần hỗ trợ hoặc mua hàng?
                    </h3>
                    <div class="flex flex-col sm:flex-row gap-4 mb-4">
                        <a href="/lien-he" class="flex-1 inline-flex items-center justify-center px-6 py-3.5 text-base font-bold text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 rounded-xl transition-colors shadow-lg shadow-blue-500/30">
                            <i class="fas fa-paper-plane mr-2"></i> Gửi yêu cầu ngay
                        </a>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <a href="{{ $setting->zalo }}" target="_blank" class="flex items-center justify-center px-4 py-3 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl hover:border-blue-500 dark:hover:border-blue-400 transition-colors group">
                            <img src="{{ asset('images/setting/Icon_of_Zalo.svg') }}" alt="Zalo" class="w-6 h-6 mr-2 group-hover:scale-110 transition-transform">
                            <span class="font-medium text-gray-700 dark:text-gray-200">Chat Zalo</span>
                        </a>
                        <a href="tel:{{ $setting->phone }}" class="flex items-center justify-center px-4 py-3 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl hover:border-blue-500 dark:hover:border-blue-400 transition-colors group">
                            <i class="fas fa-phone-alt text-lg text-blue-600 dark:text-blue-400 mr-2 group-hover:animate-bounce"></i>
                            <span class="font-medium text-gray-700 dark:text-gray-200">{{ $setting->phone }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- PHẦN TABS THÔNG TIN CHI TIẾT --}}
        <div class="mt-16">
            <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="productTab" data-tabs-toggle="#productTabContent" role="tablist">
                    <li class="mr-2" role="presentation">
                        <button class="inline-block p-4 border-b-2 rounded-t-lg aria-selected:border-blue-600 aria-selected:text-blue-600 text-gray-500 border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300 transition-colors uppercase font-bold text-base md:text-lg" id="desc-tab" data-tabs-target="#desc" type="button" role="tab" aria-controls="desc" aria-selected="true">
                            Mô tả chi tiết
                        </button>
                    </li>
                    <li class="mr-2" role="presentation">
                        <button class="inline-block p-4 border-b-2 rounded-t-lg aria-selected:border-blue-600 aria-selected:text-blue-600 text-gray-500 border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300 transition-colors uppercase font-bold text-base md:text-lg" id="specs-tab" data-tabs-target="#specs" type="button" role="tab" aria-controls="specs" aria-selected="false">
                            Thông số kỹ thuật
                        </button>
                    </li>
                </ul>
            </div>
            <div id="productTabContent" class="bg-white dark:bg-gray-800 p-6 md:p-10 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="hidden" id="desc" role="tabpanel" aria-labelledby="desc-tab">
                    <div class="prose prose-lg max-w-none prose-blue dark:prose-invert">
                        @if($product->content) {!! $product->content !!} @else <p class="text-gray-500 italic">Nội dung đang cập nhật...</p> @endif
                    </div>
                </div>
                <div class="hidden" id="specs" role="tabpanel" aria-labelledby="specs-tab">
                    <div class="prose prose-lg max-w-none prose-blue dark:prose-invert">
                        @if($product->specifications) {!! $product->specifications !!} @else <p class="text-gray-500 italic">Nội dung đang cập nhật...</p> @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- BÌNH LUẬN & ĐÁNH GIÁ --}}
        <div class="mt-16 bg-white dark:bg-gray-800 p-6 md:p-10 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <x-comment-list :comments="$product->approvedComments" />
            <x-comment-form :commentable="$product" type="product" />
        </div>

        {{-- SẢN PHẨM LIÊN QUAN --}}
        @if(isset($relatedProducts) && $relatedProducts->count() > 0)
        <div class="mt-20">
            <div class="flex items-center justify-between mb-8 border-b-2 border-gray-100 dark:border-gray-700 pb-2">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white uppercase relative inline-block">
                    Sản phẩm liên quan
                    <div class="absolute -bottom-[4px] left-0 w-16 h-1 bg-blue-600 rounded-r-full"></div>
                </h2>
            </div>

            <div class="relative px-0 md:px-10">
                <div class="swiper related-product-slider overflow-hidden py-4 -my-4">
                    <div class="swiper-wrapper">
                        @foreach ($relatedProducts as $relProduct)
                            <div class="swiper-slide">
                                <div class="h-full">
                                    @include('partials.frontend.product_item', ['product' => $relProduct])
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="related-custom-prev absolute top-1/2 -translate-y-1/2 -left-5 z-10 w-10 h-10 bg-white border border-gray-200 rounded-full shadow-md hover:bg-brand-600 focus:outline-none hover:text-white text-brand-600 transition-colors hidden md:flex items-center justify-center cursor-pointer">
                    <i class="fas fa-chevron-left text-sm"></i>
                </div>
                <div class="related-custom-next absolute top-1/2 -translate-y-1/2 -right-5 z-10 w-10 h-10 bg-white border border-gray-200 rounded-full shadow-md hover:bg-brand-600 focus:outline-none hover:text-white text-brand-600 transition-colors hidden md:flex items-center justify-center cursor-pointer">
                    <i class="fas fa-chevron-right text-sm"></i>
                </div>
            </div>
        </div>
        @endif

        {{-- Footer Info --}}
        <div class="mt-16 bg-blue-50 dark:bg-gray-800 p-8 rounded-2xl text-center border border-blue-100 dark:border-gray-700">
            <p class="text-lg font-bold text-blue-900 dark:text-blue-400 mb-3">TRÂN TRỌNG CẢM ƠN QUÝ KHÁCH ĐÃ QUAN TÂM!</p>
            <div class="flex flex-col md:flex-row items-center justify-center gap-4 text-gray-600 dark:text-gray-300 font-medium">
                <span class="flex items-center"><i class="fas fa-map-marker-alt text-blue-600 mr-2"></i> {{ $setting->address }}</span>
                <span class="hidden md:inline text-gray-300">|</span>
                <span class="flex items-center"><i class="fas fa-phone-alt text-blue-600 mr-2"></i> Hotline: {{ $setting->phone }}</span>
            </div>
        </div>

    </div>
</div>
@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const variantMatrix = @json($variantMatrix ?? []);
        const hasVariants = @json((bool) ($product->has_variants ?? false));
        const defaultVariantId = @json($defaultVariantId);
        // 1. Slider Main & Thumbs cho Gallery SP
        const thumbsEl = document.querySelector('.gallery-thumbs');
        const mainEl = document.querySelector('.main-slider');
        let thumbnailSlider = null;

        if (thumbsEl) {
            thumbnailSlider = new Swiper(thumbsEl, {
                spaceBetween: 10,
                slidesPerView: 4,
                freeMode: true,
                watchSlidesProgress: true,
                breakpoints: { 640: { slidesPerView: 5 } }
            });
        }

        if (mainEl) {
            new Swiper(mainEl, {
                spaceBetween: 10,
                navigation: {
                    nextEl: mainEl.querySelector('.gallery-custom-next'),
                    prevEl: mainEl.querySelector('.gallery-custom-prev')
                },
                thumbs: thumbnailSlider ? { swiper: thumbnailSlider } : null,
            });
        }

        // 2. Slider SP liên quan
        const relSliderEl = document.querySelector('.related-product-slider');
        if (relSliderEl) {
            new Swiper(relSliderEl, {
                slidesPerView: 2,
                spaceBetween: 16,
                navigation: {
                    nextEl: relSliderEl.parentElement.querySelector('.related-custom-next'),
                    prevEl: relSliderEl.parentElement.querySelector('.related-custom-prev'),
                },
                breakpoints: {
                    640:  { slidesPerView: 2, spaceBetween: 20 },
                    768:  { slidesPerView: 3, spaceBetween: 24 },
                    1024: { slidesPerView: 4, spaceBetween: 24 }
                }
            });
        }

        const qtyInput = document.getElementById('product-qty');
        const qtyPlus = document.getElementById('product-qty-plus');
        const qtyMinus = document.getElementById('product-qty-minus');
        const addToCartBtn = document.querySelector('.btn-add-to-cart[data-id="{{ $product->id }}"]');
        const variantButtons = Array.from(document.querySelectorAll('.variant-option'));
        const variantStatus = document.getElementById('variant-status');
        const priceEl = document.getElementById('product-current-price');
        const comparePriceEl = document.getElementById('product-compare-price');
        const selectedOptions = {};

        const syncQuantity = () => {
            if (!qtyInput || !addToCartBtn) return;
            const quantity = Math.max(1, parseInt(qtyInput.value || '1', 10) || 1);
            qtyInput.value = quantity;
            addToCartBtn.dataset.quantity = String(quantity);
        };

        qtyPlus?.addEventListener('click', function () {
            if (!qtyInput) return;
            qtyInput.value = String((parseInt(qtyInput.value || '1', 10) || 1) + 1);
            syncQuantity();
        });

        qtyMinus?.addEventListener('click', function () {
            if (!qtyInput) return;
            const nextValue = Math.max(1, (parseInt(qtyInput.value || '1', 10) || 1) - 1);
            qtyInput.value = String(nextValue);
            syncQuantity();
        });

        qtyInput?.addEventListener('change', syncQuantity);
        syncQuantity();

        const formatMoney = (value) => Number(value || 0).toLocaleString('vi-VN');

        const updateVariantSelectionUI = () => {
            variantButtons.forEach((button) => {
                const attributeId = button.dataset.attributeId;
                const isSelected = selectedOptions[attributeId] === button.dataset.value;

                button.classList.toggle('bg-blue-600', isSelected);
                button.classList.toggle('text-white', isSelected);
                button.classList.toggle('border-blue-600', isSelected);
                button.classList.toggle('text-gray-700', !isSelected);
            });
        };

        const resolveVariant = () => {
            const signature = Object.keys(selectedOptions)
                .sort((a, b) => Number(a) - Number(b))
                .map((key) => `${key}=${selectedOptions[key]}`)
                .join('|');

            return variantMatrix[signature] ?? null;
        };

        const applyVariantData = (variant) => {
            if (!hasVariants || !addToCartBtn) return;

            if (!variant) {
                addToCartBtn.dataset.variantId = '';
                addToCartBtn.disabled = true;
                addToCartBtn.classList.add('opacity-60', 'cursor-not-allowed');
                if (variantStatus) variantStatus.textContent = 'Biến thể chưa hợp lệ hoặc chưa có trong kho cấu hình.';
                return;
            }

            addToCartBtn.dataset.variantId = String(variant.id);
            addToCartBtn.disabled = false;
            addToCartBtn.classList.remove('opacity-60', 'cursor-not-allowed');

            if (priceEl) {
                priceEl.textContent = formatMoney(variant.price);
                priceEl.classList.remove('text-red-500');
            }

            if (comparePriceEl) {
                if (variant.compare_at_price && Number(variant.compare_at_price) > Number(variant.price)) {
                    comparePriceEl.textContent = `${formatMoney(variant.compare_at_price)} đ`;
                    comparePriceEl.classList.remove('hidden');
                } else {
                    comparePriceEl.classList.add('hidden');
                    comparePriceEl.textContent = '';
                }
            }

            if (variantStatus) {
                variantStatus.textContent = `SKU: ${variant.sku ?? 'N/A'} • Tồn kho: ${variant.stock ?? 0}`;
            }
        };

        variantButtons.forEach((button) => {
            button.addEventListener('click', () => {
                selectedOptions[button.dataset.attributeId] = button.dataset.value;
                updateVariantSelectionUI();
                applyVariantData(resolveVariant());
            });
        });

        if (hasVariants) {
            if (defaultVariantId) {
                const defaultEntry = Object.entries(variantMatrix).find(([, value]) => Number(value.id) === Number(defaultVariantId));
                if (defaultEntry) {
                    const [signature, variant] = defaultEntry;
                    signature.split('|').forEach((pair) => {
                        const [key, value] = pair.split('=');
                        selectedOptions[key] = value;
                    });
                    updateVariantSelectionUI();
                    applyVariantData(variant);
                }
            } else {
                applyVariantData(null);
            }
        }
    });
</script>
@endpush
