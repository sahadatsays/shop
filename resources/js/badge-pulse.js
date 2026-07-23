export const pulseBadges = (selector) => {
    document.querySelectorAll(selector).forEach((badge) => {
        badge.classList.remove('is-pulsing');
        void badge.offsetWidth;
        badge.classList.add('is-pulsing');
        badge.addEventListener('animationend', () => badge.classList.remove('is-pulsing'), { once: true });
    });
};
