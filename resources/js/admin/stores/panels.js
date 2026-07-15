const closeAllPanels = () => {
    document.querySelectorAll('[data-admin-panel]').forEach((panel) => {
        panel.classList.remove('is-open');
        panel.setAttribute('aria-hidden', 'true');

        panel.addEventListener(
            'transitionend',
            () => {
                if (!panel.classList.contains('is-open')) {
                    panel.hidden = true;
                }
            },
            { once: true },
        );
    });

    document.querySelectorAll('[data-panel-trigger]').forEach((trigger) => {
        trigger.setAttribute('aria-expanded', 'false');
    });
};

const openPanel = (panel, trigger) => {
    panel.hidden = false;

    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            panel.classList.add('is-open');
            panel.setAttribute('aria-hidden', 'false');
            trigger.setAttribute('aria-expanded', 'true');
        });
    });
};

export const initAdminPanels = () => {
    document.querySelectorAll('[data-panel-trigger]').forEach((trigger) => {
        const panelId = trigger.getAttribute('aria-controls');
        const panel = panelId ? document.getElementById(panelId) : null;

        if (!panel) {
            return;
        }

        trigger.addEventListener('click', (event) => {
            event.stopPropagation();
            const isOpen = panel.classList.contains('is-open');
            closeAllPanels();

            if (!isOpen) {
                openPanel(panel, trigger);
            }
        });
    });

    document.addEventListener('click', closeAllPanels);

    document.querySelectorAll('[data-admin-panel]').forEach((panel) => {
        panel.addEventListener('click', (event) => event.stopPropagation());
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeAllPanels();
        }
    });
};
