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

    avatarInput?.addEventListener('change', () => {
        const file = avatarInput.files?.[0];

        if (!file) {
            return;
        }

        if (objectUrl) {
            URL.revokeObjectURL(objectUrl);
        }

        objectUrl = URL.createObjectURL(file);
        avatarPreview.innerHTML = `<img src="${objectUrl}" alt="Avatar preview" class="size-full object-cover">`;
        avatarRemove.hidden = false;
    });

    avatarRemove?.addEventListener('click', () => {
        avatarInput.value = '';

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
        avatarRemove?.click();
        saveStatus.textContent = 'Changes discarded.';
        saveStatus.classList.remove('text-green-700');
    });

    form?.addEventListener('submit', (event) => {
        event.preventDefault();

        saveButton.disabled = true;
        saveLabel.textContent = 'Saving\u2026';

        setTimeout(() => {
            saveLabel.textContent = 'Saved \u2713';
            saveStatus.textContent = 'Your profile has been updated successfully.';
            saveStatus.classList.add('text-green-700');

            setTimeout(() => {
                saveLabel.textContent = 'Save changes';
                saveButton.disabled = false;
            }, 1800);
        }, 900);
    });
};
