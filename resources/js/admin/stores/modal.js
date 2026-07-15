let modalHost = null;
let lastFocus = null;

const closeModal = () => {
    if (!modalHost) {
        return;
    }

    modalHost.classList.remove('is-open');
    document.body.style.overflow = '';

    window.setTimeout(() => {
        modalHost.hidden = true;
        modalHost.innerHTML = '';

        if (lastFocus instanceof HTMLElement) {
            lastFocus.focus();
        }
    }, 260);
};

const trapFocus = (container, event) => {
    if (event.key !== 'Tab') {
        return;
    }

    const focusable = container.querySelectorAll(
        'a[href], button:not([disabled]), textarea, input, select, [tabindex]:not([tabindex="-1"])',
    );

    if (focusable.length === 0) {
        return;
    }

    const first = focusable[0];
    const last = focusable[focusable.length - 1];

    if (event.shiftKey && document.activeElement === first) {
        event.preventDefault();
        last.focus();
    } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault();
        first.focus();
    }
};

export const adminModal = {
    open({ title, body, confirmLabel = 'Confirm', cancelLabel = 'Cancel', onConfirm = null, variant = 'default' }) {
        if (!modalHost) {
            return;
        }

        lastFocus = document.activeElement;
        document.body.style.overflow = 'hidden';

        const confirmClass =
            variant === 'danger'
                ? 'bg-admin-danger text-white hover:bg-red-700'
                : 'bg-admin-accent text-white hover:bg-admin-accent-hover dark:text-admin-bg';

        modalHost.innerHTML = `
            <div class="fixed inset-0 z-[80] flex items-end justify-center p-4 sm:items-center" role="dialog" aria-modal="true" aria-labelledby="admin-modal-title" data-admin-modal>
                <div class="admin-modal-backdrop absolute inset-0 bg-black/40" data-modal-backdrop></div>
                <div class="admin-modal-panel relative w-full max-w-md rounded-[var(--radius-admin-lg)] border admin-border admin-surface p-6 shadow-xl">
                    <h2 id="admin-modal-title" class="text-lg font-semibold admin-text">${title}</h2>
                    <div class="mt-3 text-sm admin-text-secondary">${body}</div>
                    <div class="mt-6 flex justify-end gap-2">
                        <button type="button" data-modal-cancel class="rounded-[var(--radius-admin)] border admin-border px-4 py-2 text-sm font-medium admin-text-secondary transition-colors duration-150 admin-focus-ring hover:admin-text hover:bg-admin-accent-muted/40">${cancelLabel}</button>
                        <button type="button" data-modal-confirm class="rounded-[var(--radius-admin)] px-4 py-2 text-sm font-medium transition-colors duration-150 admin-focus-ring ${confirmClass}">${confirmLabel}</button>
                    </div>
                </div>
            </div>
        `;

        modalHost.hidden = false;

        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                modalHost.classList.add('is-open');
            });
        });

        const dialog = modalHost.querySelector('[data-admin-modal]');
        dialog?.querySelector('[data-modal-cancel]')?.addEventListener('click', closeModal);
        dialog?.querySelector('[data-modal-backdrop]')?.addEventListener('click', closeModal);
        dialog?.querySelector('[data-modal-confirm]')?.addEventListener('click', () => {
            onConfirm?.();
            closeModal();
        });

        dialog?.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeModal();
            } else {
                trapFocus(dialog, event);
            }
        });

        window.setTimeout(() => {
            dialog?.querySelector('[data-modal-confirm]')?.focus();
        }, 100);
    },

    close: closeModal,
};

export const initAdminModal = () => {
    modalHost = document.querySelector('[data-modal-host]');
    window.adminModal = adminModal;
};
