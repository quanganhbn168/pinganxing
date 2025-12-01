@extends('layouts.master')
@section('title', $page->name)
@section('meta_description',$page->description)
@section('meta_image', optional($page->bannerImage())->url())
@push('css')
<style>
    /* 2. Khối màu xanh chứa nội dung */
.thongdiep-text {
    background-color: #1072ba; /* Màu xanh chủ đạo của box */
    color: #ffffff;
    padding: 40px 30px 60px 30px; /* Padding dưới to hơn để chừa chỗ cho ảnh */
    position: relative; /* Để căn chỉnh dấu nháy kép */
    border-radius: 0; /* Vuông vức theo thiết kế */
}

/* Tạo dấu nháy kép to đùng ở góc */
.thongdiep-text::before {
    content: '“';
    font-family: Georgia, serif; /* Font có chân để dấu nháy đẹp */
    font-size: 180px;
    color: #ffffff;
    position: absolute;
    top: -30px;
    left: 30px;
    line-height: 1;
    opacity: 0.8;
    pointer-events: none; /* Để không che mất nội dung khi click */
}

/* Ảnh banner nhỏ bên trong box xanh */
.thongdiep-text-banner {
    margin-bottom: 20px;
    position: relative;
    z-index: 2; /* Đè lên dấu nháy kép */
}

.thongdiep-text-banner img {
    /* width: 60%; */
    height: auto;
    display: block;
    border: 1px solid rgba(255, 255, 255, 0.2); /* Viền mờ nhẹ cho ảnh */
}

/* Định dạng nội dung văn bản bên trong (Do render từ HTML editor) */
.thongdiep-text p, 
.thongdiep-text h3,
.thongdiep-text h4 {
    color: #ffffff !important;
    position: relative;
    z-index: 2;
}

/* Câu quote to đậm: "Không chỉ là công việc..." */
/* Giả sử trong editor bạn để thẻ H3 hoặc P đậm cho câu này */
.thongdiep-text h3, 
.thongdiep-text strong {
    font-size: 22px;
    line-height: 1.4;
    font-weight: 700;
    display: block;
    margin-bottom: 20px;
}

/* Tên giám đốc & chức vụ */
.thongdiep-text p {
    font-size: 14px;
    margin-bottom: 5px;
    font-weight: 400;
}

/* 3. Ảnh chân dung Lãnh đạo */
.thongdiep-image {
    margin-top: -130px; /* Kỹ thuật kéo ảnh lên đè vào box xanh */
    text-align: right; /* Căn giữa ảnh so với cột */
    position: relative;
    z-index: 10; /* Đảm bảo ảnh nằm trên cùng */
    margin-bottom: 20px; /* Khoảng cách với footer nếu có */
}

.thongdiep-image img {
    height: 420px; /* Chiều cao cố định để ảnh không quá to */
    width: auto;
    object-fit: contain;
    display: inline-block;
    filter: drop-shadow(0 5px 15px rgba(0,0,0,0.2)); /* Bóng đổ nhẹ cho ảnh người thật hơn */
}
/* 1. Khung thẻ Job */
.job-card {
    background-color: #ecf3f8; /* Màu nền xanh nhạt giống hình */
    padding: 25px 25px 15px 25px; /* Padding trên/trái/phải to, dưới nhỏ */
    position: relative;
    transition: all 0.3s ease;
    border-bottom: 2px solid transparent; /* Chuẩn bị cho hover */
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-height: 260px; /* Chiều cao tối thiểu để đều nhau */
}

/* Hiệu ứng hover: Viền dưới đậm màu xanh */
.job-card:hover {
    background-color: #e6f0f7;
    border-bottom-color: #0056b3; 
}

/* 2. Tiêu đề */
.job-title {
    margin-bottom: 20px;
    line-height: 1.4;
}

