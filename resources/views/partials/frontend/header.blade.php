<header class="header">
     <div class="top-bar d-none d-lg-block">
        <div class="container text-center">
            <span>Thiết kế & In bảo hộ lao động</span>
            </div>
    </div>
    <div class="main-header">
        <div class="container">
            <div class="main-header-inner">
                <div class="header-col-left">
                    <div class="mobile-menu-toggle">
                        <a href="#" aria-label="Toggle Menu"><i class="fa fa-bars"></i></a>
                    </div>
                    <div class="logo d-lg-block">
                        <a href="{{ url('/') }}">
                            <img src="{{asset($setting->logo)}}" alt="Logo">
                        </a>
                    </div>
                </div>
                <div class="header-col-center">
                    <div class="logo d-none d-lg-none">
                        <a href="{{ url('/') }}">
                            <img src="{{asset($setting->logo)}}" alt="Logo">
                        </a>
                    </div>
                    <div class="search-box d-none d-lg-block">
                        <form action="{{route('frontend.search')}}" method="get">
                            <input type="text" name="q" class="form-control" placeholder="Bạn tìm gì hôm nay?">
                            <button type="submit"><i class="fa fa-search"></i></button>
                        </form>
                    </div>
                </div>
                <div class="header-col-right">
                    <div class="header-hotline">
                            <a href="">
                        <div class="frame-fix">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-whatsapp" viewBox="0 0 16 16">
                                    <path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z"></path>
                                </svg>
                                <div class="text-box">
                                    <span class="acc-text-small">Hotline hỗ trợ</span>
                                    <span class="acc-text">{{$setting->phone}}</span>
                                </div>
                        </div>
                            </a>
                    </div>
                    <div class="header-address">
                        <a href="/lien-he">
                        <div class="frame-fix">
                                <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M19 22.52L26 21.174V2H2V25.79L9.095 24.425C9.03237 24.2921 8.99993 24.1469 9 24V16C9 15.7348 9.10536 15.4804 9.29289 15.2929C9.48043 15.1054 9.73478 15 10 15H18C18.2652 15 18.5196 15.1054 18.7071 15.2929C18.8946 15.4804 19 15.7348 19 16V22.52ZM17 22.905V17H11V24C11 24.02 11 24.04 10.998 24.059L17 22.905ZM1 0H27C27.2652 0 27.5196 0.105357 27.7071 0.292893C27.8946 0.48043 28 0.734784 28 1V22C28 22.2324 27.919 22.4576 27.771 22.6368C27.623 22.816 27.4172 22.9381 27.189 22.982L1.189 27.982C1.04431 28.0098 0.89526 28.0054 0.752502 27.9689C0.609744 27.9324 0.476808 27.8649 0.363202 27.7711C0.249597 27.6772 0.158129 27.5595 0.0953412 27.4262C0.0325533 27.2929 -3.37575e-06 27.1473 2.62534e-10 27V1C2.62534e-10 0.734784 0.105357 0.48043 0.292893 0.292893C0.48043 0.105357 0.734784 0 1 0ZM6 7.998V4H22V7.998H6Z" fill="white"></path>
                                </svg>
                                <div class="text-box">
                                    <span class="acc-text-small">Địa chỉ liên hệ</span>
                                    <span class="acc-text">Kho hàng</span>
                                </div>
                        </div>
                        </a>
                    </div>
                    <div class="header-actions">
                            <div class="frame-fix">
                                <svg aria-hidden="true" class="svg-icon tool-icon" viewBox="0 0 32 32"><path d="M7.164 29.986a1 1 0 01-1.148-1.165l2-11A1 1 0 019 17h14a1 1 0 01.97.757l2 8a1 1 0 01-.806 1.23l-18 3zm1.074-2.206l15.53-2.588L22.218 19H9.835l-1.597 8.78zM16 15c-3.314 0-6-2.91-6-6.5S12.686 2 16 2s6 2.91 6 6.5-2.686 6.5-6 6.5zm0-2c2.172 0 4-1.98 4-4.5S18.172 4 16 4c-2.172 0-4 1.98-4 4.5s1.828 4.5 4 4.5z" fill="white"></path></svg>
                                <div class="text-box">
                                    <span class="acc-text-small">Thông tin</span>
                                    <span class="acc-text">
                                        Tài khoản
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-down-fill" viewBox="0 0 16 16">
                                            <path d="M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"></path>
                                        </svg>
                                    </span>
                                </div>
                                <ul>
                                    @auth('web')
                                        <li class="li-account">
                                            <a class="dropdown-item" href="{{ route('user.dashboard') }}">
                                                <i class="fa-solid fa-gauge-high"></i> Tài khoản
                                            </a>
                                        </li>
                                        <li>
                                            <form id="logout-form" action="{{ route('logout') }}" method="POST">
                                                @csrf
                                                <button type="submit">
                                                    <i class="fa-solid fa-gauge-high"></i> Bảng điều khiển
                                                </button>
                                            </form>
                                        </li>
                                    @else
                                        <li class="li-account">
                                            <a href="/login">
                                                <i class="bi bi-box-arrow-in-right"></i>
                                                Đăng nhập
                                            </a>
                                        </li>
                                        <li class="li-account">
                                            <a href="/register">
                                                <i class="bi bi-person-plus"></i>
                                                Đăng ký
                                            </a>
                                        </li>
                                    @endauth
                                </ul>               
                            </div>
                        
                    <div class="cart-action">
                        <a href="#" class="cart-icon"> 
                            <i class="fa fa-shopping-cart"></i>
                            @auth('web')
                            <span class="cart-count">{{ $cartTotalQuantity ?? 0 }}</span>
                            @else
                            <span class="cart-count" id="guest-cart-count">0</span>
                            @endauth
                        </a>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="mobile-search-container d-lg-none">
        <div class="container">
            <div class="search-box">
                <form action="/search" method="get">
                    <input type="text" class="form-control" placeholder="Tìm kiếm sản phẩm...">
                    <button type="submit"><i class="fa fa-search"></i></button>
                </form>
            </div>
        </div>
    </div>
    <nav class="main-nav-container d-none">
        <div class="container">
            <ul class="main-menu-desktop">
                <li><a href="/">Trang Chủ</a></li>
                <li class="menu-item-has-children">
                    <a href="/san-pham">Sản Phẩm</a>
                    <span class="submenu-toggle"><i class="fa fa-angle-down"></i></span>
                    <ul class="sub-menu">
                        @foreach($categoryMenus as $category)
                        <li>
                            <a href="{{ route('products.by_category', $category->slug) }}">
                                {{ $category->name }}
                            </a>
                            @if($category->children->isNotEmpty())
                            <span class="submenu-toggle"><i class="fa fa-angle-right"></i></span>
                            <ul class="sub-menu">
                                @foreach($category->children as $menuChild)
                                <li><a href="{{ route('products.by_category', $menuChild->slug) }}">{{ $menuChild->name }}</a></li>
                                @endforeach
                            </ul>
                            @endif
                        </li>
                        @endforeach
                    </ul>
                </li>
                <li class="menu-item-has-children">
                    <a href="/blog">Tin Tức</a>
                     <span class="submenu-toggle"><i class="fa fa-angle-down"></i></span>
                    <ul class="sub-menu">
                        <li><a href="#">Tin Khuyến Mãi</a></li>
                        <li><a href="#">Tin Thời Trang</a></li>
                    </ul>
                </li>
                <li><a href="{{route('intro.show')}}">Về Chúng Tôi</a></li>
                <li><a href="/tra-cuu-bao-hanh">Bảo hành</a></li>
                <li><a href="/lien-he">Liên Hệ</a></li>
            </ul>
        </div>
    </nav>
