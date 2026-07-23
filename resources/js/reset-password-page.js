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

export const initResetPasswordPage = () => {
    const page = document.querySelector('[data-reset-password]');

    if (!page) {
        return;
    }

    const form = page.querySelector('[data-reset-form]');
    const submitButton = page.querySelector('[data-reset-submit]');
    const submitLabel = page.querySelector('[data-reset-label]');
    const statusEl = page.querySelector('[data-reset-status]');

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

        submitButton.disabled = true;
        submitLabel.textContent = 'Resetting\u2026';
        statusEl.textContent = '';
        statusEl.classList.remove('text-green-700');

        const formData = new FormData(form);

        try {
            await parseJsonResponse(await fetch(form.action, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData,
            }));

            submitLabel.textContent = 'Password reset \u2713';
            statusEl.textContent = 'Your password has been reset. Redirecting to sign in\u2026';
            statusEl.classList.add('text-green-700');

            setTimeout(() => {
                window.location.href = '/login';
            }, 1200);
        } catch (error) {
            submitLabel.textContent = 'Reset password';
            submitButton.disabled = false;
            statusEl.textContent = error.message;
        }
    });
};
