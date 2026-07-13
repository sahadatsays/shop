const initMobileNav = () => {
    const toggle = document.querySelector('[data-nav-toggle]');
    const nav = document.querySelector('[data-mobile-nav]');

    if (!toggle || !nav) {
        return;
    }

    const iconOpen = toggle.querySelector('[data-icon-open]');
    const iconClose = toggle.querySelector('[data-icon-close]');

    const setOpen = (open) => {
        nav.hidden = !open;
        toggle.setAttribute('aria-expanded', String(open));
        toggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
        iconOpen?.classList.toggle('hidden', open);
        iconClose?.classList.toggle('hidden', !open);
    };

    toggle.addEventListener('click', () => setOpen(nav.hidden));

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !nav.hidden) {
            setOpen(false);
            toggle.focus();
        }
    });
};

const initHeaderElevation = () => {
    const header = document.querySelector('[data-site-header]');

    if (!header) {
        return;
    }

    const update = () => {
        header.classList.toggle('shadow-glass', window.scrollY > 8);
    };

    update();
    window.addEventListener('scroll', update, { passive: true });
};

const initRevealOnScroll = () => {
    const targets = document.querySelectorAll('[data-reveal]');

    if (targets.length === 0 || !('IntersectionObserver' in window)) {
        targets.forEach((el) => el.classList.add('animate-fade-in-up'));
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in-up');
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.15 },
    );

    targets.forEach((el) => {
        el.classList.add('opacity-0');
        observer.observe(el);
    });
};

initMobileNav();
initHeaderElevation();
initRevealOnScroll();
