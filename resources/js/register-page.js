export const initRegisterPage = () => {
    const page = document.querySelector('[data-register]');

    if (!page) {
        return;
    }

    const form = page.querySelector('[data-register-form]');
    const submitButton = page.querySelector('[data-register-submit]');
    const submitLabel = page.querySelector('[data-register-label]');
    const statusEl = page.querySelector('[data-register-status]');
    const mismatchEl = page.querySelector('[data-password-mismatch]');
    const termsCheckbox = page.querySelector('[data-terms-checkbox]');

    page.querySelectorAll('[data-toggle-password]').forEach((button) => {
        const input = page.querySelector(`#${button.dataset.togglePassword}`);

        if (!input) {
            return;
        }

        button.addEventListener('click', () => {
            const visible = input.type === 'text';
            input.type = visible ? 'password' : 'text';
            button.querySelector('[data-eye-open]').hidden = !visible;
            button.querySelector('[data-eye-closed]').hidden = visible;
            button.setAttribute('aria-label', visible ? `Show ${input.labels?.[0]?.textContent?.toLowerCase() ?? 'password'}` : `Hide ${input.labels?.[0]?.textContent?.toLowerCase() ?? 'password'}`);
        });
    });

    const validatePasswords = () => {
        const password = form.querySelector('#password')?.value ?? '';
        const confirm = form.querySelector('#password-confirm')?.value ?? '';
        const mismatch = password !== confirm && confirm.length > 0;

        mismatchEl.hidden = !mismatch;
        form.querySelector('#password-confirm')?.setAttribute('aria-invalid', String(mismatch));

        return !mismatch;
    };

    form.querySelector('#password-confirm')?.addEventListener('input', validatePasswords);
    form.querySelector('#password')?.addEventListener('input', validatePasswords);

    form?.addEventListener('submit', (event) => {
        event.preventDefault();

        const name = form.querySelector('#name')?.value.trim();
        const email = form.querySelector('#email')?.value.trim();
        const phone = form.querySelector('#phone')?.value.trim();
        const password = form.querySelector('#password')?.value;
        const termsAccepted = termsCheckbox?.checked;

        statusEl.classList.remove('text-green-700');

        if (!name || !email || !phone || !password) {
            statusEl.textContent = 'Please complete all required fields.';
            return;
        }

        if (password.length < 8) {
            statusEl.textContent = 'Password must be at least 8 characters.';
            return;
        }

        if (!validatePasswords()) {
            statusEl.textContent = 'Please make sure your passwords match.';
            return;
        }

        if (!termsAccepted) {
            statusEl.textContent = 'Please accept the Terms of Service and Privacy Policy.';
            termsCheckbox?.focus();
            return;
        }

        submitButton.disabled = true;
        submitLabel.textContent = 'Creating account\u2026';
        statusEl.textContent = '';

        setTimeout(() => {
            submitLabel.textContent = 'Account created \u2713';
            statusEl.textContent = 'Welcome to Valor. Redirecting to your account\u2026';
            statusEl.classList.add('text-green-700');

            setTimeout(() => {
                window.location.href = '/account';
            }, 1200);
        }, 900);
    });
};