.job-title a {
    color: #0b244e; /* Màu xanh than đậm */
    font-size: 18px;
    font-weight: 700;
    text-transform: uppercase; /* Chữ in hoa */
    text-decoration: none;
    display: -webkit-box;
    -webkit-line-clamp: 2; /* Giới hạn 2 dòng */
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* 3. Danh sách thông tin */
.job-info {
    list-style: none;
    padding: 0;
    margin: 0;
}

.job-info li {
    display: flex;
    align-items: center;
    margin-bottom: 12px;
    font-size: 15px;
    color: #333;
}

/* Căn chỉnh Icon */
.job-info .icon-wrapper {
    width: 25px; /* Cố định chiều rộng để thẳng hàng */
    display: inline-block;
    color: #3c7dbd; /* Màu xanh của icon */
    font-size: 16px;
}

/* Icon $ có viền vuông bao quanh giống hình */
.icon-salary {
    border: 1.5px solid #3c7dbd;
    border-radius: 3px;
    font-size: 10px;
    padding: 1px 3px;
    width: auto;
    height: auto;
    display: inline-block;
    vertical-align: middle;
}

.job-info .text-label {
    margin-right: 5px;
}

.job-info .text-value {
    color: #333;
    font-weight: 400;
}

/* 4. Footer & Mũi tên */
.job-card-footer {
    margin-top: 20px;
    padding-top: 15px;
    border-top: 1px solid #dbe6ee; /* Đường kẻ ngang mờ */
    text-align: right; /* Đẩy mũi tên sang phải */
}

.job-arrow {
    color: #3c7dbd;
    font-size: 16px;
    transition: transform 0.3s ease;
    display: inline-block;
}

/* Hiệu ứng mũi tên chạy khi hover */
.job-card:hover .job-arrow {
    transform: translateX(5px);
    color: #0056b3;
}
</style>
@endpush
@section('content')
<div class="banner">
    <img src="{{optional($page->bannerImage())->url() ?? asset($setting->banner)}}" alt="{{ $page->name }}">
</div>
<div class="container mt-5">
    <div class="thongdiep">
        <h2 class="custom-section-title">Thông điệp từ lãnh đạo</h2>
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="thongdiep-text">
                    <div class="thongdiep-text-banner">
                        <img src="{{ optional($thongdiep->bannerImage())->url() }}" alt="">
                    </div>
                    {{ $thongdiep->content['html'] }}
                </div>
                <div class="thongdiep-image">
                    <img src="{{ optional($thongdiep->mainImage())->url() }}" alt="{{ $thongdiep->name }}">
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="">
                    {{ $page->content['html']}}
                </div>
            </div>
        </div>
    </div>
    <div class="tuyendung mt-5 mb-5">
    <div class="row">
        @forelse($careers as $career)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="job-card h-100">
                    <div class="job-card-body">
                        {{-- Tiêu đề --}}
                        <h3 class="job-title">
                            <a href="{{ route('frontend.careers.show', $career->slug ?? $career->id) }}">
                                {{ $career->name }}
                            </a>
                        </h3>

                        {{-- Danh sách thông tin --}}
                        <ul class="job-info">
                            <li>
                                <span class="icon-wrapper"><i class="fas fa-map-marker-alt"></i></span>
                                <span class="text-label">Địa điểm:</span> 
                                <span class="text-value">{{ $career->location ?? 'Hà Nội' }}</span>
                            </li>
                            <li>
                                {{-- Icon tiền tệ giả lập giống hình --}}
                                <span class="icon-wrapper"><i class="fas fa-dollar-sign icon-salary"></i></span>
                                <span class="text-label">Lương:</span> 
                                <span class="text-value">{{ $career->salary ?? 'Thỏa thuận' }}</span>
                            </li>
                            <li>
                                <span class="icon-wrapper"><i class="far fa-calendar-alt"></i></span>
                                <span class="text-label">Ngày hết hạn:</span> 
                                <span class="text-value">
                                    {{ $career->deadline ? \Carbon\Carbon::parse($career->deadline)->format('d/m/Y') : '30/12/2025' }}
                                </span>
                            </li>
                        </ul>
                    </div>

                    {{-- Footer mũi tên --}}
                    <div class="job-card-footer">
                        <a href="{{ route('frontend.careers.show', $career->slug ?? $career->id) }}" class="job-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center">
                <p>Chưa có tin tuyển dụng nào.</p>
            </div>
        @endforelse
    </div>
</div>
</div>
@endsection