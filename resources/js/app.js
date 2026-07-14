import { initProductPage } from './product-page';
import { initShopPage } from './shop-page';
import { initSearchPage } from './search-page';
import { initCartPage } from './cart-page';
import { initCheckoutPage } from './checkout-page';
import { initWishlistPage } from './wishlist-page';
import { initComparePage } from './compare-page';
import { initOrdersPage } from './orders-page';
import { initTrackPage } from './track-page';
import { initProfilePage } from './profile-page';
import { initAddressesPage } from './addresses-page';

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

const initSearchPanel = () => {
    const toggle = document.querySelector('[data-search-toggle]');
    const panel = document.querySelector('[data-search-panel]');
    const input = document.querySelector('[data-search-input]');

    if (!toggle || !panel) {
        return;
    }

    const setOpen = (open) => {
        panel.hidden = !open;
        toggle.setAttribute('aria-expanded', String(open));

        if (open) {
            input?.focus();
        }
    };

    toggle.addEventListener('click', () => setOpen(panel.hidden));

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !panel.hidden) {
            setOpen(false);
            toggle.focus();
        }
    });
};

const initCarousels = () => {
    document.querySelectorAll('[data-carousel]').forEach((track) => {
        const section = track.closest('section') ?? document;
        const prev = section.querySelector('[data-carousel-prev]');
        const next = section.querySelector('[data-carousel-next]');

        if (!prev || !next) {
            return;
        }

        const step = () => {
            const card = track.querySelector(':scope > *');
            return card ? card.getBoundingClientRect().width + 24 : track.clientWidth;
        };

        const updateButtons = () => {
            prev.disabled = track.scrollLeft <= 4;
            next.disabled = track.scrollLeft >= track.scrollWidth - track.clientWidth - 4;
        };

        prev.addEventListener('click', () => track.scrollBy({ left: -step(), behavior: 'smooth' }));
        next.addEventListener('click', () => track.scrollBy({ left: step(), behavior: 'smooth' }));
        track.addEventListener('scroll', updateButtons, { passive: true });
        updateButtons();
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
initSearchPanel();
initCarousels();
initHeaderElevation();
initRevealOnScroll();
initProductPage();
initShopPage();
initSearchPage();
initCartPage();
initCheckoutPage();
initWishlistPage();
initComparePage();
initOrdersPage();
initTrackPage();
initProfilePage();
initAddressesPage();
