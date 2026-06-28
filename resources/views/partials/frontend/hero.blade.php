{{-- <section id="hero" class="hero">
	<div class="box-name">
		{{$setting->site_name}}
	</div>
	<div class="box-banner">
		{{$setting->banner}}
	</div>
</section>
<section class="section-stats">
    <div class="stats-item">
        <img src="..." alt="">
        <p class="stats-number" data-target="100">0</p>
        <p class="stats-label">Khách hàng hài lòng</p>
    </div>
    <div class="stats-item">
        <img src="..." alt="">
        <p class="stats-number" data-target="2000">0</p>
        <p class="stats-label">Khách hàng thân thiết</p>
    </div>
    <div class="stats-item">
        <img src="..." alt="">
        <p class="stats-number" data-target="1000">0</p>
        <p class="stats-label">Dự án phim</p>
    </div>
    <div class="stats-item">
        <img src="..." alt="">
        <p class="stats-number" data-target="5">0</p>
        <p class="stats-label">Năm hình thành và phát triển</p>
    </div>
</section> --}}
<div id="hero" class="hero">
    <div class="hero-wrapper">
        <div class="container">
            <div class="row">
                <div class="col-12 col-md-6">
                    <h1 class="hero-title">NHA KHOA BANI</h1>
                    <ul class="about-difference fa-ul text-light">
                        <li><span class="fa-li"><i class="fas fa-check-circle text-success"></i></span> Bác sĩ chuyên môn sâu, nhiều năm kinh nghiệm</li>
                        <li><span class="fa-li"><i class="fas fa-check-circle text-success"></i></span> Trang thiết bị hiện đại, đạt chuẩn quốc tế</li>
                        <li><span class="fa-li"><i class="fas fa-check-circle text-success"></i></span> Chi phí minh bạch, không phát sinh</li>
                        <li><span class="fa-li"><i class="fas fa-check-circle text-success"></i></span> Bảo hành lâu dài, cam kết rõ ràng</li>
                        <li><span class="fa-li"><i class="fas fa-check-circle text-success"></i></span> Dịch vụ tận tâm, chăm sóc sau điều trị</li>
                    </ul>

                    <div class="cta-action">
                        <a href="{{ route('contact.show') }}" class="btn ml-2 btn-register btn-hero">Đặt lịch ngay</a>
                        <a href="{{ route('contact.show') }}" class="btn btn-price btn-hero">Xem bảng giá</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>          
@push('js')
{{-- <script>
    function animateCount($el, target) {
        $({ countNum: 0 }).animate({ countNum: target }, {
            duration: 1500,
            easing: 'swing',
            step: function () {
                $el.text(Math.floor(this.countNum));
            },
            complete: function () {
                $el.text(target.toLocaleString() + '+'); // ví dụ: 1000+
            }
        });
    }

    $(document).ready(function () {
        let hasAnimated = false;

        $(window).on('scroll', function () {
            const statsSection = $('.section-stats');
            const sectionTop = statsSection.offset().top - window.innerHeight + 100;

            if (!hasAnimated && $(window).scrollTop() > sectionTop) {
                hasAnimated = true;

                $('.stats-number').each(function () {
                    const $el = $(this);
                    const target = parseInt($el.data('target'));
                    animateCount($el, target);
                });
            }
        });
    });
</script> --}}
@endpush

