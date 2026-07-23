export const initNewsletterForm = () => {
    const form = document.querySelector('[data-newsletter-form]');

    if (!form) {
        return;
    }

    const status = document.querySelector('[data-newsletter-status]');
    const submit = form.querySelector('[data-newsletter-submit]');
    const label = form.querySelector('[data-newsletter-label]');

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const email = form.querySelector('#newsletter-email')?.value.trim();

        if (!email) {
            return;
        }

        submit.disabled = true;
        label.textContent = 'Subscribing…';

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ email }),
            });

            const payload = await response.json().catch(() => ({}));

            if (!response.ok) {
                throw new Error(payload.message ?? 'Unable to subscribe right now.');
            }

            status.textContent = payload.message ?? 'Thanks for subscribing.';
            status.classList.add('text-white');
            form.reset();
            label.textContent = 'Subscribed ✓';
        } catch (error) {
            status.textContent = error.message;
            label.textContent = 'Subscribe';
        } finally {
            submit.disabled = false;
            setTimeout(() => {
                label.textContent = 'Subscribe';
            }, 1800);
        }
    });
};
