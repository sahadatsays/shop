export const initContactPage = () => {
    const page = document.querySelector('[data-contact]');

    if (!page) {
        return;
    }

    const form = page.querySelector('[data-contact-form]');
    const submitButton = page.querySelector('[data-contact-submit]');
    const submitLabel = page.querySelector('[data-contact-label]');
    const statusEl = page.querySelector('[data-contact-status]');

    form?.addEventListener('submit', (event) => {
        event.preventDefault();

        const firstName = form.querySelector('#first-name')?.value.trim();
        const lastName = form.querySelector('#last-name')?.value.trim();
        const email = form.querySelector('#email')?.value.trim();
        const topic = form.querySelector('#contact-topic')?.value;
        const message = form.querySelector('#contact-message')?.value.trim();

        statusEl.classList.remove('text-green-700');

        if (!firstName || !lastName || !email || !topic || !message) {
            statusEl.textContent = 'Please complete all required fields.';
            return;
        }

        submitButton.disabled = true;
        submitLabel.textContent = 'Sending\u2026';
        statusEl.textContent = '';

        setTimeout(() => {
            submitLabel.textContent = 'Message sent \u2713';
            statusEl.textContent = `Thanks, ${firstName}. We\'ll reply to ${email} within one business day.`;
            statusEl.classList.add('text-green-700');
            form.reset();

            setTimeout(() => {
                submitLabel.textContent = 'Send message';
                submitButton.disabled = false;
            }, 2000);
        }, 900);
    });
};
