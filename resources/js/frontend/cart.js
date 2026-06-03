document.addEventListener('DOMContentLoaded', function() {
    const cartState = window.CartState;
    if (!cartState) return;

    const offcanvasBody = document.querySelector('.cart-offcanvas-wrapper .offcanvas-body');
    const offcanvasFooter = document.querySelector('.cart-offcanvas-wrapper .offcanvas-footer');
    const cartCountSpan = document.querySelector('.cart-action .cart-count');
    const cartTotalSpan = document.querySelector('.cart-offcanvas-wrapper .total-price');
    const itemTemplate = document.getElementById('guest-cart-item-template'); 
    const cartDrawer = document.getElementById('cart-drawer');
    const cartDrawerOverlay = document.querySelector('.cart-drawer-overlay');

    const openCartDrawer = () => {
        if (!cartDrawer || !cartDrawerOverlay) return;

        cartDrawerOverlay.classList.remove('hidden');
        cartDrawer.classList.remove('translate-x-full');
        cartDrawer.classList.add('translate-x-0');
        cartDrawer.setAttribute('aria-hidden', 'false');
        requestAnimationFrame(() => cartDrawerOverlay.classList.remove('opacity-0'));
    };

    const closeCartDrawer = () => {
        if (!cartDrawer || !cartDrawerOverlay) return;

        cartDrawer.classList.add('translate-x-full');
        cartDrawer.classList.remove('translate-x-0');
        cartDrawer.setAttribute('aria-hidden', 'true');
        cartDrawerOverlay.classList.add('opacity-0');
        window.setTimeout(() => cartDrawerOverlay.classList.add('hidden'), 220);
    };

    const renderOffCanvasCart = (cartData) => {
        if (!offcanvasBody || !itemTemplate) return;
        offcanvasBody.innerHTML = '';
        const { items = [], total_quantity = 0, total_price = 0 } = cartData;
        
        if (items.length === 0) {
            offcanvasBody.innerHTML = '<p class="text-center p-4">Giỏ hàng của bạn đang trống.</p>';
            if(offcanvasFooter) offcanvasFooter.style.display = 'none';
        } else {
            let html = '';
            items.forEach(item => {
                const itemId = item.id;
                const itemHtml = itemTemplate.innerHTML
                .replace(/__ID__/g, itemId) 
                .replace(/__NAME__/g, item.name)
                .replace(/__PRICE__/g, Number(item.price).toLocaleString('vi-VN'))
                .replace(/__QUANTITY__/g, item.quantity)
                .replace(/__IMAGE__/g, item.image)
                .replace(/__URL__/g, item.url || `/san-pham/${item.slug}`)
                .replace(/__VARIANT__/g, item.variant_text || '');
                html += itemHtml;
            });
            offcanvasBody.innerHTML = html;
            if(offcanvasFooter) offcanvasFooter.style.display = 'block';
        }
        if (cartCountSpan) cartCountSpan.innerText = total_quantity;
        if (cartTotalSpan) cartTotalSpan.innerText = Number(total_price).toLocaleString('vi-VN') + 'đ';
    };

    cartState.onUpdated((cart) => {
        renderOffCanvasCart(cart);
    });

    document.addEventListener('click', function(e) {
        const btnAdd = e.target.closest('.btn-add-to-cart');
        if (btnAdd) {
            e.preventDefault();
            const el = btnAdd;
            const quantity  = parseInt(el.dataset.quantity || '1', 10) || 1;
            const productId = el.dataset.id;
            const variantId = el.dataset.variantId || null;
            const originalText = el.innerHTML;
            el.disabled = true;
            cartState
                .addItem({ productId, variantId, quantity })
                .then(() => openCartDrawer())
                .catch((error) => cartState.handleError(error))
                .finally(() => {
                    el.disabled = false;
                    el.innerHTML = originalText;
                });
        }

        const btnRemove = e.target.closest('.cart-offcanvas-wrapper .item-remove');
        if (btnRemove) {
            e.preventDefault();
            const itemId = btnRemove.dataset.itemId;
            if (!itemId) return;
            cartState.removeItem({ itemId }).catch((error) => {
                cartState.handleError(error, 'Không thể xóa sản phẩm, vui lòng thử lại.');
            });
        }

        if (e.target.closest('[data-cart-drawer-close]')) {
            e.preventDefault();
            closeCartDrawer();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeCartDrawer();
        }
    });

    cartState.load().catch((error) => {
        cartState.handleError(error, 'Không thể tải dữ liệu giỏ hàng.');
    });
});
