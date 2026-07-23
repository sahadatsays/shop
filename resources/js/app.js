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
import { initReviewsPage } from './reviews-page';
import { initLoginPage } from './login-page';
import { initRegisterPage } from './register-page';
import { initForgotPasswordPage } from './forgot-password-page';
import { initNotificationsPage } from './notifications-page';
import { initSupportPage } from './support-page';
import { initAboutPage } from './about-page';
import { initContactPage } from './contact-page';

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

import { addToCart, updateCartBadge } from './cart-api';

const initAddToCart = () => {
    document.addEventListener('click', async (event) => {
        const button = event.target.closest('[data-add-to-cart]');

        if (!button || button.disabled) {
            return;
        }

        const productId = button.dataset.productId
            ?? button.closest('[data-product-id]')?.dataset.productId
            ?? document.querySelector('[data-product-page]')?.dataset.productId;

        if (!productId) {
            return;
        }

        event.preventDefault();

        const quantityInput = button.closest('[data-atc-anchor]')?.querySelector('[data-qty-input]')
            ?? document.querySelector('[data-product-page] [data-qty-input]');
        const quantity = Number(quantityInput?.value) || 1;

        const originalText = button.textContent.trim();
        button.disabled = true;

        try {
            const payload = await addToCart(Number(productId), quantity);
            button.textContent = 'Added ✓';
            updateCartBadge(payload.cart?.item_count ?? 0);

            setTimeout(() => {
                button.textContent = originalText;
                button.disabled = false;
            }, 1500);
        } catch (error) {
            button.textContent = 'Unavailable';
            alert(error.message);

            setTimeout(() => {
                button.textContent = originalText;
                button.disabled = false;
            }, 2000);
        }
    });
};

initAddToCart();
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
initReviewsPage();
initLoginPage();
initRegisterPage();
initForgotPasswordPage();
initNotificationsPage();
initSupportPage();
initAboutPage();
initContactPage();
