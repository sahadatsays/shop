const animateCounter = (element, target, prefix, suffix, decimals) => {
    const duration = 1500;
    const start = performance.now();

    const step = (now) => {
        const progress = Math.min((now - start) / duration, 1);
        const eased = 1 - (1 - progress) ** 3;
        const current = target * eased;

        const formatted = decimals > 0
            ? current.toFixed(decimals)
            : Math.floor(current).toLocaleString();

        element.textContent = `${prefix}${formatted}${suffix}`;

        if (progress < 1) {
            requestAnimationFrame(step);
        }
    };

    requestAnimationFrame(step);
};

export const initAboutPage = () => {
    const page = document.querySelector('[data-about]');

    if (!page) {
        return;
    }

    const counters = [...page.querySelectorAll('[data-stat-counter]')];

    if (counters.length === 0 || !('IntersectionObserver' in window)) {
        counters.forEach((counter) => {
            const value = Number(counter.dataset.statValue);
            const decimals = Number(counter.dataset.statDecimals ?? 0);
            const prefix = counter.dataset.statPrefix ?? '';
            const suffix = counter.dataset.statSuffix ?? '';
            const formatted = decimals > 0 ? value.toFixed(decimals) : value.toLocaleString();
            counter.textContent = `${prefix}${formatted}${suffix}`;
        });
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting || entry.target.dataset.counted === 'true') {
                    return;
                }

                entry.target.dataset.counted = 'true';

                animateCounter(
                    entry.target,
                    Number(entry.target.dataset.statValue),
                    entry.target.dataset.statPrefix ?? '',
                    entry.target.dataset.statSuffix ?? '',
                    Number(entry.target.dataset.statDecimals ?? 0),
                );
            });
        },
        { threshold: 0.4 },
    );

    counters.forEach((counter) => observer.observe(counter));
};
