@extends('layouts.master')
@section('title', $setting->company_name ?? 'VietJourney')
@section('meta_description', $setting->meta_description ?? '')

@section('content')

    <!-- Hero Slider -->
    <section class="relative">
        @include('partials.frontend.slide')

        <!-- Search Box -->
        <div id="booking" class="absolute left-0 right-0 -bottom-14 px-4 z-20">
            <form action="{{ route('frontend.search.post') ?? '#' }}" method="POST" class="max-w-6xl mx-auto bg-white rounded-2xl shadow-[0_20px_60px_rgba(15,23,42,0.08)] p-4" data-aos="fade-up" data-aos-delay="150">
                @csrf
                <div class="grid md:grid-cols-5 gap-3">
                    <div class="px-5 py-4 rounded-xl bg-slate-50">
                        <label class="text-xs text-slate-400 font-semibold">Bạn muốn đi đâu?</label>
                        <select name="destination" class="w-full bg-transparent mt-2 text-sm font-semibold outline-none border-none p-0 focus:ring-0 text-slate-700">
                            <option value="">Chọn điểm đến</option>
                            <option value="Hạ Long">Hạ Long</option>
                            <option value="Đà Nẵng">Đà Nẵng</option>
                            <option value="Phú Quốc">Phú Quốc</option>
                            <option value="Sapa">Sapa</option>
                        </select>
                    </div>

                    <div class="px-5 py-4 rounded-xl bg-slate-50">
                        <label class="text-xs text-slate-400 font-semibold">Ngày khởi hành</label>
                        <input name="date" type="date" class="w-full bg-transparent mt-2 text-sm font-semibold outline-none border-none p-0 focus:ring-0 text-slate-700">
                    </div>

                    <div class="px-5 py-4 rounded-xl bg-slate-50">
                        <label class="text-xs text-slate-400 font-semibold">Số ngày</label>
                        <select name="days" class="w-full bg-transparent mt-2 text-sm font-semibold outline-none border-none p-0 focus:ring-0 text-slate-700">
                            <option value="">Chọn số ngày</option>
                            <option value="2">2N1Đ</option>
                            <option value="3">3N2Đ</option>
                            <option value="4">4N3Đ</option>
                        </select>
                    </div>

                    <div class="px-5 py-4 rounded-xl bg-slate-50">
                        <label class="text-xs text-slate-400 font-semibold">Số khách</label>
                        <select name="guests" class="w-full bg-transparent mt-2 text-sm font-semibold outline-none border-none p-0 focus:ring-0 text-slate-700">
                            <option value="2">2 khách</option>
                            <option value="4">4 khách</option>
                            <option value="6">6 khách</option>
                            <option value="10">10 khách</option>
                        </select>
                    </div>

                    <button type="submit" class="rounded-xl bg-yellow-brand hover:bg-amber-300 transition text-slate-900 font-extrabold flex items-center justify-center gap-2 text-[15px]">
                        <i class="fas fa-search"></i> Tìm tour ngay
                    </button>
                </div>
            </form>
        </div>
    </section>

    <!-- Services -->
    @if(isset($homeServicesCategories) && $homeServicesCategories->count())
    <section id="services" class="py-24 bg-white relative z-10">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">
            <div class="mb-10 text-center md:text-left" data-aos="fade-up">
                <div class="text-xs uppercase tracking-[0.25em] font-extrabold text-yellow-brand mb-3">
                    Dịch vụ của chúng tôi
                </div>
                <h2 class="text-3xl md:text-4xl font-serif font-bold text-slate-900 max-w-xl" style="font-family: 'Playfair Display', serif;">
                    Chúng tôi mang đến cho bạn trải nghiệm trọn vẹn
                </h2>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-6 gap-5">
                @php
                    $bgColors = ['bg-teal-50', 'bg-sky-50', 'bg-violet-50', 'bg-emerald-50', 'bg-orange-50', 'bg-fuchsia-50'];
                    $icons = ['fa-suitcase-rolling text-teal-600', 'fa-plane text-sky-600', 'fa-hotel text-violet-600', 'fa-passport text-emerald-600', 'fa-car text-orange-600', 'fa-users text-fuchsia-600'];
                @endphp
                @foreach($homeServicesCategories->take(6) as $index => $service)
                    <a href="{{ $service->slug_url ?? '#' }}" class="service-card rounded-2xl p-6 min-h-[180px] {{ $bgColors[$index % 6] }} block" data-aos="fade-up" data-aos-delay="{{ $index * 50 }}">
                        <div class="text-4xl mb-5">
                            <i class="fas {{ $icons[$index % 6] }}"></i>
                        </div>
                        <h3 class="font-extrabold text-slate-900 mb-2">{{ $service->name }}</h3>
                        <p class="text-sm text-slate-500 leading-6">{{ Str::limit(strip_tags($service->description ?? $service->content), 60) }}</p>
                        <div class="mt-4 text-sm font-bold text-primary">
                            Xem chi tiết <i class="fas fa-arrow-right text-xs ml-1"></i>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Tours -->
    @if(isset($homeProducts) && $homeProducts->count())
    <section id="tours" class="py-12 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">
            <div class="flex items-end justify-between gap-5 mb-8" data-aos="fade-up">
                <div>
                    <div class="text-xs uppercase tracking-[0.25em] font-extrabold text-yellow-brand mb-3">
                        Tour nổi bật
                    </div>
                    <h2 class="text-3xl md:text-4xl font-serif font-bold text-slate-900" style="font-family: 'Playfair Display', serif;">
                        Những hành trình được yêu thích nhất
                    </h2>
                </div>
                <div class="hidden md:flex items-center gap-3">
                    <a href="{{ url('/tour') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full border border-slate-200 text-slate-600 text-sm font-bold hover:bg-slate-50 hover:text-primary transition-all mr-2">
                        Xem tất cả <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                    <div class="tour-swiper-prev w-10 h-10 rounded-full border border-slate-200 flex items-center justify-center text-slate-400 hover:bg-primary hover:text-white hover:border-primary cursor-pointer transition-colors shadow-sm">
                        <i class="fas fa-chevron-left"></i>
                    </div>
                    <div class="tour-swiper-next w-10 h-10 rounded-full border border-slate-200 flex items-center justify-center text-slate-400 hover:bg-primary hover:text-white hover:border-primary cursor-pointer transition-colors shadow-sm">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>
            </div>

            <div class="swiper tour-swiper pb-12" data-aos="fade-up">
                <div class="swiper-wrapper">
                    @foreach($homeProducts as $product)
                        <div class="swiper-slide h-auto">
                            @include('partials.frontend.tour-card', ['product' => $product])
                        </div>
                    @endforeach
                </div>
                <!-- Pagination for Mobile -->
                <div class="swiper-pagination !bottom-0"></div>
            </div>
            
            <div class="mt-4 text-center md:hidden">
                <a href="{{ route('products.index') ?? '#' }}" class="inline-flex items-center gap-2 text-primary font-bold text-sm bg-primary/10 px-6 py-3 rounded-full">
                    Xem tất cả <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>
    @endif

    <!-- Why Choose -->
    <section id="about" class="py-16">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">
            <div class="grid lg:grid-cols-4 gap-5">
                <div class="lg:row-span-2 bg-slate-50 rounded-3xl p-8 md:p-10" data-aos="fade-right">
                    <div class="text-xs uppercase tracking-[0.2em] font-extrabold text-yellow-brand mb-4">
                        Vì sao chọn {{ $setting->company_name ?? config('app.name') }}?
                    </div>
                    <h2 class="text-3xl font-serif font-bold text-slate-900 mb-5 leading-tight" style="font-family: 'Playfair Display', serif;">
                        Uy tín tạo nên thương hiệu
                    </h2>
                    <p class="text-slate-500 leading-7">
                        Với hơn 15 năm kinh nghiệm, chúng tôi cam kết mang đến hành trình chân thực, an toàn và đáng nhớ nhất cho mỗi khách hàng.
                    </p>
                    <a href="{{ route('frontend.intro.index') ?? '#' }}" class="inline-flex items-center gap-2 mt-8 px-6 py-3.5 rounded-xl bg-primary text-white font-bold hover:bg-dark-primary transition shadow-lg shadow-primary/30">
                        Tìm hiểu thêm <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="bg-primary text-white rounded-3xl p-8 text-center flex flex-col justify-center items-center" data-aos="fade-up">
                    <div class="text-4xl md:text-5xl font-extrabold mb-2">15+</div>
                    <p class="text-white/80 text-sm font-medium">Năm kinh nghiệm trong lĩnh vực du lịch</p>
                </div>

                <div class="bg-orange-50 rounded-3xl p-8 text-center flex flex-col justify-center items-center" data-aos="fade-up" data-aos-delay="50">
                    <div class="text-4xl md:text-5xl font-extrabold text-primary mb-2">10K+</div>
                    <p class="text-slate-600 text-sm font-medium">Khách hàng tin tưởng</p>
                </div>

                <div class="bg-teal-50 rounded-3xl p-8 text-center flex flex-col justify-center items-center" data-aos="fade-up" data-aos-delay="100">
                    <div class="text-4xl md:text-5xl font-extrabold text-primary mb-2">98%</div>
                    <p class="text-slate-600 text-sm font-medium">Khách hàng hài lòng</p>
                </div>

                <div class="bg-teal-50 rounded-3xl p-8 text-center flex flex-col justify-center items-center" data-aos="fade-up">
                    <div class="text-4xl md:text-5xl font-extrabold text-primary mb-2">100+</div>
                    <p class="text-slate-600 text-sm font-medium">Điểm đến hấp dẫn trong và ngoài nước</p>
                </div>

                <div class="bg-slate-50 rounded-3xl p-8 text-center flex flex-col justify-center items-center border border-slate-100" data-aos="fade-up" data-aos-delay="50">
                    <div class="text-4xl mb-3 text-primary"><i class="fas fa-user-tie"></i></div>
                    <p class="font-bold text-slate-900">Đội ngũ HDV chuyên nghiệp</p>
                </div>

                <div class="bg-yellow-50 rounded-3xl p-8 text-center flex flex-col justify-center items-center" data-aos="fade-up" data-aos-delay="100">
                    <div class="text-4xl mb-3 text-yellow-brand"><i class="fas fa-headset"></i></div>
                    <p class="font-bold text-slate-900">Hỗ trợ 24/7 mọi lúc mọi nơi</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Process -->
    <section class="py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">
            <div class="mb-12 text-center" data-aos="fade-up">
                <div class="text-xs uppercase tracking-[0.25em] font-extrabold text-yellow-brand mb-3">
                    Quy trình đặt tour
                </div>
                <h2 class="text-3xl md:text-4xl font-serif font-bold text-slate-900" style="font-family: 'Playfair Display', serif;">
                    Đơn giản – Nhanh chóng – An toàn
                </h2>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                @php
                    $steps = [
                        ['icon' => 'fa-compass', 'title' => 'Tìm tour', 'desc' => 'Chọn hành trình yêu thích từ hàng trăm lựa chọn đa dạng.'],
                        ['icon' => 'fa-clipboard-list', 'title' => 'Đăng ký', 'desc' => 'Điền thông tin đặt tour nhanh chóng và thuận tiện.'],
                        ['icon' => 'fa-credit-card', 'title' => 'Thanh toán', 'desc' => 'Thanh toán an toàn qua nhiều cổng giao dịch uy tín.'],
                        ['icon' => 'fa-suitcase-rolling', 'title' => 'Khởi hành', 'desc' => 'Xách balo lên và tận hưởng chuyến đi tuyệt vời cùng chúng tôi.'],
                    ];
                @endphp
                @foreach($steps as $index => $step)
                    <div class="relative bg-white rounded-2xl border border-slate-100 shadow-[0_14px_35px_rgba(15,23,42,0.06)] p-8 text-center md:text-left hover:-translate-y-2 transition-transform duration-300" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                        <div class="w-16 h-16 mx-auto md:mx-0 rounded-full bg-yellow-50 text-primary flex items-center justify-center text-2xl mb-6">
                            <i class="fas {{ $step['icon'] }}"></i>
                        </div>
                        <div class="absolute top-6 right-6 text-4xl font-black text-slate-50 opacity-50 select-none">0{{ $index + 1 }}</div>
                        <h3 class="text-xl font-extrabold text-slate-900 mb-3">{{ $step['title'] }}</h3>
                        <p class="text-sm text-slate-500 leading-relaxed">{{ $step['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Destinations -->
    @if(isset($homeCategories) && $homeCategories->count())
    <section id="destinations" class="py-16">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">
            <div class="flex items-end justify-between mb-10" data-aos="fade-up">
                <div>
                    <div class="text-xs uppercase tracking-[0.25em] font-extrabold text-yellow-brand mb-3">
                        Điểm đến nổi bật
                    </div>
                    <h2 class="text-3xl md:text-4xl font-serif font-bold text-slate-900" style="font-family: 'Playfair Display', serif;">
                        Khám phá những vùng đất tuyệt đẹp
                    </h2>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4">
                @foreach($homeCategories->take(8) as $category)
                    <a href="{{ $category->slug_url ?? '#' }}" class="destination-card relative h-36 md:h-40 rounded-2xl overflow-hidden group block" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                        <img src="{{ $category->image?->url ?? 'https://images.unsplash.com/photo-1528127269322-539801943592?q=80&w=400&auto=format&fit=crop' }}" alt="{{ $category->name }}" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-80 group-hover:opacity-100 transition-opacity"></div>
                        <div class="absolute left-3 bottom-3 right-3 text-white font-extrabold text-sm leading-tight text-shadow-sm">{{ $category->name }}</div>
                    </a>
                @endforeach
            </div>

            <!-- Video -->
            <div class="mt-12 relative rounded-3xl overflow-hidden min-h-[300px] md:min-h-[400px] shadow-2xl" data-aos="fade-up">
                <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=80&w=2000&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover" alt="{{ config('app.name') }} Video">
                <div class="absolute inset-0 bg-primary/70 mix-blend-multiply"></div>
                <div class="absolute inset-0 bg-gradient-to-r from-dark-primary/90 to-transparent"></div>
                
                <div class="absolute inset-0 flex flex-col md:flex-row md:items-center px-8 md:px-16 py-12">
                    <a href="https://www.youtube.com/watch?v=Scxs7L0vhZ4" data-fancybox class="glightbox-video w-20 h-20 md:w-24 md:h-24 rounded-full bg-white/20 hover:bg-white/30 backdrop-blur-md border-2 border-white/50 flex items-center justify-center text-white text-3xl shrink-0 transition-all hover:scale-110 mb-8 md:mb-0 shadow-[0_0_40px_rgba(255,255,255,0.2)]">
                        <i class="fas fa-play ml-1"></i>
                    </a>
                    <div class="md:ml-12 text-white">
                        <div class="text-xs uppercase tracking-[0.2em] font-bold text-yellow-brand mb-4">
                            {{ config('app.name') }} - Hơn cả một chuyến đi
                        </div>
                        <h3 class="text-3xl md:text-5xl font-serif font-bold max-w-2xl leading-tight mb-6" style="font-family: 'Playfair Display', serif;">
                            Cảm nhận vẻ đẹp Việt Nam qua từng thước phim
                        </h3>
                        <a href="https://www.youtube.com/watch?v=Scxs7L0vhZ4" data-fancybox class="inline-flex items-center gap-2 text-sm font-bold bg-white/10 px-5 py-2.5 rounded-full backdrop-blur-sm border border-white/20 hover:bg-white hover:text-primary transition-colors">
                            Xem video giới thiệu <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Reviews -->
    @if(isset($testimonials) && $testimonials->count())
    <section class="py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">
            <div class="mb-12 text-center" data-aos="fade-up">
                <div class="text-xs uppercase tracking-[0.25em] font-extrabold text-yellow-brand mb-3">
                    Khách hàng nói về chúng tôi
                </div>
                <h2 class="text-3xl md:text-4xl font-serif font-bold text-slate-900" style="font-family: 'Playfair Display', serif;">
                    Những trải nghiệm đáng nhớ
                </h2>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($testimonials->take(4) as $review)
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-[0_10px_40px_rgba(15,23,42,0.04)] p-8 flex flex-col h-full hover:shadow-[0_20px_50px_rgba(15,23,42,0.08)] transition-shadow duration-300" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                        <div class="flex justify-between items-center mb-6">
                            <div class="text-4xl text-primary opacity-20 leading-none">
                                <i class="fas fa-quote-left"></i>
                            </div>
                            <div class="flex text-sm text-yellow-brand gap-0.5">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                            </div>
                        </div>
                        <p class="text-slate-600 leading-relaxed mb-8 flex-grow text-sm italic">
                            "{{ strip_tags($review->content) }}"
                        </p>
                        <div class="flex items-center gap-4 mt-auto pt-6 border-t border-slate-50">
                            <img src="{{ $review->image?->url ?? 'https://ui-avatars.com/api/?name='.urlencode($review->name).'&background=006b63&color=fff' }}" class="w-12 h-12 rounded-full object-cover ring-2 ring-primary/10" alt="{{ $review->name }}">
                            <div>
                                <div class="font-extrabold text-slate-900 text-sm">{{ $review->name }}</div>
                                <div class="text-xs text-slate-400 mt-0.5">{{ $review->position ?? 'Khách hàng' }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Blog -->
    @if(isset($allPosts) && $allPosts->count())
    <section id="blog" class="py-16">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">
            <div class="flex items-end justify-between mb-10" data-aos="fade-up">
                <div>
                    <div class="text-xs uppercase tracking-[0.25em] font-extrabold text-yellow-brand mb-3">
                        Tin tức du lịch
                    </div>
                    <h2 class="text-3xl md:text-4xl font-serif font-bold text-slate-900" style="font-family: 'Playfair Display', serif;">
                        Cẩm nang & Kinh nghiệm
                    </h2>
                </div>
                <a href="{{ route('frontend.posts.index') ?? '#' }}" class="hidden md:flex items-center gap-2 text-primary font-bold text-sm hover:text-dark-primary transition">
                    Xem tất cả <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                @foreach($allPosts->take(3) as $post)
                    <article class="group cursor-pointer" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                        <a href="{{ $post->slug_url ?? '#' }}" class="block">
                            <div class="relative h-60 rounded-2xl overflow-hidden mb-6 shadow-md">
                                <img src="{{ $post->image?->url ?? 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=80&w=900&auto=format&fit=crop' }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" alt="{{ $post->title }}">
                                @if($post->category)
                                    <span class="absolute top-4 left-4 px-4 py-1.5 rounded-full bg-white/90 backdrop-blur-sm text-primary text-xs font-black uppercase tracking-wide shadow-sm">{{ $post->category->name }}</span>
                                @endif
                            </div>
                            <h3 class="text-xl font-extrabold text-slate-900 leading-snug group-hover:text-primary transition-colors line-clamp-2">{{ $post->title }}</h3>
                            <p class="text-slate-500 text-sm mt-3 line-clamp-2">{{ strip_tags($post->description ?? $post->content) }}</p>
                            <div class="flex items-center gap-4 text-xs text-slate-400 mt-5 font-medium">
                                <span class="flex items-center gap-1.5"><i class="far fa-calendar-alt"></i> {{ $post->created_at->format('d/m/Y') }}</span>
                                <span class="flex items-center gap-1.5 text-primary group-hover:underline uppercase tracking-wide font-bold">Đọc tiếp <i class="fas fa-arrow-right"></i></span>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Partners -->
    @if(isset($brands) && $brands->count())
    <section class="py-12 bg-white border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">
            <div class="bg-slate-50 rounded-3xl p-6 md:p-8 overflow-hidden shadow-inner" data-aos="fade-up">
                <div class="text-xs uppercase tracking-[0.25em] font-extrabold text-slate-400 mb-6 text-center">
                    Đối tác & Khách hàng tiêu biểu
                </div>

                <div class="relative flex overflow-hidden group">
                    <div class="absolute left-0 top-0 bottom-0 w-24 bg-gradient-to-r from-slate-50 to-transparent z-10"></div>
                    <div class="absolute right-0 top-0 bottom-0 w-24 bg-gradient-to-l from-slate-50 to-transparent z-10"></div>

                    <div class="flex gap-8 partner-track w-max items-center group-hover:[animation-play-state:paused]">
                        @foreach($brands as $brand)
                            <div class="w-40 md:w-48 h-20 bg-white rounded-2xl border border-slate-100 flex items-center justify-center p-4 hover:shadow-md transition-shadow grayscale hover:grayscale-0">
                                <img src="{{ $brand->image?->url }}" alt="{{ $brand->name }}" class="max-w-full max-h-full object-contain">
                            </div>
                        @endforeach
                        {{-- Duplicate for infinite scroll effect --}}
                        @foreach($brands as $brand)
                            <div class="w-40 md:w-48 h-20 bg-white rounded-2xl border border-slate-100 flex items-center justify-center p-4 hover:shadow-md transition-shadow grayscale hover:grayscale-0">
                                <img src="{{ $brand->image?->url }}" alt="{{ $brand->name }}" class="max-w-full max-h-full object-contain">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- CTA -->
    <section class="relative bg-primary rounded-t-[2.5rem] overflow-hidden mt-16 shadow-[0_-10px_40px_rgba(0,0,0,0.1)]" data-aos="fade-up">
        <img src="https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?q=80&w=1800&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover mix-blend-overlay opacity-40" alt="CTA Background">
        <div class="absolute inset-0 bg-gradient-to-r from-dark-primary/95 to-primary/60"></div>
        <div class="relative z-10 max-w-7xl mx-auto px-4 lg:px-8 py-14 lg:py-20 flex flex-col md:flex-row md:items-center justify-between gap-8 text-white">
            <div class="max-w-2xl">
                <h2 class="text-3xl md:text-4xl font-serif font-bold mb-4 leading-tight" style="font-family: 'Playfair Display', serif;">
                    Bạn chưa biết nên đi đâu?
                </h2>
                <p class="text-white/80 text-lg md:text-xl">
                    Để {{ config('app.name') }} tư vấn cho bạn! Nhận tư vấn miễn phí – Lên lịch trình theo yêu cầu.
                </p>
            </div>
            <div class="shrink-0">
                <a href="#booking" class="inline-flex items-center gap-3 px-8 py-4 rounded-xl bg-yellow-brand text-slate-900 font-extrabold hover:bg-amber-300 transition shadow-[0_10px_25px_rgba(251,191,36,0.4)] text-lg hover:-translate-y-1">
                    <i class="fas fa-paper-plane"></i> Nhận tư vấn ngay
                </a>
            </div>
        </div>
    </section>

@endsection

@push('js')
<script>
    // Include custom initialization logic if not handled by AOS global
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof AOS !== 'undefined') {
            AOS.init({
                duration: 750,
                once: true,
                offset: 50,
            });
        }
        
        if (typeof Swiper !== 'undefined') {
            var tourSwiper = new Swiper('.tour-swiper', {
                slidesPerView: 1.2,
                spaceBetween: 16,
                pagination: {
                    el: '.tour-swiper .swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.tour-swiper-next',
                    prevEl: '.tour-swiper-prev',
                },
                breakpoints: {
                    640: {
                        slidesPerView: 2,
                        spaceBetween: 20,
                    },
                    1024: {
                        slidesPerView: 3,
                        spaceBetween: 24,
                    }
                }
            });
        }
    });
</script>
@endpush
