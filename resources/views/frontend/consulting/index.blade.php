@extends('layouts.master')
@section('title', 'Tư vấn triển khai & Dự toán')
@section('content')
@push('css')
<style>
/* CSS REUSED FROM RECRUITMENT PAGE & UPDATED FOR CONSULTING */
.thongdiep-text {
    background-color: #1072ba; /* Màu xanh chủ đạo */
    color: #ffffff;
    padding: 40px 30px 60px 30px;
    position: relative;
    border-radius: 0;
}

.thongdiep-text::before {
    content: '“';
    font-family: Georgia, serif;
    font-size: 180px;
    color: #ffffff;
    position: absolute;
    top: -30px;
    left: 30px;
    line-height: 1;
    opacity: 0.8;
    pointer-events: none;
}

.thongdiep-text-banner {
    margin-bottom: 20px;
    position: relative;
    z-index: 2;
}

.thongdiep-text-banner img {
    height: auto;
    display: block;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.thongdiep-text p, 
.thongdiep-text h3,
.thongdiep-text h4,
.thongdiep-text ul,
.thongdiep-text li {
    color: #ffffff !important;
    position: relative;
    z-index: 2;
}

.thongdiep-text h3, 
.thongdiep-text strong {
    font-size: 22px;
    line-height: 1.4;
    font-weight: 700;
    display: block;
    margin-bottom: 20px;
}

.thongdiep-image {
    margin-top: -130px; 
    text-align: right; 
    position: relative;
    z-index: 10; 
    margin-bottom: 20px; 
}

.thongdiep-image img {
    height: 420px; 
    width: auto;
    object-fit: contain;
    display: inline-block;
    filter: drop-shadow(0 5px 15px rgba(0,0,0,0.2)); 
}

</style>
@endpush

<div class="banner">
    {{-- Banner ảnh --}}
    <img src="{{ !empty($setting->banner_image) ? asset($setting->banner_image) : asset($setting->banner) }}" alt="Tư vấn giải pháp" style="width: 100%; height: auto; object-fit: cover;">
</div>

<div class="container mt-5">
    
    {{-- MESSAGE BLOCK --}}
    <div class="thongdiep">
        <h2 class="custom-section-title text-uppercase font-weight-bold mb-4">Giải pháp kỹ thuật toàn diện</h2>
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="thongdiep-text">
                    <h3>Chuyên gia của chúng tôi sẵn sàng hỗ trợ bạn</h3>
                    <p style="margin-bottom: 15px;">Quy trình tư vấn chuyên nghiệp giúp tối ưu chi phí và hiệu quả vận hành:</p>
                    <ul style="list-style-type: disc; padding-left: 20px;">
                        <li style="margin-bottom: 10px;"><strong>Khảo sát thực tế:</strong> Đánh giá hiện trạng và nhu cầu cụ thể.</li>
                        <li style="margin-bottom: 10px;"><strong>Thiết kế giải pháp:</strong> Lên bản vẽ và phương án thi công chi tiết.</li>
                        <li style="margin-bottom: 10px;"><strong>Dự toán ngân sách:</strong> Minh bạch, chi tiết và tối ưu chi phí đầu tư.</li>
                        <li><strong>Tư vấn công nghệ:</strong> Cập nhật xu hướng thiết bị mới nhất.</li>
                    </ul>
                    <br>
                    <p><em>"Giải pháp đúng - Đầu tư thông minh."</em></p>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="thongdiep-image">
                    {{-- Dùng ảnh intro hoặc ảnh kỹ thuật --}}
                    <img src="{{ asset($setting->intro_image ?? 'images/setting/lien-he-bg.jpg') }}" onerror="this.src='https://placehold.co/400x420/png?text=Solutions'" alt="Tư vấn giải pháp">
                </div>
            </div>
        </div>
    </div>

    {{-- CONSULTING FORM --}}
    <div class="tuyendung mt-5 mb-5">
        <h2 class="custom-section-title text-uppercase font-weight-bold mb-4">Gửi yêu cầu & Bản vẽ</h2>
        
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm border-0 p-4 p-md-5" style="border-top: 4px solid #1072ba !important; border-radius: 4px;">
                    <p class="text-center mb-4 text-muted mt-2">Vui lòng mô tả yêu cầu hoặc tải lên bản vẽ, chúng tôi sẽ phản hồi trong vòng 24h.</p>
                    
                    <form action="{{ route('consulting.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="font-weight-bold">Họ và tên <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" required placeholder="Nhập họ tên...">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="font-weight-bold">Số điện thoại <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" class="form-control" required placeholder="Nhập số điện thoại...">
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="font-weight-bold">Email</label>
                                    <input type="email" name="email" class="form-control" placeholder="Nhập email nhận báo giá...">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="font-weight-bold">Tên Công ty / Cửa hàng</label>
                                    <input type="text" name="company" class="form-control" placeholder="Nhập tên doanh nghiệp...">
                                </div>
                            </div>

                            <div class="col-12 mb-3">
                                <div class="form-group">
                                    <label class="font-weight-bold">Địa chỉ triển khai</label>
                                    <input type="text" name="address" class="form-control" placeholder="Địa chỉ lắp đặt, thi công...">
                                </div>
                            </div>

                            <div class="col-12 mb-3">
                                <div class="form-group">
                                    <label class="font-weight-bold">Yêu cầu chi tiết / Mô tả dự án</label>
                                    <textarea name="details" class="form-control" rows="5" placeholder="Mô tả nhu cầu, số lượng, quy mô..."></textarea>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="font-weight-bold">Ngân sách dự kiến</label>
                                    <input type="text" name="budget" class="form-control" placeholder="VD: 50 triệu - 100 triệu">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">File đính kèm (Bản vẽ, Hồ sơ)</label>
                                <div class="custom-file">
                                    <input type="file" name="file" class="custom-file-input" id="customFile">
                                    <label class="custom-file-label" for="customFile" style="height: 48px; line-height: 2.5;">Chọn file...</label>
                                </div>
                                <small class="text-muted mt-3 d-block">Hỗ trợ PDF, DOCX, IMG, CAD. Max 10MB.</small>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg px-5 font-weight-bold text-uppercase" style="background-color: #1072ba; border-color: #1072ba; border-radius: 0;">
                                <i class="fa fa-paper-plane mr-2"></i> Gửi Yêu Cầu
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    // Custom file input name
    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });
</script>
@endpush
@endsection
