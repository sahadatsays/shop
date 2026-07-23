const STAR_PATH = 'M12 2.5l2.9 5.9 6.5.9-4.7 4.6 1.1 6.5L12 17.3l-5.8 3.1 1.1-6.5L2.6 9.3l6.5-.9L12 2.5Z';

const renderRatingHtml = (value, size = 'sm') => {
    const starSize = size === 'sm' ? 'size-3.5' : 'size-4.5';
    const percent = Math.max(0, Math.min(100, (value / 5) * 100));
    const stars = Array.from({ length: 5 }, () =>
        `<svg class="${starSize}" viewBox="0 0 24 24" fill="currentColor"><path d="${STAR_PATH}"/></svg>`,
    ).join('');

    return `<span class="relative inline-flex shrink-0" role="img" aria-label="Rated ${value} out of 5 stars">
        <span class="flex gap-0.5 text-navy-200" aria-hidden="true">${stars}</span>
        <span class="absolute inset-0 overflow-hidden text-bronze-500" style="width: ${percent}%" aria-hidden="true">
            <span class="flex gap-0.5">${stars}</span>
        </span>
    </span>`;
};

const parseDate = (value) => new Date(value);

const ratingBucket = (rating) => {
    if (rating >= 5) {
        return '5 stars';
    }

    if (rating >= 4) {
        return '4 stars';
    }

    return '3 stars & below';
};

const initRatingPicker = (picker, valueInput) => {
    if (!picker || !valueInput) {
        return;
    }

    const setRating = (value) => {
        valueInput.value = String(value);

        picker.querySelectorAll('[data-rating-star]').forEach((star) => {
            const starValue = Number(star.dataset.ratingStar);
            star.classList.toggle('text-bronze-500', starValue <= value);
            star.classList.toggle('text-navy-200', starValue > value);
            star.setAttribute('aria-pressed', String(starValue <= value));
        });
    };

    picker.querySelectorAll('[data-rating-star]').forEach((star) => {
        star.addEventListener('click', () => setRating(Number(star.dataset.ratingStar)));

        star.addEventListener('mouseenter', () => {
            const hoverValue = Number(star.dataset.ratingStar);
            picker.querySelectorAll('[data-rating-star]').forEach((s) => {
                const v = Number(s.dataset.ratingStar);
                s.classList.toggle('text-bronze-400', v <= hoverValue);
                s.classList.toggle('text-navy-200', v > hoverValue);
            });
        });

        star.addEventListener('mouseleave', () => setRating(Number(valueInput.value || 5)));
    });

    setRating(Number(valueInput.value || 5));

    return setRating;
};

export const initRatingPickers = (root = document) => {
    root.querySelectorAll('[data-rating-picker]').forEach((picker) => {
        const pickerId = picker.dataset.ratingPicker;
        const valueInput = root.querySelector(`[data-rating-value="${pickerId}"]`);

        initRatingPicker(picker, valueInput);
    });
};

