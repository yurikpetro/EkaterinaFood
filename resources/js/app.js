const formatPrice = (amount) =>
    new Intl.NumberFormat('ru-RU').format(amount) + ' ₽';

const initFloatingCart = () => {
    const toggle = document.getElementById('cart-floating-toggle');
    const panel = document.getElementById('cart-floating-panel');
    const chevron = document.getElementById('cart-floating-chevron');
    const footer = document.querySelector('footer');

    if (!toggle || !panel) {
        return;
    }

    const setExpanded = (expanded) => {
        panel.classList.toggle('hidden', !expanded);
        toggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
        chevron?.classList.toggle('rotate-180', expanded);
        document.body.classList.toggle('pb-36', expanded);
        document.body.classList.toggle('sm:pb-32', expanded);
    };

    setExpanded(true);

    toggle.addEventListener('click', () => {
        const isHidden = panel.classList.contains('hidden');
        setExpanded(isHidden);
    });

    if (footer) {
        footer.classList.add('mb-28', 'sm:mb-24');
    }
};

const initCartPage = () => {
    const page = document.getElementById('cart-page');
    const itemsContainer = document.getElementById('cart-items');

    if (!page || !itemsContainer) {
        return;
    }

    const totalEl = page.querySelector('[data-cart-total]');
    const updateUrl = page.dataset.updateUrl;
    const csrf = page.dataset.csrf;
    const syncTimers = new Map();

    const updateHeaderBadge = (count) => {
        const cartLink = document.querySelector('header a[href*="cart"]');
        if (!cartLink) {
            return;
        }

        let badge = cartLink.querySelector('span.rounded-full');

        if (count > 0) {
            if (!badge) {
                badge = document.createElement('span');
                badge.className =
                    'bg-terracotta text-white text-sm font-bold min-w-[1.5rem] h-6 px-1.5 rounded-full flex items-center justify-center';
                cartLink.appendChild(badge);
            }
            badge.textContent = String(count);
        } else if (badge) {
            badge.remove();
        }
    };

    const recalcLocal = () => {
        let total = 0;

        page.querySelectorAll('[data-cart-item]').forEach((row) => {
            const input = row.querySelector('[data-cart-quantity]');
            const unitPrice = Number(row.dataset.unitPrice);
            const minQty = Number(row.dataset.minQuantity);
            const qty = parseInt(input.value, 10) || 0;
            const effectiveQty = qty >= minQty ? qty : 0;
            const subtotal = effectiveQty * unitPrice;

            row.querySelector('[data-cart-subtotal]').textContent = formatPrice(subtotal);
            total += subtotal;
        });

        if (totalEl) {
            totalEl.textContent = formatPrice(total);
        }
    };

    const applyServerState = (data) => {
        const serverItems = new Map(
            data.items.map((item) => [String(item.product_id), item]),
        );

        page.querySelectorAll('[data-cart-item]').forEach((row) => {
            const productId = row.dataset.productId;
            const serverItem = serverItems.get(productId);

            if (!serverItem) {
                row.remove();
                return;
            }

            const input = row.querySelector('[data-cart-quantity]');
            input.value = serverItem.quantity;
            row.querySelector('[data-cart-subtotal]').textContent = formatPrice(serverItem.subtotal);
        });

        if (totalEl) {
            totalEl.textContent = formatPrice(data.total);
        }

        updateHeaderBadge(data.cartCount);

        if (data.items.length === 0) {
            window.location.reload();
        }
    };

    const syncToServer = async (productId, quantity) => {
        const body = new FormData();
        body.append('_token', csrf);
        body.append('product_id', productId);
        body.append('quantity', quantity);

        try {
            const response = await fetch(updateUrl, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body,
            });

            if (!response.ok) {
                window.location.reload();
                return;
            }

            applyServerState(await response.json());
        } catch {
            window.location.reload();
        }
    };

    const scheduleSync = (productId, quantity) => {
        const key = productId;
        clearTimeout(syncTimers.get(key));

        syncTimers.set(
            key,
            setTimeout(() => {
                syncTimers.delete(key);
                syncToServer(productId, quantity);
            }, 400),
        );
    };

    page.querySelectorAll('[data-cart-quantity]').forEach((input) => {
        const row = input.closest('[data-cart-item]');
        const productId = row.dataset.productId;

        input.addEventListener('input', () => {
            recalcLocal();
            scheduleSync(productId, input.value);
        });

        input.addEventListener('change', () => {
            recalcLocal();
            clearTimeout(syncTimers.get(productId));
            syncTimers.delete(productId);
            syncToServer(productId, input.value);
        });
    });
};

document.addEventListener('DOMContentLoaded', () => {
    initFloatingCart();
    initCartPage();
});
