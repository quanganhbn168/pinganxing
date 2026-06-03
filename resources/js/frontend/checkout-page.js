document.addEventListener('DOMContentLoaded', function () {
    const cartState = window.CartState;
    const cartContainer = document.getElementById('order-summary-list');
    const form = document.getElementById('checkout-form');

    if (!cartState || !cartContainer || !form) return;

    const submitBtn = form.querySelector('button[type="submit"]');
    const summarySubtotal = document.getElementById('summary-subtotal');
    const summaryTotal = document.getElementById('summary-total');
    const formatCurrency = (number) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(number);
    const escapeHtml = (value) => String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');

    const renderSummary = (cart) => {
        const items = cart?.items || [];
        cartContainer.innerHTML = '';

        if (!items.length) {
            cartContainer.innerHTML = '<div class="py-6 text-center text-sm font-semibold text-gray-500">Giỏ hàng trống</div>';
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            if (summarySubtotal) summarySubtotal.textContent = formatCurrency(0);
            if (summaryTotal) summaryTotal.textContent = formatCurrency(0);
            return;
        }

        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');

        let total = 0;
        items.forEach((item) => {
            const productName = item.name || 'Sản phẩm';
            const unitPrice = Number(item.price || 0);
            const qty = Number(item.quantity || 1);
            const subtotal = unitPrice * qty;
            total += subtotal;

            const variantText = item.variant_text || '';
            const productType = item.product_type === 'variable' ? 'Có biến thể' : 'Sản phẩm đơn';
            const itemHtml = `
                <div class="flex gap-3 py-4">
                    <img src="${escapeHtml(item.image || '')}" alt="${escapeHtml(productName)}" class="h-16 w-16 shrink-0 rounded-lg border border-gray-100 bg-gray-50 object-contain p-1 dark:border-gray-800 dark:bg-gray-800">
                    <div class="min-w-0 flex-1">
                        <div class="line-clamp-2 text-sm font-bold text-gray-950 dark:text-white">${escapeHtml(productName)}</div>
                        <div class="mt-1 flex flex-wrap gap-1.5 text-[11px] font-semibold">
                            <span class="rounded-full bg-gray-100 px-2 py-0.5 text-gray-600 dark:bg-gray-800 dark:text-gray-300">${escapeHtml(productType)}</span>
                            ${variantText ? `<span class="rounded-full bg-blue-50 px-2 py-0.5 text-blue-700 dark:bg-blue-950 dark:text-blue-300">${escapeHtml(variantText)}</span>` : ''}
                        </div>
                        <div class="mt-2 flex items-center justify-between gap-2">
                            <span class="text-xs font-semibold text-gray-500">SL: ${qty} x ${formatCurrency(unitPrice)}</span>
                            <span class="item-total text-sm font-bold text-blue-700 dark:text-blue-400" data-total="${subtotal}">${formatCurrency(subtotal)}</span>
                        </div>
                    </div>
                </div>`;
            cartContainer.insertAdjacentHTML('beforeend', itemHtml);
        });

        if (summarySubtotal) summarySubtotal.textContent = formatCurrency(total);
        if (summaryTotal) summaryTotal.textContent = formatCurrency(total);
    };

    cartState.onUpdated(renderSummary);
    cartState.load().catch((error) => {
        cartState.handleError(error, 'Không thể tải dữ liệu giỏ hàng.');
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const phoneInput = document.getElementById('customer_phone');
        const nameInput = document.getElementById('customer_name');
        const addrInput = document.getElementById('customer_address');
        const phoneRegex = /^(0[3|5|7|8|9])[0-9]{8}$|^\+84[3|5|7|8|9][0-9]{8}$/;
        let isValid = true;

        form.querySelectorAll('.validation-err').forEach((el) => el.remove());
        form.querySelectorAll('.checkout-field-error').forEach((el) => {
            el.classList.remove('checkout-field-error');
        });
        const showError = (input, message) => {
            isValid = false;
            input.classList.add('checkout-field-error');
            const err = document.createElement('small');
            err.className = 'text-red-600 validation-err block mt-1 text-xs font-semibold';
            err.innerText = message;
            input.parentNode.appendChild(err);
        };

        if (!nameInput.value || nameInput.value.length < 2) {
            showError(nameInput, 'Vui lòng nhập họ tên hợp lệ.');
        }

        if (!phoneInput.value || !phoneRegex.test(phoneInput.value)) {
            showError(phoneInput, 'Số điện thoại không hợp lệ (ví dụ: 098xxxxxxx)');
        }

        if (!addrInput.value || addrInput.value.length < 10) {
            showError(addrInput, 'Vui lòng nhập địa chỉ cụ thể.');
        }

        if (isValid) {
            form.submit();
        }
    });
});
