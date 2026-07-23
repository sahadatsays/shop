const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';

const parseJsonResponse = async (response) => {
    const payload = await response.json().catch(() => ({}));

    if (!response.ok) {
        const message = payload.message
            ?? Object.values(payload.errors ?? {})?.flat()?.[0]
            ?? 'Something went wrong. Please try again.';

        throw new Error(message);
    }

    return payload;
};

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
        const password = form.querySelector('#password')?.value ?? '';
        const remember = form.querySelector('[name="remember"]')?.checked ?? false;

        if (!email || !password) {
            statusEl.textContent = 'Please enter your email address and password.';
            statusEl.classList.remove('text-green-700');
            return;
        }

        submitButton.disabled = true;
        submitLabel.textContent = 'Signing in\u2026';
        statusEl.textContent = '';
        statusEl.classList.remove('text-green-700');

        try {
            await parseJsonResponse(await fetch(form.action, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ email, password, remember }),
            }));

            submitLabel.textContent = 'Signed in \u2713';
            statusEl.textContent = 'Welcome back. Redirecting to your account\u2026';
            statusEl.classList.add('text-green-700');

            setTimeout(() => {
                window.location.href = '/account';
            }, 900);
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

            if (provider === 'google') {
                window.location.href = '/auth/google';
                return;
            }

            statusEl.textContent = `${provider === 'apple' ? 'Apple' : 'Social'} sign-in is not configured yet.`;
            statusEl.classList.remove('text-green-700');
        });
    });
};
