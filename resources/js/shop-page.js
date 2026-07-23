const VIEW_STORAGE_KEY = 'valor_shop_view_mode';

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

const initPriceRange = (form) => {
    document.querySelectorAll('[data-price-range]').forEach((widget) => {
        const minInput = widget.querySelector('[data-price-min]');
        const maxInput = widget.querySelector('[data-price-max]');
        const fill = widget.querySelector('[data-price-fill]');
        const minLabel = widget.querySelector('[data-price-min-label]');
        const maxLabel = widget.querySelector('[data-price-max-label]');
        const minHidden = widget.querySelector('[data-price-min-input]');
        const maxHidden = widget.querySelector('[data-price-max-input]');
        const rangeMin = Number(widget.dataset.min) || 0;
        const rangeMax = Number(widget.dataset.max) || 500;

        const render = () => {
            let low = Number(minInput.value);
            let high = Number(maxInput.value);

            if (low > high) {
                [low, high] = [high, low];
            }

            const span = Math.max(rangeMax - rangeMin, 1);
            fill.style.left = `${((low - rangeMin) / span) * 100}%`;
            fill.style.right = `${100 - ((high - rangeMin) / span) * 100}%`;
            minLabel.textContent = `$${low}`;
            maxLabel.textContent = `$${high}`;

            if (minHidden) {
                minHidden.value = low <= rangeMin ? '' : String(low);
            }

            if (maxHidden) {
                maxHidden.value = high >= rangeMax ? '' : String(high);
            }
        };

        const submitSoon = () => {
            render();
            form?.requestSubmit();
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

        minInput.addEventListener('change', submitSoon);
        maxInput.addEventListener('change', submitSoon);

        render();
    });
};

const initViewToggle = () => {
    const grid = document.querySelector('[data-product-grid]');
    const list = document.querySelector('[data-product-list]');
    const gridButton = document.querySelector('[data-view-grid]');
    const listButton = document.querySelector('[data-view-list]');

    if (!grid || !list || !gridButton || !listButton) {
        return;
    }

    const activeClasses = ['bg-navy-900', 'text-white'];
    const inactiveClasses = ['text-navy-500'];

    const setView = (view) => {
        const isGrid = view === 'grid';
        grid.hidden = !isGrid;
        list.hidden = isGrid;

        if (grid.dataset) {
            grid.dataset.productView = view;
        }

        [[gridButton, isGrid], [listButton, !isGrid]].forEach(([button, isActive]) => {
            button.setAttribute('aria-pressed', String(isActive));
            activeClasses.forEach((cls) => button.classList.toggle(cls, isActive));
            inactiveClasses.forEach((cls) => button.classList.toggle(cls, !isActive));
        });

        localStorage.setItem(VIEW_STORAGE_KEY, view);
    };

    gridButton.addEventListener('click', () => setView('grid'));
    listButton.addEventListener('click', () => setView('list'));

    setView(localStorage.getItem(VIEW_STORAGE_KEY) === 'list' ? 'list' : 'grid');
};

const initFilterChips = () => {
    document.querySelectorAll('[data-filter-chip]').forEach((chip) => {
        chip.addEventListener('click', () => {
            chip.setAttribute('aria-pressed', String(chip.getAttribute('aria-pressed') !== 'true'));
        });
    });
};

const showSkeleton = (shop) => {
    shop.querySelector('[data-shop-skeleton]')?.removeAttribute('hidden');
    shop.querySelector('[data-shop-results]')?.classList.add('hidden');
};

const initShopForm = () => {
    const shop = document.querySelector('[data-shop]');
    const form = shop?.querySelector('[data-shop-form]');

    if (!shop || !form) {
        return;
    }

    initPriceRange(form);

    const submitForm = () => {
        showSkeleton(shop);
        form.requestSubmit();
    };

    form.querySelectorAll('input[type="checkbox"], select[name="sort"], select[name="per_page"]').forEach((input) => {
        input.addEventListener('change', submitForm);
    });

    let searchTimer;
    const searchInput = form.querySelector('[data-shop-search]');

    searchInput?.addEventListener('input', () => {
        window.clearTimeout(searchTimer);
        searchTimer = window.setTimeout(submitForm, 450);
    });

    form.addEventListener('submit', () => {
        showSkeleton(shop);
    });
};

export const initShopPage = () => {
    initFiltersDrawer();
    initViewToggle();
    initFilterChips();
    initShopForm();
};
