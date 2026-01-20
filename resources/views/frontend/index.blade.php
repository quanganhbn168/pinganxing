@extends('layouts.master')
@section('title','Trang chủ - '.$setting->name)
@section('meta_description',$setting->meta_description)
@section('meta_keywords',$setting->meta_keywords)
@push('css')
<link rel="stylesheet" href="{{ asset('css/counter.css') }}" media="print" onload="this.media='all'">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" media="print" onload="this.media='all'">
<noscript>
    <link rel="stylesheet" href="{{ asset('css/counter.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">
</noscript>
@endpush
@push('jsonld')
<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Store",
        "name": "{{$setting->name}}",
        "alternateName": "{{$setting->name}}",
        "url": "{{ url()->current() }}",
        "logo": "{{asset($setting->logo)}}",
        "description": "{{$setting->meta_description}}",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "{{$setting->address}}",
            "addressLocality": "Thành phố Bắc Ninh",
            "addressRegion": "Bắc Ninh",
            "postalCode": "220000",
            "addressCountry": "VN"
        },
        "telephone": "{{$setting->phone}}",
        "email": "{{$setting->email}}",
        "openingHoursSpecification": [{
                "@type": "OpeningHoursSpecification",
                "dayOfWeek": [
                    "Monday",
                    "Tuesday",
                    "Wednesday",
                    "Thursday",
                    "Friday"
                ],
                "opens": "08:00",
                "closes": "17:30"
            },
            {
                "@type": "OpeningHoursSpecification",
                "dayOfWeek": "Saturday",
                "opens": "08:00",
                "closes": "12:00"
            }
        ],
        "sameAs": [
            "{{$setting->facebook}}",
            "{{$setting->youtube}}",
            "{{$setting->zalo}}"
        ]
    }
</script>
@endpush
@section("content")
@isset($sections['hero'])
<section id="slider">
    @include("partials.frontend.slide")
</section>
@endisset
@isset($sections['intro'])
@php $introSection = $sections['intro']; @endphp
<section class="section section-intro">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12 col-md-6">
                <a href="{{route('frontend.slug.handle',$introMain->slug)}}">
                    <img src="{{ optional($introMain->mainImage())->url() }}" alt="{{$introMain->name}}" fetchpriority="high" width="600" height="400" style="width: 100%; height: auto;">
                </a>
            </div>
            <div class="col-12 col-md-6">
                <h2 class="">{{$setting->name}}</h2>
                {!! $introMain->description !!}
                <div class="intro-action">
                    <a href="{{ $introSection->getSetting('button_link', asset('storage/' . $setting->profile)) }}" target="_blank" class="btn btn-primary rounded-pill btn-crossover">
                        <span class="btn-crossover-text">{{ $introSection->getSetting('button_text', 'Download Profile') }}</span>
                        <span class="btn-crossover-icon">
                            <i class="fa-solid fa-arrow-right-long"></i>
                        </span>
                    </a>
                    <a href="{{ $introSection->getSetting('button_2_link', '/gioi-thieu') }}" class="btn btn-outline-primary rounded-pill btn-crossover">
                        <span class="btn-crossover-text">{{ $introSection->getSetting('button_2_text', 'Xem chi tiết') }}</span>
                        <span class="btn-crossover-icon">
                            <i class="fa-solid fa-arrow-right-long"></i>
                        </span>
                    </a>
                </div>
            </div>
        </div>
        <div class="row mt-5">
    @foreach($sodem as $item)
        <div class="col-md-3 col-6">
            <x-counter 
                :icon="$item['icon']" 
                :to="$item['value']" 
                suffix="+" 
                :label="$item['title']" 
            />
        </div>
    @endforeach
</div>
    </div>
