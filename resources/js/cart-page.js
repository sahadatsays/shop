import { removeCartItem, saveCart, updateCartItem } from './cart-api';

const COUPON_CODE = 'VALOR10';
const COUPON_RATE = 0.1;

const formatMoney = (value) => `$${value.toFixed(2)}`;

const showCartError = (cart, message) => {
    let alert = cart.querySelector('[data-cart-error]');

    if (!alert) {
        alert = document.createElement('div');
        alert.dataset.cartError = '';
        alert.className = 'mt-6 rounded-card border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-800';
        cart.querySelector('h1')?.parentElement?.after(alert);
    }

    alert.textContent = message;
    alert.hidden = false;
};

export const initCartPage = () => {
    const cart = document.querySelector('[data-cart]');

    if (!cart) {
        return;
    }

    const threshold = Number(cart.dataset.freeShippingThreshold) || 75;
    const taxRate = Number(cart.dataset.taxRate) || 0;
    const flatShipping = Number(cart.dataset.flatShipping) || 0;

    const itemsList = cart.querySelector('[data-cart-items]');
    const emptyState = cart.querySelector('[data-cart-empty]');
    const couponSection = cart.querySelector('[data-coupon-section]');
    const couponForm = cart.querySelector('[data-coupon-form]');
    const couponInput = cart.querySelector('[data-coupon-input]');
    const couponError = cart.querySelector('[data-coupon-error]');
    const couponApplied = cart.querySelector('[data-coupon-applied]');
    const shippingBar = cart.querySelector('[data-shipping-bar]');
    const shippingMessage = cart.querySelector('[data-shipping-message]');
    const shippingProgress = cart.querySelector('[data-shipping-progress]');
    const saveButton = cart.querySelector('[data-save-cart]');

    let couponActive = false;

    const items = () => [...itemsList.querySelectorAll('[data-cart-item]')];

    const updateShippingBar = (subtotal) => {
        if (!shippingBar) {
            return;
        }

        const remaining = threshold - subtotal;
        shippingProgress.style.width = `${Math.min(100, (subtotal / threshold) * 100)}%`;

        shippingMessage.innerHTML =
            remaining > 0
                ? `Add <strong class="font-semibold text-olive-800">${formatMoney(remaining)}</strong> more to unlock free express shipping.`
                : `You've unlocked <strong class="font-semibold text-olive-800">free express shipping</strong>!`;
    };

    const recalculate = () => {
        let subtotal = 0;
        let count = 0;

        items().forEach((row) => {
            const price = Number(row.dataset.price) || 0;
            const qty = Number(row.querySelector('[data-qty-input]')?.value) || 0;
            const lineTotal = price * qty;

            subtotal += lineTotal;
            count += qty;
            row.querySelector('[data-line-total]').textContent = formatMoney(lineTotal);
        });

        const discount = couponActive ? subtotal * COUPON_RATE : 0;
        const shipping = subtotal === 0 || subtotal >= threshold ? 0 : flatShipping;
        const tax = (subtotal - discount) * taxRate;
        const total = subtotal - discount + shipping + tax;

        const label = cart.querySelector('[data-cart-count-label]');
        label.textContent = `${count} ${count === 1 ? 'item' : 'items'}`;

        cart.querySelector('[data-summary-count]').textContent = String(count);
        cart.querySelector('[data-summary-subtotal]').textContent = formatMoney(subtotal);
        cart.querySelector('[data-summary-discount]').textContent = `\u2212${formatMoney(discount)}`;
        cart.querySelector('[data-summary-discount-row]').hidden = !couponActive;
        cart.querySelector('[data-summary-shipping]').textContent = shipping === 0 ? 'Free' : formatMoney(shipping);
        cart.querySelector('[data-summary-tax]').textContent = formatMoney(tax);
        cart.querySelector('[data-summary-total]').textContent = formatMoney(total);

        updateShippingBar(subtotal);

        const isEmpty = items().length === 0;
        emptyState.hidden = !isEmpty;
        couponSection.hidden = isEmpty;
        shippingBar.hidden = isEmpty;
    };

    const persistQuantity = async (row, quantity) => {
        const cartItemId = row.dataset.cartItemId;

        if (!cartItemId) {
            recalculate();
            return;
        }

        try {
            if (quantity <= 0) {
                await removeCartItem(cartItemId);
                row.remove();
            } else {
                await updateCartItem(cartItemId, quantity);
            }

            recalculate();
        } catch (error) {
            showCartError(cart, error.message);
        }
    };

    itemsList.addEventListener('click', (event) => {
        const removeButton = event.target.closest('[data-cart-remove]');

        if (removeButton) {
            const row = removeButton.closest('[data-cart-item]');
            row.classList.add('opacity-0');
            persistQuantity(row, 0);
            return;
        }

        if (event.target.closest('[data-qty-minus], [data-qty-plus]')) {
            const row = event.target.closest('[data-cart-item]');
            const input = row?.querySelector('[data-qty-input]');
            const quantity = Number(input?.value) || 1;
            persistQuantity(row, quantity);
        }
    });

    itemsList.addEventListener('change', (event) => {
        if (event.target.matches('[data-qty-input]')) {
            const row = event.target.closest('[data-cart-item]');
            persistQuantity(row, Number(event.target.value) || 1);
        }
    });

    couponForm?.addEventListener('submit', (event) => {
        event.preventDefault();

        const code = couponInput.value.trim().toUpperCase();
        const isValid = code === COUPON_CODE;

        couponError.hidden = isValid || code === '';
        couponApplied.hidden = !isValid;
        couponForm.hidden = isValid;

        if (isValid) {
            couponActive = true;
            recalculate();
        }
    });

    cart.querySelector('[data-coupon-remove]')?.addEventListener('click', () => {
        couponActive = false;
        couponApplied.hidden = true;
        couponForm.hidden = false;
        couponInput.value = '';
        couponInput.focus();
        recalculate();
    });

    saveButton?.addEventListener('click', async () => {
        try {
            await saveCart();
            saveButton.textContent = 'Cart saved';
            saveButton.disabled = true;
        } catch (error) {
            showCartError(cart, error.message);
        }
    });

    recalculate();
};
