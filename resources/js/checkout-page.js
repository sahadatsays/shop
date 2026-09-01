import { applyCoupon, removeCoupon } from './cart-api';

export const initCheckoutPage = () => {
    const checkout = document.querySelector('[data-checkout]');

    if (!checkout) {
        return;
    }

    const taxRate = Number(checkout.dataset.taxRate) || 0;
    const currencySymbol = checkout.dataset.currencySymbol || '$';
    const form = checkout.querySelector('form');
    const placeOrderButton = checkout.querySelector('[data-place-order]');
    const placeOrderLabel = checkout.querySelector('[data-place-order-label]');
    const termsCheckbox = checkout.querySelector('[data-terms-checkbox]');

    const formatMoney = (value) => `${currencySymbol}${value.toFixed(2)}`;

    let discountCents = Number(checkout.dataset.discountCents ?? 0);

    const subtotal = Number(checkout.dataset.subtotal) || [...checkout.querySelectorAll('[data-item-total]')].reduce(
        (sum, item) => sum + Number(item.dataset.price) * Number(item.dataset.qty),
        0,
    );

    const promoForm = checkout.querySelector('[data-promo-form]');
    const promoInput = checkout.querySelector('[data-promo-input]');
    const promoError = checkout.querySelector('[data-promo-error]');
    const promoApplied = checkout.querySelector('[data-promo-applied]');
    const promoAppliedLabel = checkout.querySelector('[data-promo-applied-label]');
    const discountRow = checkout.querySelector('[data-total-discount-row]');
    const discountLabel = checkout.querySelector('[data-total-discount-label]');
    const discountValue = checkout.querySelector('[data-total-discount]');

    const updateDiscountUi = (payload) => {
        const cart = payload?.cart;

        if (!cart) {
            return;
        }

        discountCents = cart.discount_cents ?? 0;
        checkout.dataset.discountCents = String(discountCents / 100);

        const hasDiscount = discountCents > 0;

        if (promoForm) {
            promoForm.hidden = hasDiscount;
        }

        if (promoApplied) {
            promoApplied.hidden = !hasDiscount;
        }

        if (promoAppliedLabel && cart.discount_label) {
            promoAppliedLabel.textContent = cart.discount_label;
        }

        if (discountRow) {
            discountRow.hidden = !hasDiscount;
        }

        if (discountLabel && cart.coupon_code) {
            discountLabel.textContent = `Coupon (${cart.coupon_code})`;
        }

        if (discountValue && cart.discount) {
            discountValue.textContent = cart.discount;
        }
    };

    const recalculate = () => {
        const delivery = checkout.querySelector('[data-delivery-option]:checked');
        const shipping = Number(delivery?.dataset.cost) || 0;
        const discount = discountCents / 100;
        const tax = (subtotal - discount) * taxRate;
        const total = subtotal - discount + shipping + tax;

        checkout.querySelector('[data-total-subtotal]').textContent = formatMoney(subtotal);
        checkout.querySelector('[data-total-shipping]').textContent = shipping === 0 ? 'Free' : formatMoney(shipping);
        checkout.querySelector('[data-total-tax]').textContent = formatMoney(tax);
        checkout.querySelector('[data-total-grand]').textContent = formatMoney(total);

        if (discountRow) {
            discountRow.hidden = discount <= 0;
        }

        if (discountValue) {
            discountValue.textContent = discount > 0 ? `\u2212${formatMoney(discount)}` : '';
        }

        if (placeOrderLabel) {
            placeOrderLabel.textContent = `Place order · ${formatMoney(total)}`;
        }
    };

    const billingSame = checkout.querySelector('[data-billing-same]');
    const billingFields = checkout.querySelector('[data-billing-fields]');

    billingSame?.addEventListener('change', () => {
        billingFields.hidden = billingSame.checked;
    });

    checkout.querySelectorAll('[data-delivery-option]').forEach((radio) => {
        radio.addEventListener('change', recalculate);
    });

    const paymentPanels = [...checkout.querySelectorAll('[data-payment-panel]')];

    checkout.querySelectorAll('[data-payment-option]').forEach((radio) => {
        radio.addEventListener('change', () => {
            paymentPanels.forEach((panel) => {
                panel.hidden = panel.dataset.paymentPanel !== radio.value;
            });
        });
    });

    checkout.querySelector('[data-card-number]')?.addEventListener('input', (event) => {
        const digits = event.target.value.replace(/\D/g, '').slice(0, 16);
        event.target.value = digits.replace(/(\d{4})(?=\d)/g, '$1 ');
    });

    checkout.querySelector('[data-card-expiry]')?.addEventListener('input', (event) => {
        const digits = event.target.value.replace(/\D/g, '').slice(0, 4);
        event.target.value = digits.length > 2 ? `${digits.slice(0, 2)} / ${digits.slice(2)}` : digits;
    });

    checkout.querySelector('[data-promo-apply]')?.addEventListener('click', async () => {
        const code = promoInput.value.trim();

        if (!code) {
            return;
        }

        promoError.hidden = true;

        try {
            const payload = await applyCoupon(code);
            promoInput.value = '';
            updateDiscountUi(payload);
            recalculate();
        } catch (error) {
            promoError.textContent = error.message;
            promoError.hidden = false;
        }
    });

    checkout.querySelector('[data-promo-remove]')?.addEventListener('click', async () => {
        try {
            const payload = await removeCoupon();
            updateDiscountUi(payload);
            promoInput.focus();
            recalculate();
        } catch (error) {
            promoError.textContent = error.message;
            promoError.hidden = false;
        }
    });

    promoInput?.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            checkout.querySelector('[data-promo-apply]')?.click();
        }
    });

    const giftToggle = checkout.querySelector('[data-gift-toggle]');
    const giftFields = checkout.querySelector('[data-gift-fields]');

    giftToggle?.addEventListener('change', () => {
        giftFields.hidden = !giftToggle.checked;
    });

    termsCheckbox?.addEventListener('change', () => {
        placeOrderButton.disabled = !termsCheckbox.checked;
    });

    form?.addEventListener('submit', () => {
        if (placeOrderButton) {
            placeOrderButton.disabled = true;
        }

        if (placeOrderLabel) {
            placeOrderLabel.textContent = 'Placing order…';
        }
    });

    recalculate();
};