</section>
@endisset
@isset($sections['fields'])
@php $fieldsSection = $sections['fields']; @endphp
<section class="section section-fields" style="background-image: url('{{ $fieldsSection->background_image ? asset($fieldsSection->background_image) : asset('images/setting/contractors-bg-1.png') }}');">
    <h2 class="section-title">
        <a href="{{ route('frontend.fields.index') }}">{{ $fieldsSection->title ?? 'Lĩnh vực hoạt động' }}</a>
    </h2>
    <div class="container">
        @if(!empty($fieldsSection->subtitle))
            <p class="text-center mb-4 section-subtitle">{{ $fieldsSection->subtitle }}</p>
        @endif
        <div class="row">
            @foreach($homeFields as $key => $field)
            <div class="col-6 col-md-4 mb-4">
                <div class="field-category-item">
                    <div class="field-category-item__image">
                        <a href="{{ route('frontend.slug.handle', $field->slugValue) }}">
                            <img src="{{ optional($field->mainImage())->url() }}" alt="{{ $field->name }}" loading="lazy">
                        </a>
                    </div>
                    <div class="field-category-item__name">
                        <a href="{{ route('frontend.slug.handle', $field->slugValue) }}">
                            {{ $field->name }}
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endisset
@isset($sections['projects'])
@php $projectsSection = $sections['projects']; @endphp
<section class="section section-project">
    <div class="container">
        <h2 class="section-title"><a href="">{{ $projectsSection->title ?? 'Dự án nổi bật' }}</a></h2>
        @if(!empty($projectsSection->subtitle))
            <p class="text-center mb-4 section-subtitle">{{ $projectsSection->subtitle }}</p>
        @endif
        @if($homeProjectCategories->isNotEmpty())
        <ul class="nav nav-pills justify-content-center mb-4 d-none d-md-flex" id="projectTabs" role="tablist">
            @if($homeProjects->isNotEmpty())
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-all-projects" data-toggle="pill" data-target="#pane-all-projects" type="button" role="tab" aria-controls="pane-all-projects" aria-selected="true">Tất cả</button>
            </li>
            @endif
            @foreach($homeProjectCategories as $projectCategory)
            @if($projectCategory->projects->isNotEmpty())
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-{{ $projectCategory->slug }}" data-toggle="pill" data-target="#pane-{{ $projectCategory->slug }}" type="button" role="tab" aria-controls="pane-{{ $projectCategory->slug }}" aria-selected="false">{{ $projectCategory->name }}</button>
            </li>
            @endif
            @endforeach
        </ul>
        @endif
        <div class="tab-content" id="projectTabsContent">
            @if($homeProjects->isNotEmpty())
            {{-- Mobile Title 1 --}}
            <div class="project-header-mobile is-open d-md-none" data-target="#pane-all-projects">Tất cả</div>
            <div class="tab-pane fade show active" id="pane-all-projects" role="tabpanel">
                <div class="project-slider-wrapper">
                    <div class="swiper project-swiper">
                        <div class="swiper-wrapper">
                            @foreach($homeProjects as $project)
                            @include('partials.frontend.project_item', ['project' => $project])
                            @endforeach
                        </div>
                    </div>
                    <div class="swiper-pagination"></div>
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                </div>
            </div>
            @endif

            @foreach($homeProjectCategories as $projectCategory)
            @if($projectCategory->projects->isNotEmpty())
            {{-- Mobile Title Other --}}
            <div class="project-header-mobile d-md-none" data-target="#pane-{{ $projectCategory->slug }}">{{ $projectCategory->name }}</div>
            <div class="tab-pane fade" id="pane-{{ $projectCategory->slug }}" role="tabpanel">
                <div class="project-slider-wrapper">
                    <div class="swiper project-swiper">
                        <div class="swiper-wrapper">
                            @foreach($projectCategory->projects as $project)
                            @include('partials.frontend.project_item', ['project' => $project])
                            @endforeach
                        </div>
                    </div>
                    <div class="swiper-pagination"></div>
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                </div>
            </div>
            @endif
            @endforeach
        </div>
    </div>
