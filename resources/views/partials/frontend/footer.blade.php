<footer class="footer-new">
    <div class="main-footer">
        <div class="container">
            <div class="row gy-4"> {{-- gy-4 để tạo khoảng cách giữa các cột trên mobile --}}

                {{-- Cột 1: Thông tin công ty --}}
                <div class="col-12 col-lg-4">
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

                {{-- Lặp qua các cột menu được định nghĩa trong config/menu_footer.php --}}
                @foreach(get_menu_footer() as $menuColumn)
                <div class="col-12 col-md-6 col-lg-2">
                    <div class="footer-widget">
                        @if(!empty($menuColumn['items']))
                        <ul class="menu-list">
                            @foreach($menuColumn['items'] as $item)
                            <li><a href="{{ $item['url'] }}">{{ $item['title'] }}</a></li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                </div>
                @endforeach

                {{-- Cột 4: Đăng ký nhận tin & Mạng xã hội --}}
                <div class="col-12 col-lg-4">
                    <div class="footer-widget">
                        <h4 class="widget-title">Đăng ký nhận tin</h4>
                        <p>Nhận thông tin mới nhất về sản phẩm và các chương trình khuyến mãi của chúng tôi.</p>
                        
                        {{-- Sửa lại form theo cấu trúc input-group của Bootstrap --}}
                        <form class="subscribe-form mt-4">
                            <div class="input-group">
                                <input type="email" class="form-control" placeholder="Nhập email của bạn..." aria-label="Nhập email của bạn">
                                <button type="submit" class="btn btn-primary" id="button-subscribe">
                                    <i class="fas fa-paper-plane"></i> {{-- Icon gửi thư --}}
                                </button>
                            </div>
                        </form>

                        <div class="social-list mt-4">
                            <a href="{{ $setting->youtube ?? '#' }}" target="_blank" title="Youtube"><i class="fab fa-youtube"></i></a>
                            <a href="{{ $setting->facebook ?? '#' }}" target="_blank" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                            {{-- Đổi icon Zalo cho hiện đại hơn --}}
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