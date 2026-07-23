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

export const initProfilePage = () => {
    const page = document.querySelector('[data-profile]');

    if (!page) {
        return;
    }

    const form = page.querySelector('[data-profile-form]');
    const avatarInput = page.querySelector('[data-avatar-input]');
    const avatarPreview = page.querySelector('[data-avatar-preview]');
    const avatarRemove = page.querySelector('[data-avatar-remove]');
    const saveStatus = page.querySelector('[data-save-status]');
    const saveLabel = page.querySelector('[data-save-label]');
    const saveButton = page.querySelector('[data-profile-save]');
    const resetButton = page.querySelector('[data-profile-reset]');

    const defaultAvatarHtml = avatarPreview.innerHTML;
    let objectUrl = null;
    let removeAvatar = false;

    avatarInput?.addEventListener('change', () => {
        const file = avatarInput.files?.[0];

        if (!file) {
            return;
        }

        removeAvatar = false;

        if (objectUrl) {
            URL.revokeObjectURL(objectUrl);
        }

        objectUrl = URL.createObjectURL(file);
        avatarPreview.innerHTML = `<img src="${objectUrl}" alt="Avatar preview" class="size-full object-cover">`;
        avatarRemove.hidden = false;
    });

    avatarRemove?.addEventListener('click', () => {
        avatarInput.value = '';
        removeAvatar = true;

        if (objectUrl) {
            URL.revokeObjectURL(objectUrl);
            objectUrl = null;
        }

        avatarPreview.innerHTML = defaultAvatarHtml;
        avatarRemove.hidden = true;
    });

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

    resetButton?.addEventListener('click', () => {
        form.reset();
        removeAvatar = false;
        avatarRemove?.click();
        saveStatus.textContent = 'Changes discarded.';
        saveStatus.classList.remove('text-green-700');
    });

    form?.addEventListener('submit', async (event) => {
        event.preventDefault();

        saveButton.disabled = true;
        saveLabel.textContent = 'Saving\u2026';

        const formData = new FormData();
        formData.append('_method', 'PUT');
        formData.append('first_name', form.querySelector('#first-name')?.value ?? '');
        formData.append('last_name', form.querySelector('#last-name')?.value ?? '');
        formData.append('phone', form.querySelector('#phone')?.value ?? '');

        const currentPassword = form.querySelector('#current-password')?.value ?? '';
        const newPassword = form.querySelector('#new-password')?.value ?? '';
        const confirmPassword = form.querySelector('#confirm-password')?.value ?? '';

        if (newPassword) {
            formData.append('current_password', currentPassword);
            formData.append('password', newPassword);
            formData.append('password_confirmation', confirmPassword);
        }

        if (avatarInput?.files?.[0]) {
            formData.append('avatar', avatarInput.files[0]);
        }

        if (removeAvatar) {
            formData.append('remove_avatar', '1');
        }

        try {
            const payload = await parseJsonResponse(await fetch(form.action, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData,
            }));

            saveLabel.textContent = 'Saved \u2713';
            saveStatus.textContent = payload.message ?? 'Your profile has been updated successfully.';
            saveStatus.classList.add('text-green-700');

            if (payload.customer?.avatar_url) {
                avatarPreview.innerHTML = `<img src="${payload.customer.avatar_url}" alt="Avatar preview" class="size-full object-cover">`;
            }

            const currentPassword = form.querySelector('#current-password');
            const newPassword = form.querySelector('#new-password');
            const confirmPassword = form.querySelector('#confirm-password');

            if (currentPassword) {
                currentPassword.value = '';
            }

            if (newPassword) {
                newPassword.value = '';
            }

            if (confirmPassword) {
                confirmPassword.value = '';
            }
        } catch (error) {
            saveStatus.textContent = error.message;
            saveStatus.classList.remove('text-green-700');
        } finally {
            setTimeout(() => {
                saveLabel.textContent = 'Save changes';
                saveButton.disabled = false;
            }, 1200);
        }
    });
};