</section>
@endisset
@isset($sections['partners'])
@php $partnersSection = $sections['partners']; @endphp
<section class="section section-partner">
    <div class="container">
        <h2 class="section-title"><a href="">{{ $partnersSection->title ?? 'Đối tác & khách hàng' }}</a></h2>
        @if(!empty($partnersSection->subtitle))
            <p class="text-center mb-4 section-subtitle">{{ $partnersSection->subtitle }}</p>
        @endif
        <div class="row">
            <div class="col-12 col-lg-6">
                <div class="partner-testimonial">
                    <div class="quote-box">
                        <div class="quote-icon">
                            <i class="fas fa-quote-left"></i>
                        </div>
                        <p class="quote-text">
                            {{ $partnersSection->getSetting('quote_text', 'Chúng tôi cam kết đem đến cho khách hàng những sản phẩm chất lượng cao và dịch vụ tốt nhất!') }}
                        </p>
                        <div class="quote-author">
                            <strong class="author-name">{{ $partnersSection->getSetting('quote_author', 'Lê Sỹ Ngà') }}</strong>
                            <span class="author-title">{{ $partnersSection->getSetting('quote_position', 'Giám đốc') }}</span>
                        </div>
                    </div>
                    <img src="{{ $partnersSection->getSetting('quote_image') ? asset($partnersSection->getSetting('quote_image')) : asset('images/setting/bat-tay.png') }}" alt="{{ $partnersSection->getSetting('quote_author', 'Director') }}" class="director-image" loading="lazy">
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="partner-list">
                    @foreach($brands as $brand)
                    <a href="{{ $brand->link ?? '#' }}" class="partner-logo-item" target="_blank">
                        <img src="{{ !empty($brand->image) ? asset($brand->image) : optional($brand->mainImage())->url() }}" alt="{{ $brand->name }}">
                    </a>
                    @endforeach
                </div>
                <a href="/khach-hang" class="view-all-partners">Xem tất cả <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
</section>
@endisset
@isset($sections['core_values'])
<section class="section section-core-values">
    @foreach ($slide_banners as $slide_banner)
    <div class="coreValueItem">
        <img src="{{ optional($slide_banner->mainImage())->url() }}" alt="{{ $slide_banner->title }}">
    </div>
    @endforeach
