const ICONS = {
    home: '<svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 11 12 3l9 8M6 10v10h12V10"/></svg>',
    office: '<svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/><path d="M6 12h12M10 6h4M10 10h4M10 14h4M10 18h4"/></svg>',
    other: '<svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s-6-5.5-6-10a6 6 0 0 1 12 0c0 4.5-6 10-6 10Zm0-7.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/></svg>',
};

const slugify = (value) => value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');

const iconForLabel = (label) => {
    const key = label.toLowerCase();

    if (key === 'home') {
        return 'home';
    }

    if (key === 'office') {
        return 'office';
    }

    return 'other';
};

const formatAddressHtml = ({ line1, line2, city, state, postal, country }) => {
    const parts = [line1];

    if (line2) {
        parts.push(line2);
    }

    parts.push(`${city}, ${state} ${postal}`, country);

    return parts.join('<br>');
};

export const initAddressesPage = () => {
    const page = document.querySelector('[data-addresses]');

    if (!page) {
        return;
    }

    const grid = page.querySelector('[data-address-grid]');
    const template = page.querySelector('[data-address-template]');
    const dialog = page.querySelector('[data-address-dialog]');
    const deleteDialog = page.querySelector('[data-address-delete-dialog]');
    const form = page.querySelector('[data-address-form]');
    const dialogTitle = page.querySelector('[data-address-dialog-title]');
    const saveLabel = page.querySelector('[data-address-save-label]');
    const statusEl = page.querySelector('[data-address-status]');
    const countEl = page.querySelector('[data-address-count]');
    const emptyState = page.querySelector('[data-addresses-empty]');
    const deleteLabelEl = page.querySelector('[data-delete-label]');

    let editingCard = null;
    let deletingCard = null;
    let nextId = 3;

    const getCards = () => [...page.querySelectorAll('[data-address-card]')];

    const updateCount = () => {
        const count = getCards().length;
        countEl.textContent = `${count} saved ${count === 1 ? 'address' : 'addresses'}`;
        emptyState.hidden = count > 0;
        grid.hidden = count === 0;
    };

    const setStatus = (message, success = false) => {
        statusEl.textContent = message;
        statusEl.classList.toggle('text-green-700', success);
    };

    const openDialog = () => {
        dialog.showModal();
        form.querySelector('#address-label')?.focus();
    };

    const closeDialog = () => {
        dialog.close();
        form.reset();
        editingCard = null;
        saveLabel.textContent = 'Save address';
        dialogTitle.textContent = 'Add address';
    };

    const readForm = () => ({
        label: form.querySelector('#address-label').value,
        name: form.querySelector('#full-name').value.trim(),
        line1: form.querySelector('#line1').value.trim(),
        line2: form.querySelector('#line2').value.trim(),
        city: form.querySelector('#city').value.trim(),
        state: form.querySelector('#state').value.trim(),
        postal: form.querySelector('#postal').value.trim(),
        country: form.querySelector('#address-country').value,
        phone: form.querySelector('#phone').value.trim(),
        setDefault: form.querySelector('[data-address-set-default-input]').checked,
    });

    const fillForm = (card) => {
        form.querySelector('#address-label').value = card.dataset.label;
        form.querySelector('#full-name').value = card.dataset.name;
        form.querySelector('#line1').value = card.dataset.line1;
        form.querySelector('#line2').value = card.dataset.line2;
        form.querySelector('#city').value = card.dataset.city;
        form.querySelector('#state').value = card.dataset.state;
        form.querySelector('#postal').value = card.dataset.postal;
        form.querySelector('#address-country').value = card.dataset.country;
        form.querySelector('#phone').value = card.dataset.phone;
        form.querySelector('[data-address-set-default-input]').checked = card.dataset.default === 'true';
    };

    const applyDataToCard = (card, data) => {
        card.dataset.label = data.label;
        card.dataset.name = data.name;
        card.dataset.line1 = data.line1;
        card.dataset.line2 = data.line2;
        card.dataset.city = data.city;
        card.dataset.state = data.state;
        card.dataset.postal = data.postal;
        card.dataset.country = data.country;
        card.dataset.phone = data.phone;

        card.querySelector('[data-address-label-el], h2')?.replaceChildren(document.createTextNode(data.label));
        card.querySelector('[data-address-name-el]')?.replaceChildren(document.createTextNode(data.name));
        card.querySelector('[data-address-text]').innerHTML = formatAddressHtml(data);
        card.querySelector('[data-address-phone-el]')?.replaceChildren(document.createTextNode(data.phone || ''));

        const iconEl = card.querySelector('[data-address-icon]') ?? card.querySelector('.flex.size-10');
        if (iconEl) {
            iconEl.innerHTML = ICONS[iconForLabel(data.label)];
        }

        card.querySelector('[data-address-edit]')?.setAttribute('aria-label', `Edit ${data.label} address`);
        card.querySelector('[data-address-delete]')?.setAttribute('aria-label', `Delete ${data.label} address`);
    };

    const clearDefaultFromAll = () => {
        getCards().forEach((card) => {
            card.dataset.default = 'false';
            card.classList.remove('ring-2', 'ring-bronze-400/60', 'ring-offset-2', 'ring-offset-canvas');
            card.querySelector('[data-default-badge]')?.remove();

            const setDefaultBtn = card.querySelector('[data-address-set-default]');
            if (!setDefaultBtn) {
                const actions = card.querySelector('.mt-auto');
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.dataset.addressSetDefault = '';
                btn.className = 'rounded-xl px-3 py-2 text-xs font-semibold text-olive-700 transition-colors duration-200 hover:bg-olive-50';
                btn.textContent = 'Set as default';
                actions?.prepend(btn);
            }
        });
    };

    const markAsDefault = (card) => {
        clearDefaultFromAll();
        card.dataset.default = 'true';
        card.classList.add('ring-2', 'ring-bronze-400/60', 'ring-offset-2', 'ring-offset-canvas');
        card.querySelector('[data-address-set-default]')?.remove();

        const header = card.querySelector('.flex.items-start.justify-between');
        if (header && !header.querySelector('[data-default-badge]')) {
            const badge = document.createElement('span');
            badge.dataset.defaultBadge = '';
            badge.className = 'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold bg-bronze-100 text-bronze-800';
            badge.textContent = 'Default';
            header.appendChild(badge);
        }
    };

    const createCard = (data) => {
        const fragment = template.content.cloneNode(true);
        const card = fragment.querySelector('[data-address-card]');
        const id = slugify(data.label) || `address-${nextId++}`;

        card.dataset.addressId = id;
        applyDataToCard(card, data);

        grid.insertBefore(fragment, grid.querySelector('[data-address-add-card]'));

        if (data.setDefault) {
            markAsDefault(card);
        }

        return card;
    };

    page.querySelectorAll('[data-address-add], [data-address-add-card]').forEach((trigger) => {
        trigger.addEventListener('click', () => {
            editingCard = null;
            form.reset();
            dialogTitle.textContent = 'Add address';
            saveLabel.textContent = 'Save address';
            openDialog();
        });
    });

    page.querySelectorAll('[data-address-dialog-close]').forEach((btn) => {
        btn.addEventListener('click', closeDialog);
    });

    dialog?.addEventListener('click', (event) => {
        if (event.target === dialog) {
            closeDialog();
        }
    });

    form?.addEventListener('submit', (event) => {
        event.preventDefault();

        const data = readForm();

        if (!data.name || !data.line1 || !data.city || !data.state || !data.postal) {
            setStatus('Please fill in all required fields.');
            return;
        }

        if (editingCard) {
            applyDataToCard(editingCard, data);

            if (data.setDefault) {
                markAsDefault(editingCard);
            }

            setStatus(`${data.label} address updated.`, true);
        } else {
            const card = createCard(data);
            setStatus(`${data.label} address added.`, true);
        }

        updateCount();
        closeDialog();
    });

    grid?.addEventListener('click', (event) => {
        const editBtn = event.target.closest('[data-address-edit]');
        const deleteBtn = event.target.closest('[data-address-delete]');
        const defaultBtn = event.target.closest('[data-address-set-default]');

        if (editBtn) {
            editingCard = editBtn.closest('[data-address-card]');
            fillForm(editingCard);
            dialogTitle.textContent = `Edit ${editingCard.dataset.label}`;
            saveLabel.textContent = 'Update address';
            openDialog();
        }

        if (deleteBtn) {
            deletingCard = deleteBtn.closest('[data-address-card]');
            deleteLabelEl.textContent = deletingCard.dataset.label;
            deleteDialog.showModal();
        }

        if (defaultBtn) {
            const card = defaultBtn.closest('[data-address-card]');
            markAsDefault(card);
            setStatus(`${card.dataset.label} is now your default address.`, true);
        }
    });

    page.querySelector('[data-delete-cancel]')?.addEventListener('click', () => {
        deleteDialog.close();
        deletingCard = null;
    });

    page.querySelector('[data-delete-confirm]')?.addEventListener('click', () => {
        if (!deletingCard) {
            return;
        }

        const label = deletingCard.dataset.label;
        const wasDefault = deletingCard.dataset.default === 'true';

        deletingCard.remove();
        updateCount();

        if (wasDefault) {
            const first = getCards()[0];
            if (first) {
                markAsDefault(first);
            }
        }

        setStatus(`${label} address removed.`, true);
        deleteDialog.close();
        deletingCard = null;
    });

    deleteDialog?.addEventListener('click', (event) => {
        if (event.target === deleteDialog) {
            deleteDialog.close();
            deletingCard = null;
        }
    });
};
