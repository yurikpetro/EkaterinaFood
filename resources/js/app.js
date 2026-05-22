const formatPrice = (amount) =>
    new Intl.NumberFormat('ru-RU').format(amount) + ' ₽';

const formatNumber = (amount) => new Intl.NumberFormat('ru-RU').format(amount);

let toastTimer = null;

const showToast = (message) => {
    const toast = document.getElementById('site-toast');
    if (!toast) {
        return;
    }

    toast.textContent = message;
    toast.classList.remove('is-hidden');

    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => {
        toast.classList.add('is-hidden');
    }, 3000);
};

const updateHeaderBadge = (count) => {
    const cartLink = document.getElementById('header-cart-link');
    if (!cartLink) {
        return;
    }

    let badge = document.getElementById('header-cart-badge');

    if (count > 0) {
        if (!badge) {
            badge = document.createElement('span');
            badge.id = 'header-cart-badge';
            badge.className =
                'absolute -top-0.5 -right-0.5 bg-terracotta text-white text-xs font-bold min-w-[1.25rem] h-5 px-1 rounded-full flex items-center justify-center';
            cartLink.appendChild(badge);
        }
        badge.textContent = String(count);
        badge.classList.remove('cart-badge-pop');
        void badge.offsetWidth;
        badge.classList.add('cart-badge-pop');
    } else if (badge) {
        badge.remove();
    }
};

const buildFloatingItemHtml = (item) => `
    <li class="px-4 py-3 flex justify-between gap-2 text-base" data-floating-item="${item.product_id}">
        <div class="min-w-0">
            <p class="font-semibold text-warm-brown leading-snug truncate">${item.name}</p>
            <p class="text-sm text-warm-brown/80">
                ${item.quantity} ${item.unit} × ${formatNumber(item.price)} ₽
            </p>
        </div>
        <p class="font-bold text-terracotta shrink-0">${formatPrice(item.subtotal)}</p>
    </li>
`;

const renderFloatingCart = (data, expand = false) => {
    const floating = document.getElementById('cart-floating');
    if (!floating) {
        return;
    }

    if (!data.cartCount || data.items.length === 0) {
        floating.classList.add('hidden');
        return;
    }

    floating.classList.remove('hidden');

    const badge = document.getElementById('cart-floating-badge');
    const headerTotal = document.getElementById('cart-floating-total');
    const footerTotal = document.getElementById('cart-floating-footer-total');
    const list = document.getElementById('cart-floating-list');

    if (badge) {
        badge.textContent = String(data.cartCount);
    }
    if (headerTotal) {
        headerTotal.textContent = formatPrice(data.total);
    }
    if (footerTotal) {
        footerTotal.textContent = formatPrice(data.total);
    }
    if (list) {
        list.innerHTML = data.items.map(buildFloatingItemHtml).join('');
    }

    if (expand && window.setFloatingCartExpanded) {
        window.setFloatingCartExpanded(true);
    }
};

const updateInCartBadges = (items) => {
    document.querySelectorAll('[data-in-cart-badge]').forEach((el) => {
        const productId = el.dataset.inCartBadge;
        const item = items.find((i) => String(i.product_id) === productId);

        if (item) {
            el.textContent = `В корзине: ${item.quantity} ${item.unit}`;
            el.classList.remove('hidden');
        } else {
            el.textContent = '';
            el.classList.add('hidden');
        }
    });
};

const initFlashDismiss = () => {
    document.querySelectorAll('[data-flash-dismiss]').forEach((el) => {
        setTimeout(() => {
            el.style.transition = 'opacity 0.3s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 300);
        }, 6000);
    });
};

const initQuantitySteppers = () => {
    document.querySelectorAll('[data-qty-stepper]').forEach((stepper) => {
        const input = stepper.querySelector('[data-qty-input]');
        const decrement = stepper.querySelector('[data-qty-decrement]');
        const increment = stepper.querySelector('[data-qty-increment]');

        if (!input || !decrement || !increment) {
            return;
        }

        const min = parseInt(input.min, 10) || 0;
        const max = input.max !== '' ? parseInt(input.max, 10) : null;

        const clamp = (value) => {
            let qty = parseInt(value, 10);

            if (Number.isNaN(qty)) {
                qty = min;
            }

            if (qty < min) {
                qty = min;
            }

            if (max !== null && qty > max) {
                qty = max;
            }

            return qty;
        };

        const updateButtons = () => {
            const qty = parseInt(input.value, 10) || 0;
            decrement.disabled = qty <= min;
            increment.disabled = max !== null && qty >= max;
        };

        const setValue = (nextValue) => {
            input.value = String(clamp(nextValue));
            updateButtons();
            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.dispatchEvent(new Event('change', { bubbles: true }));
        };

        decrement.addEventListener('click', () => {
            setValue((parseInt(input.value, 10) || min) - 1);
        });

        increment.addEventListener('click', () => {
            setValue((parseInt(input.value, 10) || min) + 1);
        });

        input.addEventListener('input', updateButtons);

        input.addEventListener('change', () => {
            input.value = String(clamp(input.value));
            updateButtons();
        });

        updateButtons();
    });
};