</section>
@endisset
@isset($sections['news'])
@php $newsSection = $sections['news']; @endphp
<section class="section section-news">
    <div class="container">
        <div class="row">
            <div class="col-12 col-lg-8">
                <h2 class="section-title text-left mb-2">
                    <a href="">{{ $newsSection->title ?? 'Tin tức - sự kiện' }}</a>
                </h2>
                @if(!empty($newsSection->subtitle))
                    <p class="text-left mb-3 section-subtitle">{{ $newsSection->subtitle }}</p>
                @endif
                @if($homePostCategories->isNotEmpty())
                <ul class="nav nav-pills mb-3" id="newsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-news-all" data-toggle="pill" data-target="#pane-news-all" type="button" role="tab" aria-controls="pane-news-all" aria-selected="true">Tất cả</button>
                    </li>
                    @foreach($homePostCategories as $postCategory)
                    @if($postCategory->posts->isNotEmpty())
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-news-{{ $postCategory->slug }}" data-toggle="pill" data-target="#pane-news-{{ $postCategory->slug }}" type="button" role="tab" aria-controls="pane-news-{{ $postCategory->slug }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">{{ $postCategory->name }}</button>
                    </li>
                    @endif
                    @endforeach
                </ul>
                <div class="tab-content" id="newsTabsContent">
                    <div class="tab-pane fade show active" id="pane-news-all" role="tabpanel" aria-labelledby="tab-news-all">
                        @php
                        $firstAllPost = $allPosts->first();
                        $otherAllPosts = $allPosts->skip(1);
                        @endphp
                        <div class="news-grid">
                            <div class="news-grid-large">
                                <a href="{{ route('frontend.slug.handle', $firstAllPost->slug) }}" class="news-item">
                                    <img src="{{ optional($firstAllPost->mainImage())->url() }}" alt="{{ $firstAllPost->title }}">
                                    <div class="news-item-title">
                                        <h3>{{ $firstAllPost->title }}</h3>
                                    </div>
                                </a>
                            </div>
                            @if($otherAllPosts->isNotEmpty())
                            <div class="news-grid-small">
                                @foreach($otherAllPosts as $post)
                                <a href="{{ route('frontend.slug.handle', $post->slug) }}" class="news-item">
                                    <img src="{{ optional($post->mainImage())->url() }}" alt="{{ $post->title }}">
                                    <div class="news-item-title">
                                        <h4>{{ $post->title }}</h4>
                                    </div>
                                </a>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                    @foreach($homePostCategories as $postCategory)
                    @if($postCategory->posts->isNotEmpty())
                    <div class="tab-pane fade" id="pane-news-{{ $postCategory->slug }}" role="tabpanel" aria-labelledby="tab-news-{{ $postCategory->slug }}">
                        @php
                        $firstPost = $postCategory->posts->first();
                        $otherPosts = $postCategory->posts->skip(1)->take(2);
                        @endphp
                        <div class="news-grid">
                            <div class="news-grid-large">
                                <a href="{{ route('frontend.slug.handle', $firstPost->slugValue) }}" class="news-item">
                                    <img src="{{ optional($firstPost->mainImage())->url() }}" alt="{{ $firstPost->title }}">
                                    <div class="news-item-title">
                                        <h3>{{ $firstPost->title }}</h3>
                                    </div>
                                </a>
                            </div>
                            @if($otherPosts->isNotEmpty())
                            <div class="news-grid-small">
                                @foreach($otherPosts as $post)
                                <a href="{{ route('frontend.slug.handle', $post->slugValue) }}" class="news-item">
                                    <img src="{{ optional($post->mainImage())->url() }}" alt="{{ $post->title }}">
                                    <div class="news-item-title">
                                        <h4>{{ $post->title }}</h4>
                                    </div>
                                </a>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>
                @endif
            </div>
            <div class="col-12 col-lg-4 mt-4 mt-lg-0">
    <h4 class="section-title">
        <a href="javascript:void(0)">{{ $newsSection->getSetting('video_title', 'Video giới thiệu') }}</a>
    </h4>
    <div class="video-list">
        <div class="position-relative rounded overflow-hidden" style="background: #000; min-height: 200px;">
            
            {{-- LOGIC YOUTUBE --}}
            @if(($setting->video_type ?? 'youtube') === 'youtube' && !empty($setting->intro_video_url))
                @php
                    // Lấy ID Youtube để tạo thumbnail và link embed
                    $videoID = '';
                    $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/';
                    if (preg_match($pattern, $setting->intro_video_url, $match)) {
                        $videoID = $match[1];
                    }
                @endphp
                
                @if($videoID)
                    <a href="https://www.youtube.com/watch?v={{ $videoID }}" class="glightbox position-relative d-block w-100 h-100">
                        {{-- Lấy ảnh thumbnail chất lượng cao từ Youtube --}}
                        <img src="https://img.youtube.com/vi/{{ $videoID}}/hqdefault.jpg" class="w-100 h-100" style="object-fit: cover; opacity: 0.8;" alt="Video Thumbnail">
                        
                        {{-- Nút Play Icon --}}
                        <div class="position-absolute top-50 start-50 translate-middle text-white" style="top: 50%; left: 50%; transform: translate(-50%, -50%);">
                            <i class="fas fa-play-circle fa-4x"></i>
                        </div>
                    </a>
                @endif

            {{-- LOGIC UPLOAD VIDEO --}}
            @elseif(($setting->video_type ?? '') === 'upload' && !empty($setting->intro_video))
                @php
                    $videoPath = asset('storage/' . $setting->intro_video);
                @endphp
                {{-- Với video upload, ta dùng thẻ video luôn hoặc Glightbox (nhưng cần ảnh thumb riêng). 
                     Để đơn giản và đẹp, ta dùng Glightbox gọi file video --}}
                <a href="{{ $videoPath }}" class="glightbox position-relative d-block w-100 h-100">
                    {{-- Vì không có ảnh thumb, ta dùng ảnh banner mặc định hoặc nền đen --}}
                    <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-dark text-white" style="min-height: 215px;">
                        <span class="text-center">
                             <i class="fas fa-play-circle fa-4x mb-2"></i><br>
                             Xem Video Giới Thiệu
                        </span>
                    </div>
                </a>
            @endif

        </div>
    </div>
