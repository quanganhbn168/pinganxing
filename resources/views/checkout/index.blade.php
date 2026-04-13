@extends('layouts.master')
@section('title', 'Thanh toán')
@section('content')
<div class="container py-5">
    <form action="{{ route('checkout.place') }}" method="POST" id="checkout-form" novalidate>
        @csrf
        <div class="row">
            <div class="col-md-7">
                <h4>Thông tin giao hàng</h4>
                <hr>
                @auth('web')
                    <div class="alert alert-info">
                        Đang đặt hàng với tài khoản: <strong>{{ auth('web')->user()->name }}</strong>
                        (<a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Đăng xuất</a>)
                    </div>
                @endauth
                <div class="mb-3">
                    <label for="customer_name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="customer_name" name="customer_name" value="{{ auth('web')->user()->name ?? old('customer_name') }}" required>
                </div>
                <div class="mb-3">
                    <label for="customer_phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                    <input type="tel" class="form-control" id="customer_phone" name="customer_phone" value="{{ auth('web')->user()->phone ?? old('customer_phone') }}" required>
                </div>
                <div class="mb-3">
                    <label for="customer_address" class="form-label">Địa chỉ <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="customer_address" name="customer_address" value="{{ auth('web')->user()->address ?? old('customer_address') }}" required>
                </div>
                 <div class="mb-3">
                    <label for="note" class="form-label">Ghi chú đơn hàng (tùy chọn)</label>
                    <textarea class="form-control" id="note" name="note" rows="3">{{ old('note') }}</textarea>
                </div>
            </div>
            <div class="col-md-5">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title">Đơn hàng của bạn</h4>
                        <ul class="list-group list-group-flush mt-3" id="order-summary-list">
                        </ul>
                        <hr>
                        <ul class="list-group list-group-flush">
                             <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 pb-0">
                                Tạm tính
                                <span id="summary-subtotal">0đ</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 mb-3">
                                <div><strong>Tổng cộng</strong></div>
                                <span><strong id="summary-total">0đ</strong></span>
                            </li>
                        </ul>
                        <hr>
                        <h5>Phương thức thanh toán</h5>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="payment_cod" value="cod" checked>
                            <label class="form-check-label" for="payment_cod">
                                Thanh toán khi nhận hàng (COD)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="payment_bank" value="bank_transfer">
                            <label class="form-check-label" for="payment_bank">
                                Chuyển khoản ngân hàng (VietQR)
                            </label>
                        </div>
                        <button type="submit" class="btn bg-main w-100 mt-3">ĐẶT HÀNG</button>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" name="cart_data" id="cart_data_input">
    </form>
</div>
@endsection
@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const isGuest = {{ Auth::guard('web')->check() ? 'false' : 'true' }};
    const STORAGE_KEY = 'guest_cart';
    const cartContainer = document.getElementById('order-summary-list'); 
    const submitBtn = document.querySelector('#checkout-form button[type="submit"]');
    const form = document.getElementById('checkout-form');
    
    const formatCurrency = (number) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(number);
    
    function updateSummaryTotal() {
        let total = 0;
        const itemTotals = cartContainer.querySelectorAll('.item-total');
        itemTotals.forEach(item => {
            total += parseFloat(item.getAttribute('data-total')) || 0;
        });
        document.getElementById('summary-subtotal').textContent = formatCurrency(total);
        document.getElementById('summary-total').textContent = formatCurrency(total);
    }
    
    function renderAuthSummary() {
        const authCartItems = {!! json_encode($cartItems ?? []) !!};
        cartContainer.innerHTML = '';

        if (!authCartItems.length) {
            cartContainer.innerHTML = '<li class="list-group-item">Giỏ hàng trống</li>';
            submitBtn.disabled = true;
            submitBtn.classList.add('disabled');
            return;
        }

        authCartItems.forEach(item => {
            const unitPrice = Number(
                (item.variant && item.variant.price) ??
                (item.product && (item.product.price_discount ?? item.product.price)) ??
                0
            );
            const qty = Number(item.quantity) || 1;
            const subtotal = unitPrice * qty;

            let variantText = item.variant_text || item.variantText || '';

            if (!variantText && item.variant && (item.variant.attribute_values || item.variant.attributeValues)) {
                const av = item.variant.attribute_values || item.variant.attributeValues;
                if (Array.isArray(av) && av.length) {
                    variantText = av.map(v => (v.value ?? v.name ?? v)).join(' / ');
                }
            }

            const itemHtml = `
                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <div>
                        ${item.product?.name ?? 'Sản phẩm'}
                        ${variantText ? `<small class="d-block text-muted">${variantText}</small>` : ''}
                        <small class="d-block text-muted">SL: ${qty}</small>
                    </div>
                    <span class="item-total" data-total="${subtotal}">${formatCurrency(subtotal)}</span>
                </li>`;
            cartContainer.insertAdjacentHTML('beforeend', itemHtml);
        });

        updateSummaryTotal();
    }

    function renderGuestSummary() {
        const cart = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
        cartContainer.innerHTML = '';

        if (!cart.length) {
            cartContainer.innerHTML = '<li class="list-group-item">Giỏ hàng trống</li>';
            submitBtn.disabled = true;
            submitBtn.classList.add('disabled');
            return;
        }

        cart.forEach(item => {
            const unitPrice = Number(item.price) || 0;
            const qty = Number(item.quantity) || 1;
            const subtotal = unitPrice * qty;
            const variantText = item.variantText || item.variant_text || '';

            const itemHtml = `
                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <div>
                        ${item.name}
                        ${variantText ? `<small class="d-block text-muted">${variantText}</small>` : ''}
                        <small class="d-block text-muted">SL: ${qty}</small>
                    </div>
                    <span class="item-total" data-total="${subtotal}">${formatCurrency(subtotal)}</span>
                </li>`;
            cartContainer.insertAdjacentHTML('beforeend', itemHtml);
        });

        updateSummaryTotal();
    }

    if (isGuest) {
        renderGuestSummary();
    } else {
        renderAuthSummary();
    }

    // Native HTML5 Validation setup
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Custom phone validation logic
        const phoneInput = document.getElementById('customer_phone');
        const phoneRegex = /^(0[3|5|7|8|9])[0-9]{8}$|^\+84[3|5|7|8|9][0-9]{8}$/;
        
        let isValid = true;
        
        form.querySelectorAll('.text-danger.validation-err').forEach(el => el.remove());
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        
        const showError = (input, message) => {
            isValid = false;
            input.classList.add('is-invalid');
            const err = document.createElement('small');
            err.className = 'text-danger validation-err d-block mt-1';
            err.innerText = message;
            input.parentNode.appendChild(err);
        };

        const nameInput = document.getElementById('customer_name');
        if(!nameInput.value || nameInput.value.length < 2) {
            showError(nameInput, 'Vui lòng nhập họ tên hợp lệ.');
        }

        if(!phoneInput.value || !phoneRegex.test(phoneInput.value)) {
            showError(phoneInput, 'Số điện thoại không hợp lệ (ví dụ: 098xxxxxxx)');
        }

        const addrInput = document.getElementById('customer_address');
        if(!addrInput.value || addrInput.value.length < 10) {
            showError(addrInput, 'Vui lòng nhập địa chỉ cụ thể.');
        }

        if (isValid) {
            if (isGuest) {
                document.getElementById('cart_data_input').value = localStorage.getItem(STORAGE_KEY);
            }
            form.submit();
        }
    });
});
</script>
@endpush
