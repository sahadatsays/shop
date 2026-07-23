export const initHeroSlider = () => {
    const root = document.querySelector('[data-hero-slider]');

    if (!root) {
        return;
    }

    const slides = [...root.querySelectorAll('[data-hero-slide]')];

    if (slides.length <= 1) {
        return;
    }

    const dots = [...root.querySelectorAll('[data-hero-dot]')];
    const prev = root.querySelector('[data-hero-prev]');
    const next = root.querySelector('[data-hero-next]');
    const duration = 6000;
    let index = 0;
    let timer = null;
    let startedAt = 0;
    let raf = null;

    const setActive = (nextIndex) => {
        index = (nextIndex + slides.length) % slides.length;

        slides.forEach((slide, i) => {
            const active = i === index;
            slide.classList.toggle('opacity-100', active);
            slide.classList.toggle('z-10', active);
            slide.classList.toggle('opacity-0', !active);
            slide.classList.toggle('z-0', !active);
            slide.classList.toggle('pointer-events-none', !active);
            slide.setAttribute('aria-hidden', active ? 'false' : 'true');
        });

        dots.forEach((dot, i) => {
            const active = i === index;
            dot.classList.toggle('ring-2', active);
            dot.classList.toggle('ring-bronze-400', active);
            const progress = dot.querySelector('[data-hero-progress]');
            if (progress) {
                progress.style.width = '0%';
            }
        });

        startedAt = performance.now();
    };

    const tick = (now) => {
        const elapsed = now - startedAt;
        const progress = Math.min(100, (elapsed / duration) * 100);
        const activeDot = dots[index]?.querySelector('[data-hero-progress]');

        if (activeDot) {
            activeDot.style.width = `${progress}%`;
        }

        if (elapsed >= duration) {
            setActive(index + 1);
        }

        raf = requestAnimationFrame(tick);
    };

    const start = () => {
        cancelAnimationFrame(raf);
        clearInterval(timer);
        startedAt = performance.now();
        raf = requestAnimationFrame(tick);
    };

    const go = (nextIndex) => {
        setActive(nextIndex);
        start();
    };

    prev?.addEventListener('click', () => go(index - 1));
    next?.addEventListener('click', () => go(index + 1));
    dots.forEach((dot) => {
        dot.addEventListener('click', () => go(Number(dot.dataset.heroDot)));
    });

    let touchStartX = 0;
    root.addEventListener('touchstart', (event) => {
        touchStartX = event.changedTouches[0]?.clientX ?? 0;
    }, { passive: true });

    root.addEventListener('touchend', (event) => {
        const delta = (event.changedTouches[0]?.clientX ?? 0) - touchStartX;

        if (Math.abs(delta) < 40) {
            return;
        }

        go(delta < 0 ? index + 1 : index - 1);
    }, { passive: true });

    root.addEventListener('mouseenter', () => cancelAnimationFrame(raf));
    root.addEventListener('mouseleave', start);
    root.addEventListener('focusin', () => cancelAnimationFrame(raf));
    root.addEventListener('focusout', start);

    setActive(0);
    start();
};