const initFloatingCart = () => {
    const toggle = document.getElementById('cart-floating-toggle');
    const panel = document.getElementById('cart-floating-panel');
    const chevron = document.getElementById('cart-floating-chevron');
    const footer = document.getElementById('site-footer');
    const floating = document.getElementById('cart-floating');

    if (!toggle || !panel) {
        return;
    }

    const updateFooterMargin = (expanded) => {
        if (!footer) {
            return;
        }

        footer.classList.toggle('mb-20', !!floating && !floating.classList.contains('hidden'));
        footer.classList.toggle('sm:mb-16', !!floating && !floating.classList.contains('hidden'));
        if (expanded) {
            footer.classList.add('mb-28', 'sm:mb-24');
        } else {
            footer.classList.remove('mb-28', 'sm:mb-24');
        }
    };

    const setExpanded = (expanded) => {
        panel.classList.toggle('hidden', !expanded);
        toggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
        chevron?.classList.toggle('rotate-180', expanded);
        document.body.classList.toggle('pb-36', expanded);
        document.body.classList.toggle('sm:pb-32', expanded);
        updateFooterMargin(expanded);
    };

    window.setFloatingCartExpanded = setExpanded;
    setExpanded(false);
    updateFooterMargin(false);

    toggle.addEventListener('click', () => {
        const isHidden = panel.classList.contains('hidden');
        setExpanded(isHidden);
    });
};

const initMenuCategoryNav = () => {
    const chips = document.querySelectorAll('[data-category-chip]');
    const sections = document.querySelectorAll('[data-category-section]');

    if (!chips.length || !sections.length) {
        return;
    }

    const setActiveChip = (id) => {
        chips.forEach((chip) => {
            chip.classList.toggle('menu-category-chip--active', chip.dataset.categoryChip === String(id));
        });
    };

    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver(
            (entries) => {
                const visible = entries
                    .filter((e) => e.isIntersecting)
                    .sort((a, b) => b.intersectionRatio - a.intersectionRatio)[0];

                if (visible) {
                    setActiveChip(visible.target.dataset.categorySection);
                }
            },
            { rootMargin: '-40% 0px -50% 0px', threshold: [0, 0.25, 0.5] },
        );

        sections.forEach((section) => observer.observe(section));
    }

    chips.forEach((chip) => {
        chip.addEventListener('click', () => {
            setActiveChip(chip.dataset.categoryChip);
        });
    });
};

const initMenuAddToCart = () => {
    const page = document.getElementById('menu-page');
    if (!page) {
        return;
    }

    const addUrl = page.dataset.cartAddUrl;
    const csrf = page.dataset.csrf;

    page.querySelectorAll('[data-add-to-cart-form]').forEach((form) => {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const button = form.querySelector('button[type="submit"]');
            const originalText = button?.textContent;
            if (button) {
                button.disabled = true;
                button.textContent = 'Добавляем…';
            }

            const body = new FormData(form);

            try {
                const response = await fetch(addUrl, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body,
                });

                if (!response.ok) {
                    throw new Error('add failed');
                }

                const data = await response.json();
                updateHeaderBadge(data.cartCount);
                renderFloatingCart(data, true);
                updateInCartBadges(data.items);
                showToast('Добавлено в корзину');
            } catch {
                form.submit();
            } finally {
                if (button) {
                    button.disabled = false;
                    button.textContent = originalText;
                }
            }
        });
    });
};

