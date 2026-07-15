export const initAdminPageLoader = () => {
    const bar = document.querySelector('[data-page-loader]');

    if (!bar) {
        return;
    }

    let running = false;

    const start = () => {
        if (running) {
            return;
        }

        running = true;
        bar.hidden = false;
        bar.style.transition = 'none';
        bar.style.width = '0%';
        bar.style.opacity = '1';

        requestAnimationFrame(() => {
            bar.style.transition = 'width 8s cubic-bezier(0.1, 0.7, 0.1, 1)';
            bar.style.width = '85%';
        });
    };

    const finish = () => {
        if (!running) {
            bar.hidden = true;
            return;
        }

        running = false;
        bar.style.transition = 'width 0.2s ease, opacity 0.3s ease 0.15s';
        bar.style.width = '100%';
        bar.style.opacity = '0';

        window.setTimeout(() => {
            bar.hidden = true;
            bar.style.width = '0%';
            bar.style.opacity = '1';
        }, 500);
    };

    document.querySelectorAll('a[href]').forEach((link) => {
        const href = link.getAttribute('href');

        if (!href || href.startsWith('#') || href.startsWith('javascript') || link.target === '_blank' || link.hasAttribute('download')) {
            return;
        }

        if (link.closest('[data-admin-panel]') || link.closest('[data-command-palette]')) {
            return;
        }

        link.addEventListener('click', (event) => {
            if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
                return;
            }

            start();
        });
    });

    window.addEventListener('pageshow', finish);
    window.addEventListener('load', finish);
};
