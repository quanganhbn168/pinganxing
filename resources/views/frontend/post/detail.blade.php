@extends('layouts.master')
@section('title', $post->title)
@section('meta_description', $post->description ?? Str::limit(strip_tags($post->content), 155))
@section('meta_image', optional($post->mainImage())->url() ?? '')
@push('jsonld')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "NewsArticle",
  "mainEntityOfPage": {
    "@type": "WebPage",
    "@id": "{{ url()->current() }}"
  },
  "headline": "{{ $post->title }}",
  "image": [
    "{{ optional($post->mainImage())->url() ?? '' }}"
  ],
  "datePublished": "{{ $post->created_at->toIso8601String() }}",
  "dateModified": "{{ $post->updated_at->toIso8601String() }}",
  "author": {
    "@type": "Person",
    "name": "{{ $setting->name ?? 'Admin' }}"
  },
  "publisher": {
    "@type": "Organization",
    "name": "{{ $setting->name }}",
    "logo": {
      "@type": "ImageObject",
      "url": "{{ asset($setting->logo) }}"
    }
  },
  "description": "{{ $post->description ?? Str::limit(strip_tags($post->content), 155) }}"
  @if($aggregateRating = $post->getAggregateRatingData())
  ,"aggregateRating": @json($aggregateRating)
  @endif
}
</script>
@endpush

@push('css')

