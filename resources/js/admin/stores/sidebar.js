const STORAGE_KEY = 'admin-sidebar-collapsed';

let resizeTimer = null;

const getViewportMode = () => {
    if (window.innerWidth < 768) {
        return 'mobile';
    }

    if (window.innerWidth < 1024) {
        return 'tablet';
    }

    return 'desktop';
};

export const initAdminSidebar = () => {
    const shell = document.querySelector('[data-admin-shell]');
    const sidebar = document.querySelector('[data-admin-sidebar]');
    const backdrop = document.querySelector('[data-admin-sidebar-backdrop]');
    const collapseToggle = document.querySelector('[data-sidebar-collapse]');
    const mobileToggle = document.querySelector('[data-sidebar-mobile-toggle]');

    if (!shell || !sidebar) {
        return;
    }

    const collapsed = localStorage.getItem(STORAGE_KEY) === 'true';

    const setCollapsed = (value) => {
        shell.dataset.sidebarCollapsed = String(value);
        localStorage.setItem(STORAGE_KEY, String(value));
        collapseToggle?.setAttribute('aria-expanded', String(!value));
    };

    const setMobileOpen = (open) => {
        shell.dataset.sidebarMobileOpen = String(open);
        sidebar.setAttribute('aria-hidden', String(!open));
        mobileToggle?.setAttribute('aria-expanded', String(open));

        if (open) {
            backdrop?.classList.add('is-visible');
            backdrop?.removeAttribute('hidden');
            document.body.style.overflow = 'hidden';
            requestAnimationFrame(() => {
                sidebar.querySelector('a, button')?.focus();
            });
        } else {
            backdrop?.classList.remove('is-visible');
            document.body.style.overflow = '';
            window.setTimeout(() => {
                if (!backdrop?.classList.contains('is-visible')) {
                    backdrop?.setAttribute('hidden', '');
                }
            }, 250);
        }
    };

    const syncLayout = () => {
        const mode = getViewportMode();
        shell.dataset.viewport = mode;

        if (mode === 'mobile') {
            setCollapsed(false);
            setMobileOpen(false);
        } else if (mode === 'tablet') {
            setCollapsed(true);
            setMobileOpen(false);
        } else {
            setCollapsed(collapsed);
            setMobileOpen(false);
        }
    };

    syncLayout();

    window.addEventListener('resize', () => {
        if (resizeTimer) {
            cancelAnimationFrame(resizeTimer);
        }

        resizeTimer = requestAnimationFrame(syncLayout);
    });

    collapseToggle?.addEventListener('click', () => {
        setCollapsed(shell.dataset.sidebarCollapsed !== 'true');
    });

    mobileToggle?.addEventListener('click', () => {
        setMobileOpen(shell.dataset.sidebarMobileOpen !== 'true');
    });

    backdrop?.addEventListener('click', () => setMobileOpen(false));

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && shell.dataset.sidebarMobileOpen === 'true') {
            setMobileOpen(false);
            mobileToggle?.focus();
        }
    });

    sidebar.querySelectorAll('[data-nav-group-toggle]').forEach((toggle) => {
        const group = toggle.closest('[data-nav-group]');
        const panel = group?.querySelector('[data-nav-group-panel]');

        if (!group || !panel) {
            return;
        }

        const initPanel = () => {
            if (toggle.getAttribute('aria-expanded') === 'true') {
                panel.style.maxHeight = panel.scrollHeight + 'px';
                panel.classList.remove('is-collapsed');
            } else {
                panel.style.maxHeight = '0px';
                panel.classList.add('is-collapsed');
            }

            panel.removeAttribute('hidden');
        };

        initPanel();

        toggle.addEventListener('click', () => {
            const expanded = toggle.getAttribute('aria-expanded') === 'true';
            toggle.setAttribute('aria-expanded', String(!expanded));

            if (expanded) {
                panel.style.maxHeight = panel.scrollHeight + 'px';
                requestAnimationFrame(() => {
                    panel.style.maxHeight = '0px';
                    panel.classList.add('is-collapsed');
                });
            } else {
                panel.classList.remove('is-collapsed');
                panel.style.maxHeight = panel.scrollHeight + 'px';
                panel.addEventListener(
                    'transitionend',
                    () => {
                        if (toggle.getAttribute('aria-expanded') === 'true') {
                            panel.style.maxHeight = 'none';
                        }
                    },
                    { once: true },
                );
            }
        });
    });
};