</div>
        </div>
    </div>
</section>
@endisset
@isset($sections['careers'])
<section class="section section-careers">
    <div class="container">
        <div class="row">
            <div class="col-6 col-md-4">
                <div class="career-item">
                    <h4 class="career-item-title">{{ $tuyendung->name }}</h4>
                    <div class="career-item-img card-has-overlay">
                        <a href="/tuyen-dung">
                            <img src="{{ optional($tuyendung->mainImage())->url() }}" alt="{{ $tuyendung->description }}">
                        </a>
                        <p class="text-overlay">{{$tuyendung->description}}</p>
                    </div>
                    <div class="career-item-link">
                        <a href="/tuyen-dung">Ứng tuyển</a>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4">
                <div class="career-item">
                    <h4 class="career-item-title">{{ $daily->name ?? 'Hệ thống đại lý' }}</h4>
                    @if(isset($daily))
                    <div class="career-item-img card-has-overlay">
                        <a href="{{ route('agency.index') }}">
                            <img src="{{ optional($daily->mainImage())->url() }}" alt="{{ $daily->description }}">
                        </a>
                        <p class="text-overlay">{{$daily->description}}</p>
                    </div>
                    <div class="career-item-link">
                        <a href="{{ route('agency.index') }}">Hợp tác ngay</a>
                    </div>
                    @else
                    <div class="career-item-img card-has-overlay">
                        <a href="{{ route('agency.index') }}">
                           <img src="https://placehold.co/600x400?text=Dai+Ly" alt="Đại lý">
                        </a>
                    </div>
                    <div class="career-item-link">
                        <a href="{{ route('agency.index') }}">Hợp tác ngay</a>
                    </div>
                    @endif
                </div>
            </div>

            <div class="col-6 col-md-4">
                <div class="career-item">
                    <h4 class="career-item-title">Tư vấn triển khai</h4>
                    <div class="career-item-img card-has-overlay">
                        <a href="{{ route('consulting.index') }}">
                            {{-- Use a distinct image or setting image --}}
                            <img src="{{ asset($setting->banner_image ?? 'images/setting/lien-he-bg.jpg') }}" alt="Tư vấn triển khai" style="height: 100%; object-fit: cover;">
                        </a>
                        <p class="text-overlay">Giải pháp tối ưu - Chi phí hợp lý</p>
                    </div>
                    <div class="career-item-link">
                        <a href="{{ route('consulting.index') }}">Gửi yêu cầu</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endisset
@isset($sections['testimonials'])
@php $testimonialsSection = $sections['testimonials']; @endphp
<section class="section section-testimonial">
    <div class="container">
        <h2 class="section-title"><a href="">{{ $testimonialsSection->title ?? 'Đánh giá từ khách hàng' }}</a></h2>
        @if(!empty($testimonialsSection->subtitle))
            <p class="text-center mb-4 section-subtitle">{{ $testimonialsSection->subtitle }}</p>
        @endif
        <div class="swiper testimonial-swiper">
            <div class="swiper-wrapper">
                @foreach($testimonials as $testimonial)
                <div class="swiper-slide">
                    <div class="testimonial-item">
                        <div class="testimonial-author">
                            <div class="testimonial-logo">
                                <img src="{{ optional($testimonial->mainImage())->url() }}" alt="{{ $testimonial->name }}">
                            </div>
                            <div class="testimonial-info">
                                <span class="author-name">{{ $testimonial->name }}</span>
                                {{-- <span class="author-position">{{ $testimonial->position }}</span> --}}
                            </div>
                        </div>
                        <div class="testimonial-content">
                            <p>{!! $testimonial->content !!}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section>
