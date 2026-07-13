const PROMO_CODE = 'VALOR10';
const PROMO_RATE = 0.1;

const formatMoney = (value) => `$${value.toFixed(2)}`;

export const initCheckoutPage = () => {
    const checkout = document.querySelector('[data-checkout]');

    if (!checkout) {
        return;
    }

    const taxRate = Number(checkout.dataset.taxRate) || 0;
    const form = checkout.querySelector('form');
    const placeOrderButton = checkout.querySelector('[data-place-order]');
    const placeOrderLabel = checkout.querySelector('[data-place-order-label]');
    const termsCheckbox = checkout.querySelector('[data-terms-checkbox]');

    let promoActive = false;

    /* Totals — subtotal from the summary items, shipping from the delivery radios */
    const subtotal = [...checkout.querySelectorAll('[data-item-total]')].reduce(
        (sum, item) => sum + Number(item.dataset.price) * Number(item.dataset.qty),
        0,
    );

    const recalculate = () => {
        const delivery = checkout.querySelector('[data-delivery-option]:checked');
        const shipping = Number(delivery?.dataset.cost) || 0;
        const discount = promoActive ? subtotal * PROMO_RATE : 0;
        const tax = (subtotal - discount) * taxRate;
        const total = subtotal - discount + shipping + tax;

        checkout.querySelector('[data-total-subtotal]').textContent = formatMoney(subtotal);
        checkout.querySelector('[data-total-discount]').textContent = `\u2212${formatMoney(discount)}`;
        checkout.querySelector('[data-total-discount-row]').hidden = !promoActive;
        checkout.querySelector('[data-total-shipping]').textContent = shipping === 0 ? 'Free' : formatMoney(shipping);
        checkout.querySelector('[data-total-tax]').textContent = formatMoney(tax);
        checkout.querySelector('[data-total-grand]').textContent = formatMoney(total);
        placeOrderLabel.textContent = `Pay ${formatMoney(total)} securely`;
    };

    /* Billing address toggle */
    const billingSame = checkout.querySelector('[data-billing-same]');
    const billingFields = checkout.querySelector('[data-billing-fields]');

    billingSame?.addEventListener('change', () => {
        billingFields.hidden = billingSame.checked;
    });

    /* Delivery method */
    checkout.querySelectorAll('[data-delivery-option]').forEach((radio) => {
        radio.addEventListener('change', recalculate);
    });

    /* Payment method panels */
    const paymentPanels = [...checkout.querySelectorAll('[data-payment-panel]')];

    checkout.querySelectorAll('[data-payment-option]').forEach((radio) => {
        radio.addEventListener('change', () => {
            paymentPanels.forEach((panel) => {
                panel.hidden = panel.dataset.paymentPanel !== radio.value;
            });
        });
    });

    /* Card input formatting */
    checkout.querySelector('[data-card-number]')?.addEventListener('input', (event) => {
        const digits = event.target.value.replace(/\D/g, '').slice(0, 16);
        event.target.value = digits.replace(/(\d{4})(?=\d)/g, '$1 ');
    });

    checkout.querySelector('[data-card-expiry]')?.addEventListener('input', (event) => {
        const digits = event.target.value.replace(/\D/g, '').slice(0, 4);
        event.target.value = digits.length > 2 ? `${digits.slice(0, 2)} / ${digits.slice(2)}` : digits;
    });

    /* Promo code */
    const promoInput = checkout.querySelector('[data-promo-input]');
    const promoError = checkout.querySelector('[data-promo-error]');
    const promoApplied = checkout.querySelector('[data-promo-applied]');

    checkout.querySelector('[data-promo-apply]')?.addEventListener('click', () => {
        const code = promoInput.value.trim().toUpperCase();
        const isValid = code === PROMO_CODE;

        promoError.hidden = isValid || code === '';
        promoApplied.hidden = !isValid;

        if (isValid) {
            promoActive = true;
            promoInput.value = '';
            recalculate();
        }
    });

    checkout.querySelector('[data-promo-remove]')?.addEventListener('click', () => {
        promoActive = false;
        promoApplied.hidden = true;
        promoInput.focus();
        recalculate();
    });

    promoInput?.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            checkout.querySelector('[data-promo-apply]')?.click();
        }
    });

    /* Gift option */
    const giftToggle = checkout.querySelector('[data-gift-toggle]');
    const giftFields = checkout.querySelector('[data-gift-fields]');

    giftToggle?.addEventListener('change', () => {
        giftFields.hidden = !giftToggle.checked;
    });

    /* Terms gate the secure checkout button */
    termsCheckbox?.addEventListener('change', () => {
        placeOrderButton.disabled = !termsCheckbox.checked;
    });

    /* Demo submit — show a brief processing state instead of posting */
    form?.addEventListener('submit', (event) => {
        event.preventDefault();

        placeOrderButton.disabled = true;
        placeOrderLabel.textContent = 'Processing payment\u2026';

        setTimeout(() => {
            placeOrderLabel.textContent = 'Order placed \u2713';
        }, 1200);
    });

    recalculate();
};