<style>
    /* =========================================
       1. STYLE NỘI DUNG BÀI VIẾT (CORE)
       Mobile First approach
       ========================================= */
    
    /* Banner Image */
    .banner {
        width: 100%;
        max-height: 350px;
        overflow: hidden;
        margin-bottom: 0;
    }
    .banner img {
        width: 100%;
        height: 350px;
        object-fit: cover;
        object-position: center;
    }
    
    /* Post Main Image */
    .post-image {
        margin: 15px 0 20px;
        text-align: center;
    }
    .post-image img {
        max-width: 100%;
        max-height: 450px;
        width: auto;
        height: auto;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }
    
    /* Mobile: Smaller banner */
    @media (max-width: 768px) {
        .banner, .banner img {
            height: 200px;
            max-height: 200px;
        }
        .post-image img {
            max-height: 300px;
            border-radius: 8px;
        }
    }
       
    .post-content {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        font-size: 16px;     /* Size chữ tiêu chuẩn cho Mobile dễ đọc */
        line-height: 1.7;    /* Khoảng cách dòng thoáng */
        color: #2a2a2a;      /* Màu chữ đen dịu, không đen kịt */
        overflow-wrap: break-word; /* Ngắt từ dài để không vỡ layout mobile */
    }

    /* Typography - Heading */
    .post-content h2, 
    .post-content h3, 
    .post-content h4 {
        font-weight: 700;
        color: #111;
        margin-top: 1.5em;
        margin-bottom: 0.5em;
        line-height: 1.3;
        /* Để khi click TOC, header không bị Header của web che mất */
        scroll-margin-top: 80px; 
    }

    /* Mobile Heading Sizes */
    .post-content h2 { font-size: 1.4rem; }
    .post-content h3 { font-size: 1.2rem; }
    .post-content h4 { font-size: 1.1rem; }

    /* Desktop Heading Sizes (Override) */
    @media (min-width: 768px) {
        .post-content h2 { font-size: 1.75rem; }
        .post-content h3 { font-size: 1.5rem; }
    }

    /* Paragraph & List */
    .post-content p { margin-bottom: 1.2em; text-align: justify; }
    .post-content ul, .post-content ol {
        padding-left: 20px;
        margin-bottom: 1.5em;
    }
    .post-content li { margin-bottom: 0.5em; }

    /* Images & Media - Xử lý ảnh responsive */
    .post-content img {
        max-width: 100% !important;
        height: auto !important;
        display: block;
        margin: 20px auto; /* Căn giữa ảnh */
        border-radius: 6px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }
    
    .post-content figcaption {
        text-align: center;
        font-size: 0.9rem;
        color: #666;
        font-style: italic;
        margin-top: 8px;
    }

    /* Video/Iframe (Youtube) responsive */
    .post-content iframe, 
    .post-content video {
        max-width: 100%;
        width: 100%;
        aspect-ratio: 16 / 9; /* Tự động chỉnh tỷ lệ khung hình chuẩn */
        border-radius: 6px;
        margin: 20px 0;
    }

    /* Table - Xử lý bảng cuộn ngang trên Mobile */
    .post-content table {
        display: block;      /* Quan trọng: Biến bảng thành khối block */
        width: 100%;
        overflow-x: auto;    /* Cho phép cuộn ngang */
        border-collapse: collapse;
        margin: 20px 0;
        white-space: nowrap; /* Giữ nội dung trên 1 dòng để cuộn */
    }
    
    .post-content table th,
    .post-content table td {
        padding: 10px 15px;
        border: 1px solid #ddd;
        font-size: 0.95rem;
    }
    .post-content table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }

    /* Links */
    .post-content a {
        color: #007bff;
        text-decoration: underline;
        text-underline-offset: 3px;
    }

    /* =========================================
       2. STYLE SLIDE BÀI VIẾT LIÊN QUAN
       ========================================= */
    .related-posts-section h3 {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 20px;
        border-left: 4px solid #007bff;
        padding-left: 10px;
    }

    .related-item {
        /* Thẻ bao ngoài mỗi item trong slide */
        text-decoration: none;
        color: inherit;
        display: block;
        transition: transform 0.2s;
    }
    
    .related-item:hover {
        transform: translateY(-5px);
        color: inherit;
    }

    .related-thumb {
        position: relative;
        width: 100%;
        aspect-ratio: 16 / 9; /* Giữ tỷ lệ 16:9 như yêu cầu cũ cho đẹp */
        overflow: hidden;
        border-radius: 8px;
        margin-bottom: 10px;
    }

    .related-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover; /* Cắt ảnh vừa khung, ko méo */
        transition: transform 0.5s ease;
    }
    
    .related-item:hover .related-thumb img {
        transform: scale(1.1); /* Zoom nhẹ khi hover */
    }

    .related-title {
        font-size: 1rem;
        font-weight: 600;
        line-height: 1.4;
        margin: 0;
        /* Cắt dòng nếu tiêu đề quá dài (tối đa 2 dòng) */
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        color: #333;
    }

    /* Swiper Pagination Custom */
    .related-post-swiper {
        padding-bottom: 40px !important; /* Chừa chỗ cho dấu chấm pagination */
    }
    .swiper-pagination-bullet-active {
        background-color: #007bff !important;
    }

    /* =========================================
       3. SIDEBAR STYLES
       ========================================= */
    /* =========================================
       3. SIDEBAR STYLES
       ========================================= */
    .post-sidebar {
        /* Không dùng sticky ở đây nữa để tránh đè lên TOC */
        margin-bottom: 20px;
    }
    
    /* Class mới cho TOC Wrapper */
    .sticky-toc {
        position: sticky;
        top: 100px;
        z-index: 10;
    }
    
    .sidebar-widget {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    
    .sidebar-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #007bff;
    }
    
    .sidebar-menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .sidebar-menu li {
        border-bottom: 1px solid #f0f0f0;
    }
    
    .sidebar-menu li:last-child {
        border-bottom: none;
    }
    
    .sidebar-menu a {
        display: block;
        padding: 10px 0;
        color: #555;
        text-decoration: none;
        font-size: 0.95rem;
        transition: all 0.2s;
    }
    
    .sidebar-menu a:hover {
        color: #007bff;
        padding-left: 5px;
    }
    
    .sidebar-menu a i {
        color: #007bff;
        font-size: 0.8rem;
    }
    
    /* Mobile: Sidebar nằm dưới content */
    @media (max-width: 768px) {
        .post-sidebar {
            position: static;
            margin-top: 30px;
        }
        
        .sidebar-widget {
            padding: 15px;
        }
    }