export const initReviewsPage = () => {
    const page = document.querySelector('[data-reviews]');

    if (!page) {
        return;
    }

    initRatingPickers(page);

    const list = page.querySelector('[data-reviews-list]');
    const dialog = page.querySelector('[data-review-dialog]');
    const writeDialog = page.querySelector('[data-review-write-dialog]');
    const deleteDialog = page.querySelector('[data-review-delete-dialog]');
    const form = page.querySelector('[data-review-form]');
    const writeForm = page.querySelector('[data-review-write-form]');
    const deleteForm = page.querySelector('[data-review-delete-form]');
    const editPicker = page.querySelector('[data-rating-picker="edit-rating"]');
    const editRatingValue = page.querySelector('[data-rating-value="edit-rating"]');
    const writePicker = page.querySelector('[data-rating-picker="write-rating"]');
    const writeRatingValue = page.querySelector('[data-rating-value="write-rating"]');
    const writeProductName = page.querySelector('[data-write-product-name]');
    const writeProductIdInput = page.querySelector('[data-write-product-id]');
    const editProductName = page.querySelector('[data-edit-product-name]');
    const deleteProductEl = page.querySelector('[data-delete-product]');
    const countEl = page.querySelector('[data-reviews-count]');
    const totalEl = page.querySelector('[data-reviews-total]');
    const avgEl = page.querySelector('[data-reviews-avg]');
    const avgStarsEl = page.querySelector('[data-reviews-avg-stars]');
    const emptyState = page.querySelector('[data-reviews-empty]');
    const filterEmpty = page.querySelector('[data-reviews-filter-empty]');
    const sortSelect = page.querySelector('[data-reviews-sort]');

    const setEditRating = initRatingPicker(editPicker, editRatingValue);
    const setWriteRating = initRatingPicker(writePicker, writeRatingValue);

    let activeFilter = 'All';

    const getCards = () => [...page.querySelectorAll('[data-review-card]')];

    const updateStats = () => {
        const cards = getCards();
        const count = cards.length;

        totalEl.textContent = String(count);

        if (count === 0) {
            avgEl.textContent = '—';
            if (avgStarsEl) {
                avgStarsEl.innerHTML = '';
            }
            emptyState.hidden = false;
            list.hidden = true;
            return;
        }

        emptyState.hidden = true;
        list.hidden = false;

        const ratings = cards.map((card) => Number(card.dataset.rating));
        const avg = Math.round((ratings.reduce((sum, r) => sum + r, 0) / count) * 10) / 10;

        avgEl.textContent = String(avg);
        if (avgStarsEl) {
            avgStarsEl.innerHTML = renderRatingHtml(avg, 'sm');
        }
    };

    const updateCount = (visible) => {
        countEl.textContent = `${visible} ${visible === 1 ? 'review' : 'reviews'}`;
    };

    const sortCards = () => {
        const cards = getCards();
        const mode = sortSelect.value;

        cards.sort((a, b) => {
            if (mode === 'rating-high') {
                return Number(b.dataset.rating) - Number(a.dataset.rating);
            }

            if (mode === 'rating-low') {
                return Number(a.dataset.rating) - Number(b.dataset.rating);
            }

            const dateA = parseDate(a.dataset.date);
            const dateB = parseDate(b.dataset.date);

            return mode === 'oldest' ? dateA - dateB : dateB - dateA;
        });

        cards.forEach((card) => list.appendChild(card));
    };

    const applyFilter = () => {
        const cards = getCards();
        let visible = 0;

        cards.forEach((card) => {
            const show = activeFilter === 'All' || ratingBucket(Number(card.dataset.rating)) === activeFilter;
            card.hidden = !show;
            visible += show ? 1 : 0;
        });

        updateCount(visible);
        filterEmpty.hidden = visible > 0 || cards.length === 0;
        list.hidden = cards.length === 0;
    };

    const openWriteDialog = (button) => {
        writeForm.action = button.dataset.storeUrl;
        writeProductName.textContent = button.dataset.productName;
        writeProductIdInput.value = button.dataset.productId;
        writeForm.querySelector('#write-review-title').value = '';
        writeForm.querySelector('#write-review-body').value = '';
        setWriteRating?.(5);
        writeDialog.showModal();
        writeForm.querySelector('#write-review-title')?.focus();
    };

    page.querySelectorAll('[data-reviews-filter]').forEach((button) => {
        button.addEventListener('click', () => {
            activeFilter = button.dataset.reviewsFilter;

            page.querySelectorAll('[data-reviews-filter]').forEach((sibling) => {
                const active = sibling === button;
                sibling.setAttribute('aria-pressed', String(active));
                sibling.classList.toggle('bg-navy-900', active);
                sibling.classList.toggle('text-white', active);
                sibling.classList.toggle('shadow-soft', active);
                sibling.classList.toggle('bg-surface', !active);
                sibling.classList.toggle('text-navy-700', !active);
            });

            applyFilter();
        });
    });

    page.querySelector('[data-reviews-clear-filter]')?.addEventListener('click', () => {
        page.querySelector('[data-reviews-filter="All"]')?.click();
    });

    sortSelect?.addEventListener('change', () => {
        sortCards();
        applyFilter();
    });

    page.querySelectorAll('[data-review-write]').forEach((button) => {
        button.addEventListener('click', () => openWriteDialog(button));
    });

    page.querySelectorAll('[data-review-write-close]').forEach((btn) => {
        btn.addEventListener('click', () => writeDialog.close());
    });

    writeDialog?.addEventListener('click', (event) => {
        if (event.target === writeDialog) {
            writeDialog.close();
        }
    });

    const closeEditDialog = () => dialog.close();

    page.querySelectorAll('[data-review-dialog-close]').forEach((btn) => {
        btn.addEventListener('click', closeEditDialog);
    });

    dialog?.addEventListener('click', (event) => {
        if (event.target === dialog) {
            closeEditDialog();
        }
    });

    list?.addEventListener('click', (event) => {
        const editBtn = event.target.closest('[data-review-edit]');
        const deleteBtn = event.target.closest('[data-review-delete]');

        if (editBtn) {
            const card = editBtn.closest('[data-review-card]');
            editProductName.textContent = card.dataset.product;
            form.action = card.dataset.updateUrl;
            form.querySelector('#review-title').value = card.dataset.title;
            form.querySelector('#review-body').value = card.dataset.body;
            setEditRating?.(Number(card.dataset.rating));
            dialog.showModal();
            form.querySelector('#review-title')?.focus();
        }

        if (deleteBtn) {
            const card = deleteBtn.closest('[data-review-card]');
            deleteProductEl.textContent = card.dataset.product;
            deleteForm.action = card.dataset.deleteUrl;
            deleteDialog.showModal();
        }
    });

    page.querySelector('[data-delete-cancel]')?.addEventListener('click', () => {
        deleteDialog.close();
    });

    deleteDialog?.addEventListener('click', (event) => {
        if (event.target === deleteDialog) {
            deleteDialog.close();
        }
    });

    const openProductId = page.dataset.openWriteProductId;

    if (openProductId) {
        const button = page.querySelector(`[data-review-write][data-product-id="${openProductId}"]`);

        if (button) {
            openWriteDialog(button);
        }
    }

    sortCards();
    applyFilter();
};
