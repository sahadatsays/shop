const ACTIVE_THUMB_CLASSES = ['border-navy-900', 'shadow-soft'];
const INACTIVE_THUMB_CLASSES = ['border-transparent', 'opacity-70'];

const initGallery = () => {
    const gallery = document.querySelector('[data-gallery]');

    if (!gallery) {
        return;
    }

    const stage = gallery.querySelector('[data-stage]');
    const artLayers = [...gallery.querySelectorAll('[data-art]')];
    const stage360 = gallery.querySelector('[data-stage-360]');
    const zoomHint = gallery.querySelector('[data-zoom-hint]');
    const thumbs = [...gallery.querySelectorAll('[data-thumb]')];
    const thumb360 = gallery.querySelector('[data-thumb-360]');
    const allThumbs = thumb360 ? [...thumbs, thumb360] : thumbs;

    const setActiveThumb = (button) => {
        allThumbs.forEach((thumb) => {
            const isActive = thumb === button;
            thumb.setAttribute('aria-selected', String(isActive));
            thumb.classList.toggle(ACTIVE_THUMB_CLASSES[0], isActive);
            thumb.classList.toggle(ACTIVE_THUMB_CLASSES[1], isActive);
            thumb.classList.toggle(INACTIVE_THUMB_CLASSES[0], !isActive);
            thumb.classList.toggle(INACTIVE_THUMB_CLASSES[1], !isActive);
        });
    };

    const showArt = (index) => {
        stage360.hidden = true;
        stage.classList.add('cursor-zoom-in');
        artLayers.forEach((layer) => {
            layer.hidden = layer.dataset.art !== String(index);
        });
    };

    thumbs.forEach((thumb) => {
        thumb.addEventListener('click', () => {
            showArt(thumb.dataset.thumb);
            setActiveThumb(thumb);
        });
    });

    /* Hover zoom — follows the cursor, photo view only */
    const activeLayer = () => artLayers.find((layer) => !layer.hidden);

    stage.addEventListener('mousemove', (event) => {
        if (!stage360.hidden) {
            return;
        }

        const layer = activeLayer();

        if (!layer) {
            return;
        }

        const rect = stage.getBoundingClientRect();
        const x = ((event.clientX - rect.left) / rect.width) * 100;
        const y = ((event.clientY - rect.top) / rect.height) * 100;

        layer.style.transformOrigin = `${x}% ${y}%`;
        layer.style.transform = 'scale(1.9)';
    });

    stage.addEventListener('mouseleave', () => {
        artLayers.forEach((layer) => {
            layer.style.transform = '';
        });
    });

    /* 360° viewer — drag horizontally to rotate */
    if (thumb360 && stage360) {
        const spinObject = stage360.querySelector('[data-spin-object]');
        const degreesLabel = stage360.querySelector('[data-spin-degrees]');
        let angle = 0;
        let dragging = false;
        let lastX = 0;

        thumb360.addEventListener('click', () => {
            artLayers.forEach((layer) => {
                layer.hidden = true;
                layer.style.transform = '';
            });
            stage360.hidden = false;
            stage.classList.remove('cursor-zoom-in');
            zoomHint?.classList.add('!opacity-0');
            setActiveThumb(thumb360);
        });

        thumbs.forEach((thumb) => {
            thumb.addEventListener('click', () => zoomHint?.classList.remove('!opacity-0'));
        });

        const applyRotation = () => {
            spinObject.style.transform = `rotateY(${angle}deg)`;

            if (degreesLabel) {
                degreesLabel.textContent = `${((Math.round(angle) % 360) + 360) % 360}°`;
            }
        };

        stage360.addEventListener('pointerdown', (event) => {
            dragging = true;
            lastX = event.clientX;
            stage360.setPointerCapture(event.pointerId);
        });

        stage360.addEventListener('pointermove', (event) => {
            if (!dragging) {
                return;
            }

            angle += (event.clientX - lastX) * 0.6;
            lastX = event.clientX;
            applyRotation();
        });

        ['pointerup', 'pointercancel'].forEach((type) => {
            stage360.addEventListener(type, () => {
                dragging = false;
            });
        });
    }
};

const initQuantitySteppers = () => {
    document.querySelectorAll('[data-quantity]').forEach((stepper) => {
        const input = stepper.querySelector('[data-qty-input]');
        const min = Number(input.min) || 1;
        const max = Number(input.max) || 99;

        const step = (delta) => {
            const next = Math.min(max, Math.max(min, (Number(input.value) || min) + delta));
            input.value = String(next);
        };

        stepper.querySelector('[data-qty-minus]')?.addEventListener('click', () => step(-1));
        stepper.querySelector('[data-qty-plus]')?.addEventListener('click', () => step(1));
        input.addEventListener('change', () => step(0));
    });
};

const initOptionGroups = () => {
    document.querySelectorAll('[data-option-group]').forEach((group) => {
        const label = group.querySelector('[data-option-label]');
        const options = [...group.querySelectorAll('[data-option-value]')];
        const isSwatchGroup = options.some((option) => option.querySelector('span'));

        options.forEach((option) => {
            option.addEventListener('click', () => {
                options.forEach((sibling) => {
                    const isSelected = sibling === option;
                    sibling.setAttribute('aria-pressed', String(isSelected));

                    if (isSwatchGroup) {
                        sibling.classList.toggle('ring-navy-900', isSelected);
                        sibling.classList.toggle('ring-transparent', !isSelected);
                    } else {
                        ['border-navy-900', 'bg-navy-900', 'text-white', 'shadow-soft'].forEach((cls) => {
                            sibling.classList.toggle(cls, isSelected);
                        });
                        ['border-navy-200', 'bg-surface', 'text-navy-700'].forEach((cls) => {
                            sibling.classList.toggle(cls, !isSelected);
                        });
                    }
                });

                if (label) {
                    label.textContent = option.dataset.optionValue;
                }
            });
        });
    });
};

const initToggleButtons = () => {
    document.querySelectorAll('[data-toggle-active]').forEach((button) => {
        button.setAttribute('aria-pressed', 'false');

        button.addEventListener('click', () => {
            const active = button.getAttribute('aria-pressed') !== 'true';
            button.setAttribute('aria-pressed', String(active));
            button.classList.toggle('border-bronze-500', active);
            button.classList.toggle('bg-bronze-50', active);
            button.classList.toggle('text-bronze-700', active);
        });
    });
};

const initStickyAddToCart = () => {
    const bar = document.querySelector('[data-sticky-atc]');
    const anchor = document.querySelector('[data-atc-anchor]');

    if (!bar || !anchor || !('IntersectionObserver' in window)) {
        return;
    }

    /*
     * The huge bottom rootMargin makes the entry intersect whenever the anchor
     * is at or below the viewport top, so the observer reliably fires even when
     * a fast scroll jumps past the anchor between frames.
     */
    const observer = new IntersectionObserver(
        ([entry]) => {
            const show = !entry.isIntersecting;
            bar.classList.toggle('translate-y-full', !show);
            bar.setAttribute('aria-hidden', String(!show));
        },
        { rootMargin: '0px 0px 100000px 0px', threshold: 0 },
    );

    observer.observe(anchor);
};

export const initProductPage = () => {
    initGallery();
    initQuantitySteppers();
    initOptionGroups();
    initToggleButtons();
    initStickyAddToCart();
};
