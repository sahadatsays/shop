const toasts = [];
let toastHost = null;

const renderToasts = () => {
    if (!toastHost) {
        return;
    }

    const existingIds = new Set(toasts.map((t) => t.id));

    toastHost.querySelectorAll('[data-toast-id]').forEach((el) => {
        if (!existingIds.has(el.getAttribute('data-toast-id'))) {
            el.classList.remove('admin-toast-enter');
            el.classList.add('admin-toast-exit');
            el.addEventListener('animationend', () => el.remove(), { once: true });
        }
    });

    toasts.forEach((toast) => {
        if (toastHost.querySelector(`[data-toast-id="${toast.id}"]`)) {
            return;
        }

        const el = document.createElement('div');
        el.setAttribute('role', 'status');
        el.setAttribute('aria-live', 'polite');
        el.setAttribute('data-toast-id', toast.id);
        el.className =
            'admin-toast-enter pointer-events-auto flex w-full max-w-sm items-start gap-3 rounded-[var(--radius-admin-lg)] border admin-border admin-surface px-4 py-3 shadow-lg';

        el.innerHTML = `
            <span class="mt-0.5 size-2 shrink-0 rounded-full ${toastToneDot(toast.type)}"></span>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-medium admin-text">${toast.title}</p>
                ${toast.message ? `<p class="mt-0.5 text-sm admin-muted">${toast.message}</p>` : ''}
            </div>
            <button type="button" data-toast-dismiss="${toast.id}"
                    class="admin-muted admin-focus-ring shrink-0 rounded p-1 transition-colors duration-150 hover:admin-text"
                    aria-label="Dismiss notification">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        `;

        el.querySelector('[data-toast-dismiss]')?.addEventListener('click', () => {
            dismissToast(toast.id);
        });

        toastHost.appendChild(el);
    });
};

const toastToneDot = (type) => {
    switch (type) {
        case 'success':
            return 'bg-admin-success';
        case 'warning':
            return 'bg-admin-warning';
        case 'danger':
            return 'bg-admin-danger';
        default:
            return 'bg-admin-info';
    }
};

const dismissToast = (id) => {
    const index = toasts.findIndex((toast) => toast.id === id);

    if (index >= 0) {
        toasts.splice(index, 1);
    }

    const el = toastHost?.querySelector(`[data-toast-id="${id}"]`);

    if (el) {
        el.classList.remove('admin-toast-enter');
        el.classList.add('admin-toast-exit');
        el.addEventListener('animationend', () => el.remove(), { once: true });
    }
};

export const adminToast = {
    push({ title, message = '', type = 'info', duration = 5000 }) {
        const id = crypto.randomUUID();
        toasts.push({ id, title, message, type });
        renderToasts();

        if (duration > 0) {
            window.setTimeout(() => dismissToast(id), duration);
        }
    },
};

export const initAdminToast = () => {
    toastHost = document.querySelector('[data-toast-host]');
    window.adminToast = adminToast;
};
