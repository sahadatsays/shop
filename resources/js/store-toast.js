const toasts = [];
let toastHost = null;
let toastCounter = 0;

const createToastId = () => {
    if (typeof crypto?.randomUUID === 'function') {
        return crypto.randomUUID();
    }

    toastCounter += 1;

    return `store-toast-${Date.now()}-${toastCounter}`;
};

const toneClasses = (type) => {
    switch (type) {
        case 'success':
            return {
                border: 'border-olive-200',
                bg: 'bg-olive-50',
                dot: 'bg-olive-600',
                icon: 'text-olive-700',
            };
        case 'warning':
            return {
                border: 'border-bronze-200',
                bg: 'bg-bronze-50',
                dot: 'bg-bronze-500',
                icon: 'text-bronze-700',
            };
        case 'danger':
            return {
                border: 'border-red-200',
                bg: 'bg-red-50',
                dot: 'bg-red-500',
                icon: 'text-red-700',
            };
        default:
            return {
                border: 'border-navy-200',
                bg: 'bg-surface',
                dot: 'bg-navy-600',
                icon: 'text-navy-700',
            };
    }
};

const dismissToast = (id) => {
    const index = toasts.findIndex((toast) => toast.id === id);

    if (index >= 0) {
        toasts.splice(index, 1);
    }

    const el = toastHost?.querySelector(`[data-store-toast-id="${id}"]`);

    if (el) {
        el.classList.remove('store-toast-enter');
        el.classList.add('store-toast-exit');
        el.addEventListener('animationend', () => el.remove(), { once: true });
    }
};

const renderToasts = () => {
    if (!toastHost) {
        return;
    }

    const existingIds = new Set(toasts.map((toast) => toast.id));

    toastHost.querySelectorAll('[data-store-toast-id]').forEach((el) => {
        if (!existingIds.has(el.getAttribute('data-store-toast-id'))) {
            el.classList.remove('store-toast-enter');
            el.classList.add('store-toast-exit');
            el.addEventListener('animationend', () => el.remove(), { once: true });
        }
    });

    toasts.forEach((toast) => {
        if (toastHost.querySelector(`[data-store-toast-id="${toast.id}"]`)) {
            return;
        }

        const tone = toneClasses(toast.type);
        const el = document.createElement('div');
        el.setAttribute('role', 'status');
        el.setAttribute('aria-live', 'polite');
        el.setAttribute('data-store-toast-id', toast.id);
        el.className = `store-toast-enter pointer-events-auto flex w-full max-w-sm items-start gap-3 rounded-card border ${tone.border} ${tone.bg} px-4 py-3.5 shadow-card`;

        el.innerHTML = `
            <span class="mt-1 size-2 shrink-0 rounded-full ${tone.dot}" aria-hidden="true"></span>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold text-navy-900">${toast.title}</p>
                ${toast.message ? `<p class="mt-0.5 text-sm text-navy-600">${toast.message}</p>` : ''}
            </div>
            <button type="button" data-store-toast-dismiss="${toast.id}"
                    class="shrink-0 rounded-lg p-1 text-navy-400 transition-colors duration-200 hover:bg-navy-900/5 hover:text-navy-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-bronze-500"
                    aria-label="Dismiss notification">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        `;

        el.querySelector('[data-store-toast-dismiss]')?.addEventListener('click', () => {
            dismissToast(toast.id);
        });

        toastHost.appendChild(el);
    });
};

export const storeToast = {
    push({ title, message = '', type = 'info', duration = 4500 }) {
        const id = createToastId();
        toasts.push({ id, title, message, type });
        renderToasts();

        if (duration > 0) {
            window.setTimeout(() => dismissToast(id), duration);
        }

        return id;
    },

    success(title, message = '') {
        return this.push({ title, message, type: 'success' });
    },

    error(title, message = '') {
        return this.push({ title, message, type: 'danger', duration: 6000 });
    },

    warning(title, message = '') {
        return this.push({ title, message, type: 'warning' });
    },
};

export const initStoreToast = () => {
    toastHost = document.querySelector('[data-store-toast-host]');
};