@endisset
@isset($sections['contact_form'])
@php $contactSection = $sections['contact_form']; @endphp
<section class="section-contact-visual" style="background-image: url('{{ $contactSection->background_image ? asset($contactSection->background_image) : asset('images/setting/lien-he-bg.jpg') }}');">
    <div class="contact-overlay"></div>
    <div class="container position-relative">
        <div class="section-decorator">
            <span></span><span></span><span></span><span></span>
        </div>
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="contact-visual-info">
                    <h2 class="contact-visual-title">
                        {{ $contactSection->title ?? 'Vui lòng để lại thông tin, chúng tôi sẽ liên hệ sớm nhất!' }}
                    </h2>
                    @if(!empty($contactSection->subtitle))
                        <p class="contact-visual-subtitle mb-4">{{ $contactSection->subtitle }}</p>
                    @endif
                    <ul class="contact-visual-features">
                        <li><i class="{{ $contactSection->getSetting('feature_1_icon', 'fa-solid fa-gears') }}"></i> {{ $contactSection->getSetting('feature_1_text', 'Quy trình nhanh chóng') }}</li>
                        <li><i class="{{ $contactSection->getSetting('feature_2_icon', 'fa-solid fa-headset') }}"></i> {{ $contactSection->getSetting('feature_2_text', 'Đội ngũ tư vấn nhiệt tình') }}</li>
                        <li><i class="{{ $contactSection->getSetting('feature_3_icon', 'fa-solid fa-tags') }}"></i> {{ $contactSection->getSetting('feature_3_text', 'Giá cả phù hợp nhất') }}</li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-6">
                <form action="{{ route('contact.store') }}" method="POST" id="contact-form" class="contact-visual-form">
                    @csrf
                    <div class="form-group">
                        <input type="text" name="name" class="form-control" placeholder="Tên của bạn" required>
                    </div>
                    <div class="form-group">
                        <input type="email" name="email" class="form-control" placeholder="Email">
                    </div>
                    <div class="form-group">
                        <input type="tel" name="phone" class="form-control" placeholder="Số điện thoại" required>
                    </div>
                    <div class="form-group form-group-message">
                        <input name="message" class="form-control" placeholder="Nội dung bạn quan tâm">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-submit">Gửi thông tin</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endisset
@endsection
@push('js')
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
<script>
    $.validator.addMethod("phoneVN", function(value, element) {
        return this.optional(element) || /^(0[3|5|7|8|9])[0-9]{8}$|^\+84[3|5|7|8|9][0-9]{8}$/.test(value);
    }, "Số điện thoại không hợp lệ");
    $(document).ready(function() {
        $('#contact-form').validate({
            rules: {
                name: {
                    required: true,
                    minlength: 2
                },
                phone: {
                    required: true,
                    phoneVN: true
                },
                email: {
                    email: true
                },
                message: {
                    maxlength: 1000
                }
            },
            messages: {
                name: {
                    required: "Vui lòng nhập họ và tên",
                    minlength: "Tên quá ngắn"
                },
                phone: {
                    required: "Vui lòng nhập số điện thoại",
                    phoneVN: "Số điện thoại không hợp lệ (ví dụ: 098xxxxxxx)"
                },
                email: {
                    email: "Email không hợp lệ"
                },
                message: {
                    maxlength: "Ý kiến không vượt quá 1000 ký tự"
                }
            },
            errorElement: 'small',
            errorClass: 'text-danger',
            highlight: function(element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element) {
                $(element).removeClass('is-invalid');
            }
        });
    });
</script>
<script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>
<script>
    const lightbox = GLightbox({
        selector: '.glightbox', // Áp dụng cho class .glightbox
        touchNavigation: true,
        loop: true,
        autoplayVideos: true
    });
