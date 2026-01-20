<footer class="footer-new">
    <div class="main-footer">
        <div class="container">
            <div class="row gy-4"> {{-- gy-4 để tạo khoảng cách giữa các cột trên mobile --}}

                {{-- Cột 1: Thông tin công ty (3/12) --}}
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="footer-widget">
                        <div class="logo-footer mb-3">
                            <a href="/" title="{{ $setting->name }}">
                                <img src="{{ asset($setting->logo) }}" alt="{{ $setting->name }}">
                            </a>
                        </div>
                        <p class="footer-description">
                            CnetPOS - Đồng hành cùng bạn trên "Hành trình tới tương lai", mang đến giải pháp hiện đại và đẳng cấp.
                        </p>
                        <div class="info-address">
                            <p><i class="fas fa-map-marker-alt me-2"></i> {{ $setting->address }}</p>
                            <p><i class="fas fa-phone-alt me-2"></i> <a href="tel:{{ $setting->phone }}">{{ $setting->phone }}</a></p>
                            <p><i class="fas fa-envelope me-2"></i> <a href="mailto:{{ $setting->email }}">{{ $setting->email }}</a></p>
                        </div>
                    </div>
                </div>

                {{-- Cột 2: Về Cnetpos (Fix cứng) --}}
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="footer-widget">
                        <h4 class="widget-title">Về Cnetpos</h4>
                        <ul class="menu-list">
                            <li><a href="/">Trang chủ</a></li>
                            <li><a href="{{ route('frontend.intro.index') }}">Giới thiệu chung</a></li>
                            <li><a href="/du-an">Dự án đã thực hiện</a></li>
                            <li><a href="/lien-he">Liên hệ</a></li>
                        </ul>
                    </div>
                </div>

                {{-- Cột 3: Chính sách và hướng dẫn (Động từ danh mục) --}}
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="footer-widget">
                        <h4 class="widget-title">Chính sách và hướng dẫn</h4>
                        @if(isset($footerPolicies) && $footerPolicies->count() > 0)
                        <ul class="menu-list">
                            @foreach($footerPolicies as $policy)
                            <li>
                                <a href="{{ route('frontend.slug.handle', ['slug' => $policy->slug]) }}">
                                    {{ $policy->title }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                </div>

                {{-- Cột 4: Đăng ký nhận tin & Mạng xã hội (3/12) --}}
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="footer-widget">
                        <h4 class="widget-title">Kết nối với chúng tôi</h4>
                        <p>Nhận thông tin mới nhất về sản phẩm và khuyến mãi.</p>
                        
                        <form class="subscribe-form mt-3">
                            <div class="input-group">
                                <input type="email" class="form-control" placeholder="Email nhận tin..." aria-label="Email">
                                <button type="submit" class="btn btn-primary" id="button-subscribe">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </form>

                        <div class="social-list mt-4">
                            <a href="{{ $setting->youtube ?? '#' }}" target="_blank" title="Youtube"><i class="fab fa-youtube"></i></a>
                            <a href="{{ $setting->facebook ?? '#' }}" target="_blank" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="{{ $setting->zalo ?? '#' }}" target="_blank" title="Zalo"><i class="fas fa-paper-plane"></i></a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="copyright">
        <div class="container">
            <span>© Bản quyền thuộc về <b>{{$setting->name}}</b> | Cung cấp bởi <a href="https://webappbacninh.vn/" rel="nofollow" target="_blank">Web App Bắc Ninh</a></span>
        </div>
    </div>
</footer>