</style>
@endpush
@section('content')
<div class="banner">
	@php
		$bannerUrl = optional($post->bannerImage())->url() ?: ($post->banner ? asset($post->banner) : asset($setting->banner ?? ''));
	@endphp
	<img src="{{ $bannerUrl }}" 
         alt="{{ $post->title }}" 
         class="img-fluid w-100" 
         style="max-height: 500px; object-fit: cover;" 
         loading="lazy">
</div>
<section class="section py-4">
	<div class="container">
		<div class="row">
			<div class="col-12 col-md-9">
				<article class="post-detail">
					<h1 class="mb-2">{{ $post->title }}</h1>
					<div class="post-image">
						@php
							$mainUrl = optional($post->mainImage())->url() ?: ($post->image ? asset($post->image) : null);
						@endphp
						@if($mainUrl)
							<img src="{{ $mainUrl }}" alt="{{ $post->title }}">
						@endif
					</div>
					<p class="text-muted mb-3">
						<i class="fa-regular fa-calendar"></i> {{ $post->updated_at->format('d/m/Y') }}
					</p>
					<x-social-share :title="$post->title" />

					<hr class="d-lg-none">
					<div class="post-content mt-4 text-justify">
						{!! $contentHtml !!}
					</div>
					{{-- Chia sẻ lại ở cuối bài --}}
					<div class="mt-5">
						<p class="font-weight-bold">Bạn thấy bài viết hữu ích? Chia sẻ ngay:</p>
						<x-social-share :title="$post->title" />
					</div>
				</article>
				{{-- Bài viết liên quan --}}
				@if ($relatedPosts->count())
				<div class="mt-5">
					<h3>Bài viết liên quan</h3>
					<div class="swiper related-post-swiper">
						<div class="swiper-wrapper">
							@foreach ($relatedPosts as $related)
							<div class="swiper-slide">
								<a href="{{ route('frontend.slug.handle', $related->slug) }}" class="d-block">
									<img src="{{ optional($related->mainImage())->url() ??
                                                    (optional($related->bannerImage())->url() ??
                                                        ($related->image ? asset($related->image) : asset('images/no-image.png'))) }}"
										class="img-fluid mb-2" alt="{{ $related->title }}" loading="lazy"
										width="400" height="250">
									<h6>{{ $related->title }}</h6>
								</a>
							</div>
							@endforeach
						</div>
						<div class="swiper-pagination"></div>
					</div>
				</div>
				@endif

				{{-- Bình luận & Đánh giá --}}
				<x-comment-list :comments="$post->approvedComments" />
				<x-comment-form :commentable="$post" type="post" />
			</div>
			<div class="col-12 col-md-3">
				<aside class="post-sidebar">
					<div class="sidebar-widget">
						<h2 class="sidebar-title">Danh mục bài viết</h2>
						<ul class="sidebar-menu">
							@foreach($allCategories as $category)
							<li>
								<a href="{{ route('frontend.slug.handle', $category->slugValue) }}">
									<i class="fas fa-angle-right me-2"></i>{{ $category->name }}
								</a>
							</li>
							@endforeach
						</ul>
					</div>
				</aside>
				
				<div class="sticky-toc">
					<x-toc :list="$tocList" />
				</div>
				
			</div>
		</div>
	</div>
</section>
@endsection

@push('js')
{{-- Chỉ giữ Swiper cho related posts, bỏ JS TOC tự sinh --}}
<script>
	new Swiper(".related-post-swiper", {
		slidesPerView: 2,
		spaceBetween: 20,
		pagination: {
			el: ".swiper-pagination",
			clickable: true
		},
		breakpoints: {
			768: {
				slidesPerView: 3
			},
			1024: {
				slidesPerView: 4
			},
		},
	});
</script>
@endpush