</header>
<div class="offcanvas-menu-wrapper">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">MENU</h5>
        <a href="#" class="offcanvas-close"><i class="fa fa-times"></i></a>
    </div>
    <div class="offcanvas-menu-content">
        </div>
</div>
<div class="cart-offcanvas-wrapper">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">Giỏ Hàng Của Bạn</h5>
        <a href="#" class="offcanvas-close js-close-cart"><i class="fa fa-times"></i></a>
    </div>
    @auth('web')
        <div class="offcanvas-body">
            @forelse($cartItems as $item)
            <div class="cart-item cart-item-auth">
                <div class="cart-item_image">
                    <img src="{{ asset($item->product->image ?? 'https://placehold.co/100x100') }}" alt="{{ $item->product->name }}">
                </div>
                <div class="cart-item_info">
                    <a href="{{ route('frontend.product.show', $item->product->slug) }}" class="item-name">{{ $item->product->name }}</a>
                    <div class="item-meta">
                        <span class="item-price">{{ number_format($item->product->price) }}đ</span>
                        <span class="item-quantity">x {{ $item->quantity }}</span>
                    </div>
                </div>
                <a href="#" class="item-remove" title="Xóa sản phẩm" data-item-id="{{ $item->id }}">
                    <i class="fa fa-trash"></i>
                </a>
            </div>
            @empty
            <p class="text-center p-4">Giỏ hàng của bạn đang trống.</p>
            @endforelse
        </div>
        <div class="offcanvas-footer">
            <div class="cart-summary">
                <span>Tổng cộng:</span>
                <span class="total-price">{{ number_format($cartTotal ?? 0) }}đ</span>
            </div>
            <a href="/cart" class="btn btn-dark w-100">Xem Giỏ Hàng</a>
            <a href="/checkout" class="btn btn-primary w-100 mt-2">Thanh Toán</a>
        </div>
    @else
        <div id="guest-cart-body" class="offcanvas-body">
            <p class="text-center p-4">Giỏ hàng của bạn đang trống.</p>
        </div>
        <div id="guest-cart-footer" class="offcanvas-footer" style="display: none;">
            <div class="cart-summary">
                <span>Tổng cộng:</span>
                <span id="guest-cart-total" class="total-price">0đ</span>
            </div>
            <a href="/cart" class="btn btn-dark w-100">Xem Giỏ Hàng</a>
            <a href="/checkout" class="btn bg-main w-100 mt-2">Thanh Toán</a>
        </div>
    @endauth   
