export const initLoginPage = () => {
    const page = document.querySelector('[data-login]');

    if (!page) {
        return;
    }

    const form = page.querySelector('[data-login-form]');
    const submitButton = page.querySelector('[data-login-submit]');
    const submitLabel = page.querySelector('[data-login-label]');
    const statusEl = page.querySelector('[data-login-status]');

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
            button.setAttribute('aria-label', visible ? 'Show password' : 'Hide password');
        });
    });

    form?.addEventListener('submit', async (event) => {
        event.preventDefault();

        const email = form.querySelector('#email')?.value.trim();

        if (!email) {
            statusEl.textContent = 'Please enter your email address.';
            statusEl.classList.remove('text-green-700');
            return;
        }

        submitButton.disabled = true;
        submitLabel.textContent = 'Signing in\u2026';
        statusEl.textContent = '';
        statusEl.classList.remove('text-green-700');

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

            const payload = await response.json();

            if (!response.ok) {
                throw new Error(payload.message ?? 'Unable to sign in.');
            }

            submitLabel.textContent = 'Signed in \u2713';
            statusEl.textContent = 'Welcome back. Redirecting to your account\u2026';
            statusEl.classList.add('text-green-700');

            setTimeout(() => {
                window.location.href = '/account';
            }, 1200);
        } catch (error) {
            submitLabel.textContent = 'Sign in';
            submitButton.disabled = false;
            statusEl.textContent = error.message;
            statusEl.classList.remove('text-green-700');
        }
    });

    page.querySelectorAll('[data-social-login]').forEach((button) => {
        button.addEventListener('click', () => {
            const provider = button.dataset.socialLogin;
            statusEl.textContent = `${provider === 'google' ? 'Google' : 'Apple'} sign-in is not configured in this demo.`;
            statusEl.classList.remove('text-green-700');
        });
    });
};
