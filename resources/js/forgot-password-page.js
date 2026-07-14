export const initForgotPasswordPage = () => {
    const page = document.querySelector('[data-forgot-password]');

    if (!page) {
        return;
    }

    const requestPanel = page.querySelector('[data-forgot-request]');
    const successPanel = page.querySelector('[data-forgot-success]');
    const form = page.querySelector('[data-forgot-form]');
    const submitButton = page.querySelector('[data-forgot-submit]');
    const submitLabel = page.querySelector('[data-forgot-label]');
    const statusEl = page.querySelector('[data-forgot-status]');
    const emailDisplay = page.querySelector('[data-forgot-email-display]');
    const resendButton = page.querySelector('[data-forgot-resend]');
    const resendStatus = page.querySelector('[data-forgot-resend-status]');

    let lastEmail = '';

    const showSuccess = (email) => {
        lastEmail = email;
        emailDisplay.textContent = email;
        requestPanel.hidden = true;
        successPanel.hidden = false;
    };

    const sendReset = (email, onComplete) => {
        submitButton.disabled = true;
        submitLabel.textContent = 'Sending\u2026';
        statusEl.textContent = '';
        statusEl.classList.remove('text-green-700');

        setTimeout(() => {
            submitLabel.textContent = 'Send reset link';
            submitButton.disabled = false;
            onComplete();
        }, 900);
    };

    form?.addEventListener('submit', (event) => {
        event.preventDefault();

        const email = form.querySelector('#email')?.value.trim();

        if (!email) {
            statusEl.textContent = 'Please enter your email address.';
            statusEl.classList.remove('text-green-700');
            return;
        }

        sendReset(email, () => showSuccess(email));
    });

    resendButton?.addEventListener('click', () => {
        if (!lastEmail) {
            return;
        }

        resendButton.disabled = true;
        resendStatus.textContent = 'Sending\u2026';
        resendStatus.classList.remove('text-green-700');

        setTimeout(() => {
            resendStatus.textContent = 'Reset link sent again. Check your inbox.';
            resendStatus.classList.add('text-green-700');
            resendButton.disabled = false;
        }, 800);
    });
};