</div>
<template id="guest-cart-item-template">
    <div class="cart-item">
        <div class="cart-item_image">
            <img src="__IMAGE__" alt="__NAME__">
        </div>
        <div class="cart-item_info">
            <a href="__URL__" class="item-name">__NAME__</a>
            <div class="item-variant text-muted small">__VARIANT__</div>
            <div class="item-meta">
                <span class="item-price">__PRICE__đ</span>
                <span class="item-quantity">x __QUANTITY__</span>
            </div>
        </div>
        <a href="#" class="item-remove" title="Xóa sản phẩm" data-item-id="__ID__">
            <i class="fa fa-trash"></i>
        </a>
    </div>
</template>
<div class="offcanvas-overlay"></div>
@push('js')
<script>
    $(document).ready(function() {
        window.addEventListener('scroll', function() {
            const header = document.querySelector('.header');
            const scrollPosition = window.scrollY;
            if (scrollPosition > 50) { 
                header.classList.add('header-scrolled');
                header.classList.remove('is-unsticking'); 
            } else {
                if (header.classList.contains('header-scrolled')) {
                    header.classList.remove('header-scrolled');
                    header.classList.add('is-unsticking');
                    setTimeout(function() {
                        header.classList.remove('is-unsticking');
                    }, 20);
                }
            }
        });
        if ($('.offcanvas-menu-content .main-menu-desktop').length === 0) {
            $('.main-menu-desktop').clone().appendTo('.offcanvas-menu-content');
        }
        $('.mobile-menu-toggle a').on('click', function(e) {
            e.preventDefault();
            $('body').addClass('show-offcanvas');
        });
        $('.offcanvas-menu-content').on('click', '.submenu-toggle', function(e) {
            e.preventDefault();
            $(this).parent('.menu-item-has-children').toggleClass('open');
            $(this).siblings('.sub-menu').slideToggle(300);
        });
        $('.cart-action > a').on('click', function(e) {
            e.preventDefault(); 
            $('body').addClass('show-cart-offcanvas');
        });
        $('.offcanvas-menu-wrapper .offcanvas-close').on('click', function(e) {
            e.preventDefault();
            $('body').removeClass('show-offcanvas');
        });
        $('.cart-offcanvas-wrapper .js-close-cart').on('click', function(e) {
            e.preventDefault();
            $('body').removeClass('show-cart-offcanvas');
        });
        $('.offcanvas-overlay').on('click', function(e) {
            e.preventDefault();
            $('body').removeClass('show-offcanvas show-cart-offcanvas');
        });
    });
    $('.header-actions .frame-fix').on('click', function(event) {
    // Ngăn sự kiện click lan ra ngoài, tránh việc tự đóng ngay lập tức
        event.stopPropagation(); 
        
    // Thêm/xóa class 'active' trên chính nó để bật/tắt menu
        $(this).toggleClass('active'); 
    });

// Bấm ra ngoài khu vực menu thì sẽ đóng menu lại
    $(document).on('click', function() {
        $('.header-actions .frame-fix').removeClass('active');
    });
</script>
@endpush