const initCartPage = () => {
    const page = document.getElementById('cart-page');
    const itemsContainer = document.getElementById('cart-items');

    if (!page || !itemsContainer) {
        return;
    }

    const totalEl = page.querySelector('[data-cart-total]');
    const emptyState = document.getElementById('cart-empty-state');
    const filledState = document.getElementById('cart-filled-state');
    const syncError = document.getElementById('cart-sync-error');
    const updateUrl = page.dataset.updateUrl;
    const csrf = page.dataset.csrf;
    const syncTimers = new Map();

    const showSyncError = (message) => {
        if (!syncError) {
            return;
        }
        syncError.textContent = message;
        syncError.classList.remove('hidden');
    };

    const hideSyncError = () => {
        syncError?.classList.add('hidden');
    };

    const showEmptyCart = () => {
        filledState?.classList.add('hidden');
        emptyState?.classList.remove('hidden');
        updateHeaderBadge(0);
        renderFloatingCart({ cartCount: 0, total: 0, items: [] });
    };

    const updateMinHint = (row) => {
        const input = row.querySelector('[data-cart-quantity]');
        const hint = row.querySelector('[data-cart-min-hint]');
        const minQty = Number(row.dataset.minQuantity);
        const qty = parseInt(input?.value, 10) || 0;

        if (!hint || minQty <= 1) {
            return;
        }

        hint.classList.toggle('hidden', qty === 0 || qty >= minQty);
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
            updateMinHint(row);
        });

        if (totalEl) {
            totalEl.textContent = formatPrice(total);
        }
    };

    const applyServerState = (data) => {
        hideSyncError();
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
            updateMinHint(row);
        });

        if (totalEl) {
            totalEl.textContent = formatPrice(data.total);
        }

        updateHeaderBadge(data.cartCount);
        renderFloatingCart(data);

        if (data.items.length === 0) {
            showEmptyCart();
        }
    };

    const syncToServer = async (productId, quantity, row) => {
        const body = new FormData();
        body.append('_token', csrf);
        body.append('product_id', productId);
        body.append('quantity', quantity);

        row?.classList.add('cart-syncing');

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
                throw new Error('sync failed');
            }

            applyServerState(await response.json());
        } catch {
            showSyncError('Не удалось обновить корзину. Проверьте соединение и попробуйте ещё раз.');
        } finally {
            row?.classList.remove('cart-syncing');
        }
    };

    const scheduleSync = (productId, quantity, row) => {
        clearTimeout(syncTimers.get(productId));

        syncTimers.set(
            productId,
            setTimeout(() => {
                syncTimers.delete(productId);
                syncToServer(productId, quantity, row);
            }, 400),
        );
    };

    page.querySelectorAll('[data-cart-item]').forEach((row) => {
        updateMinHint(row);
    });

    page.querySelectorAll('[data-cart-quantity]').forEach((input) => {
        const row = input.closest('[data-cart-item]');
        const productId = row.dataset.productId;

        input.addEventListener('input', () => {
            recalcLocal();
            row.classList.add('cart-syncing');
            scheduleSync(productId, input.value, row);
        });

        input.addEventListener('change', () => {
            recalcLocal();
            clearTimeout(syncTimers.get(productId));
            syncTimers.delete(productId);
            syncToServer(productId, input.value, row);
        });
    });

    recalcLocal();
};

const initCheckout = () => {
    const form = document.getElementById('checkout-form');
    if (!form) {
        return;
    }

    const addressField = document.getElementById('address-field');
    const addressInput = document.getElementById('address');
    const radios = form.querySelectorAll('[data-delivery-radio]');

    const syncDeliveryFields = () => {
        const isPickup = form.querySelector('[data-delivery-radio]:checked')?.value === 'pickup';

        if (isPickup) {
            addressField?.classList.add('hidden');
            if (addressInput) {
                addressInput.disabled = true;
                addressInput.removeAttribute('required');
            }
        } else {
            addressField?.classList.remove('hidden');
            if (addressInput) {
                addressInput.disabled = false;
            }
        }
    };

    radios.forEach((radio) => {
        radio.addEventListener('change', syncDeliveryFields);
    });

    syncDeliveryFields();

    document.querySelectorAll('.time-preset').forEach((btn) => {
        btn.addEventListener('click', () => {
            const input = document.getElementById('desired_time');
            if (input) {
                input.value = btn.dataset.timeValue;
            }
        });
    });

    const setSubmitting = (submitting) => {
        const buttons = [
            document.getElementById('checkout-submit'),
            document.getElementById('checkout-submit-mobile'),
        ].filter(Boolean);

        buttons.forEach((btn) => {
            btn.disabled = submitting;
            if (submitting) {
                btn.dataset.originalText = btn.textContent;
                btn.textContent = 'Отправляем…';
            } else if (btn.dataset.originalText) {
                btn.textContent = btn.dataset.originalText;
            }
        });
    };

    form.addEventListener('submit', () => {
        setSubmitting(true);
    });
};

document.addEventListener('DOMContentLoaded', () => {
    initFlashDismiss();
    initQuantitySteppers();
    initFloatingCart();
    initMenuCategoryNav();
    initMenuAddToCart();
    initCartPage();
    initCheckout();
});
