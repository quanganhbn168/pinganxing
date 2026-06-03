document.addEventListener('DOMContentLoaded', function () {
    const cartState = window.CartState;
    const cartTbody = document.getElementById('cart-items-container');
    const itemTemplate = document.getElementById('cart-item-template');

    if (!cartState || !cartTbody || !itemTemplate) return;

    const summarySubtotal = document.getElementById('summary-subtotal');
    const summaryTotal = document.getElementById('summary-total');
    const summaryQuantity = document.getElementById('summary-quantity');
    const checkoutLink = document.getElementById('checkout-link');
    const formatCurrency = (number) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(number);
    const escapeHtml = (value) => String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');

    const renderCart = (cart) => {
        const items = cart?.items || [];
        const total = Number(cart?.total_price || 0);
        const totalQuantity = Number(cart?.total_quantity || 0);
        cartTbody.innerHTML = '';

        if (!items.length) {
            cartTbody.innerHTML = `
                <div class="p-10 text-center">
                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-blue-50 text-blue-700">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <p class="text-base font-bold text-gray-900">Giỏ hàng của bạn đang trống.</p>
                    <p class="mt-1 text-sm text-gray-500">Chọn sản phẩm trước rồi quay lại đây nhé.</p>
                </div>`;
        } else {
            let html = '';
            items.forEach((item) => {
                const unitPrice = Number(item.price || 0);
                const qty = Number(item.quantity || 1);
                const variantText = item.variant_text || '';
                const productType = item.product_type === 'variable' ? 'Có biến thể' : 'Sản phẩm đơn';
                html += itemTemplate.innerHTML
                    .replace(/__ID__/g, escapeHtml(item.id))
                    .replace(/__URL__/g, escapeHtml(item.url || (item.slug ? `/san-pham/${item.slug}` : '#')))
                    .replace(/__IMAGE__/g, escapeHtml(item.image || ''))
                    .replace(/__NAME__/g, escapeHtml(item.name || 'Sản phẩm'))
                    .replace(/__PRODUCT_TYPE__/g, escapeHtml(productType))
                    .replace(/__VARIANT__/g, escapeHtml(variantText || 'Không có biến thể'))
                    .replace(/__PRICE_RAW__/g, unitPrice)
                    .replace(/__PRICE__/g, formatCurrency(unitPrice))
                    .replace(/__QUANTITY__/g, qty)
                    .replace(/__SUBTOTAL__/g, formatCurrency(unitPrice * qty));
            });
            cartTbody.innerHTML = html;
            cartTbody.querySelectorAll('.variant-pill').forEach((el) => {
                if (el.textContent.trim() === 'Không có biến thể') {
                    el.classList.add('hidden');
                }
            });
        }

        if (summaryQuantity) summaryQuantity.textContent = String(totalQuantity);
        if (summarySubtotal) summarySubtotal.textContent = formatCurrency(total);
        if (summaryTotal) summaryTotal.textContent = formatCurrency(total);
        if (checkoutLink) {
            checkoutLink.classList.toggle('pointer-events-none', !items.length);
            checkoutLink.classList.toggle('opacity-50', !items.length);
        }
    };

    cartState.onUpdated(renderCart);

    cartTbody.addEventListener('click', function (event) {
        const plusBtn = event.target.closest('.btn-plus');
        const minusBtn = event.target.closest('.btn-minus');
        const removeBtn = event.target.closest('.remove-item-btn');

        if (plusBtn || minusBtn) {
            event.preventDefault();
            const row = event.target.closest('.cart-item-row');
            const input = row?.querySelector('.quantity-input');
            if (!row || !input) return;

            let quantity = parseInt(input.value || '0', 10) || 0;
            quantity = plusBtn ? quantity + 1 : Math.max(0, quantity - 1);
            input.value = String(quantity);

            cartState.updateItem({ itemId: row.dataset.id, quantity }).catch((error) => {
                cartState.handleError(error, 'Không thể cập nhật số lượng.');
            });
            return;
        }

        if (removeBtn) {
            event.preventDefault();
            const row = removeBtn.closest('.cart-item-row');
            if (!row) return;
            if (!confirm('Bạn chắc chắn muốn xóa sản phẩm này?')) return;

            cartState.removeItem({ itemId: row.dataset.id }).catch((error) => {
                cartState.handleError(error, 'Không thể xóa sản phẩm, vui lòng thử lại.');
            });
        }
    });

    cartState.load().catch((error) => {
        cartState.handleError(error, 'Không thể tải dữ liệu giỏ hàng.');
    });
});
