const STORAGE_KEY = 'admin-theme';

const applyTheme = (mode) => {
    const root = document.documentElement;

    if (mode === 'dark') {
        root.classList.add('dark');
    } else if (mode === 'light') {
        root.classList.remove('dark');
    } else {
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        root.classList.toggle('dark', prefersDark);
    }

    document.querySelectorAll('[data-theme-label]').forEach((el) => {
        el.textContent = mode === 'system' ? 'System' : mode === 'dark' ? 'Dark' : 'Light';
    });
};

export const initAdminTheme = () => {
    const stored = localStorage.getItem(STORAGE_KEY);
    const initial = stored ?? 'system';
    applyTheme(initial);

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const current = localStorage.getItem(STORAGE_KEY) ?? 'system';
            const next = current === 'light' ? 'dark' : current === 'dark' ? 'system' : 'light';
            localStorage.setItem(STORAGE_KEY, next);
            applyTheme(next);
        });
    });

    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
        if ((localStorage.getItem(STORAGE_KEY) ?? 'system') === 'system') {
            applyTheme('system');
        }
    });
};

export const getAdminTheme = () => localStorage.getItem(STORAGE_KEY) ?? 'system';
