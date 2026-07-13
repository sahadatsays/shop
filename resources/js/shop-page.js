const initFiltersDrawer = () => {
    const panel = document.querySelector('[data-filters-panel]');
    const backdrop = document.querySelector('[data-filters-backdrop]');
    const toggle = document.querySelector('[data-filters-toggle]');

    if (!panel || !toggle || !backdrop) {
        return;
    }

    const setOpen = (open) => {
        panel.classList.toggle('-translate-x-full', !open);
        backdrop.hidden = !open;
        document.body.classList.toggle('overflow-hidden', open);
    };

    toggle.addEventListener('click', () => setOpen(true));
    backdrop.addEventListener('click', () => setOpen(false));

    panel.querySelectorAll('[data-filters-close]').forEach((button) => {
        button.addEventListener('click', () => setOpen(false));
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !backdrop.hidden) {
            setOpen(false);
            toggle.focus();
        }
    });
};

const initPriceRange = () => {
    document.querySelectorAll('[data-price-range]').forEach((widget) => {
        const minInput = widget.querySelector('[data-price-min]');
        const maxInput = widget.querySelector('[data-price-max]');
        const fill = widget.querySelector('[data-price-fill]');
        const minLabel = widget.querySelector('[data-price-min-label]');
        const maxLabel = widget.querySelector('[data-price-max-label]');
        const rangeMax = Number(widget.dataset.max) || 500;

        const render = () => {
            let low = Number(minInput.value);
            let high = Number(maxInput.value);

            if (low > high) {
                [low, high] = [high, low];
            }

            fill.style.left = `${(low / rangeMax) * 100}%`;
            fill.style.right = `${100 - (high / rangeMax) * 100}%`;
            minLabel.textContent = `$${low}`;
            maxLabel.textContent = `$${high}`;
        };

        minInput.addEventListener('input', () => {
            if (Number(minInput.value) > Number(maxInput.value)) {
                minInput.value = maxInput.value;
            }
            render();
        });

        maxInput.addEventListener('input', () => {
            if (Number(maxInput.value) < Number(minInput.value)) {
                maxInput.value = minInput.value;
            }
            render();
        });

        render();
    });
};

const initViewToggle = () => {
    const grid = document.querySelector('[data-product-view]');
    const gridButton = document.querySelector('[data-view-grid]');
    const listButton = document.querySelector('[data-view-list]');

    if (!grid || !gridButton || !listButton) {
        return;
    }

    const activeClasses = ['bg-navy-900', 'text-white'];
    const inactiveClasses = ['text-navy-500'];

    const setView = (view) => {
        grid.dataset.productView = view;

        [[gridButton, view === 'grid'], [listButton, view === 'list']].forEach(([button, isActive]) => {
            button.setAttribute('aria-pressed', String(isActive));
            activeClasses.forEach((cls) => button.classList.toggle(cls, isActive));
            inactiveClasses.forEach((cls) => button.classList.toggle(cls, !isActive));
        });
    };

    gridButton.addEventListener('click', () => setView('grid'));
    listButton.addEventListener('click', () => setView('list'));
};

const initFilterChips = () => {
    document.querySelectorAll('[data-filter-chip]').forEach((chip) => {
        chip.addEventListener('click', () => {
            chip.setAttribute('aria-pressed', String(chip.getAttribute('aria-pressed') !== 'true'));
        });
    });
};

export const initShopPage = () => {
    initFiltersDrawer();
    initPriceRange();
    initViewToggle();
    initFilterChips();
};