</script>
<script>
    $(document).ready(function() {
        // --- 1. Hàm để khởi tạo Swiper (đã được tối ưu) ---
        function initSwiper(paneSelector) {
            const $pane = $(paneSelector);
            const swiperEl = $pane.find('.project-swiper')[0];
            
            if (swiperEl && !swiperEl.swiper) {
                const swiperOptions = {
            loop: false,
            rewind: true,
            grabCursor: true,
            centeredSlides: false,
            slidesPerView: 1, // Hiển thị 1 slide trọn vẹn trên mobile
            spaceBetween: 20, // Khoảng cách giữa các slide
            pagination: {
                el: paneSelector + ' .swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: paneSelector + ' .swiper-button-next',
                prevEl: paneSelector + ' .swiper-button-prev',
            },
            breakpoints: {
                // Khi màn hình >= 768px (tablet)
                768: {
                    slidesPerView: 2.5,
                    spaceBetween: 30,
                },
                // Khi màn hình >= 1024px (desktop)
                1024: {
                    slidesPerView: 3.5,
                    spaceBetween: 40,
                }
            }
        };
                new Swiper(swiperEl, swiperOptions);
            }
        }
        // --- 2. Khởi tạo slider cho tab đầu tiên khi tải trang ---
        initSwiper('#pane-all-projects');
        // --- 3. Lắng nghe sự kiện click Desktop ---
        $('button[data-toggle="pill"]').on('click', function(e) {
            e.preventDefault();
            const targetPaneId = $(this).data('target');
            if (targetPaneId) {
                // Ẩn tất cả panes thủ công cho chắc
                $('.tab-pane').removeClass('show active');
                
                // Hiện pane đích
                $(targetPaneId).addClass('show active');
                
                // Cập nhật Buttons Desktop
                $('#projectTabs .nav-link').removeClass('active');
                $(this).addClass('active');

                // Đồng bộ Header Mobile
                $('.project-header-mobile').removeClass('is-open');
                $(`.project-header-mobile[data-target="${targetPaneId}"]`).addClass('is-open');
                
                initSwiper(targetPaneId);
            }
        });

        // --- 4. Lắng nghe sự kiện Accordion Mobile ---
        $('.project-header-mobile').on('click', function() {
            const targetPaneId = $(this).data('target');
            if ($(this).hasClass('is-open')) return;

            // Ẩn tất cả panes
            $('.tab-pane').removeClass('show active');
            
            // Hiện pane đích
            $(targetPaneId).addClass('show active');
            
            // Cập nhật giao diện Accordion
            $('.project-header-mobile').removeClass('is-open');
            $(this).addClass('is-open');

            // Đồng bộ Tab Desktop
            $('#projectTabs .nav-link').removeClass('active');
            $(`button[data-target="${targetPaneId}"]`).addClass('active');

            initSwiper(targetPaneId);

            // Cuộn mượt tới tiêu đề
            const offset = $(this).offset().top - 60;
            $('html, body').stop().animate({
                scrollTop: offset
            }, 500);
        });

        // Cập nhật lại Swiper khi resize màn hình
        let resizeTimer;
        $(window).on('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                $('.project-swiper').each(function() {
                    if (this.swiper) this.swiper.update();
                });
            }, 250);
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const testimonialSwiper = new Swiper('.testimonial-swiper', {
            // Kích hoạt lặp vô tận
            loop: true,
            // Tự động chạy
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            // Số lượng slide hiển thị trên các màn hình khác nhau (responsive)
            slidesPerView: 1,
            spaceBetween: 30,
            breakpoints: {
                768: {
                    slidesPerView: 2,
                },
                992: {
                    slidesPerView: 3,
                }
            },
            // Dấu chấm phân trang
            pagination: {
                el: '.testimonial-swiper .swiper-pagination',
                clickable: true,
            },
        });
    });
</script>
<script src="{{ asset('js/counter.js') }}"></script>
@endpush