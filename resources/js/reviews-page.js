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

export const initReviewsPage = () => {
    const page = document.querySelector('[data-reviews]');

    if (!page) {
        return;
    }

    const list = page.querySelector('[data-reviews-list]');
    const dialog = page.querySelector('[data-review-dialog]');
    const deleteDialog = page.querySelector('[data-review-delete-dialog]');
    const form = page.querySelector('[data-review-form]');
    const ratingPicker = page.querySelector('[data-rating-picker]');
    const ratingValue = page.querySelector('[data-rating-value]');
    const editProductName = page.querySelector('[data-edit-product-name]');
    const deleteProductEl = page.querySelector('[data-delete-product]');
    const statusEl = page.querySelector('[data-reviews-status]');
    const countEl = page.querySelector('[data-reviews-count]');
    const totalEl = page.querySelector('[data-reviews-total]');
    const avgEl = page.querySelector('[data-reviews-avg]');
    const avgStarsEl = page.querySelector('[data-reviews-avg-stars]');
    const helpfulTotalEl = page.querySelector('[data-reviews-helpful-total]');
    const emptyState = page.querySelector('[data-reviews-empty]');
    const filterEmpty = page.querySelector('[data-reviews-filter-empty]');
    const sortSelect = page.querySelector('[data-reviews-sort]');

    let editingCard = null;
    let deletingCard = null;
    let activeFilter = 'All';

    const getCards = () => [...page.querySelectorAll('[data-review-card]')];

    const setStatus = (message, success = false) => {
        statusEl.textContent = message;
        statusEl.classList.toggle('text-green-700', success);
    };

    const updateStats = () => {
        const cards = getCards();
        const count = cards.length;

        totalEl.textContent = String(count);

        if (count === 0) {
            avgEl.textContent = '—';
            avgStarsEl.innerHTML = '';
            helpfulTotalEl.textContent = '0';
            emptyState.hidden = false;
            list.hidden = true;
            return;
        }

        emptyState.hidden = true;
        list.hidden = false;

        const ratings = cards.map((card) => Number(card.dataset.rating));
        const avg = Math.round((ratings.reduce((sum, r) => sum + r, 0) / count) * 10) / 10;
        const helpful = cards.reduce((sum, card) => sum + Number(card.dataset.helpful), 0);

        avgEl.textContent = String(avg);
        avgStarsEl.innerHTML = renderRatingHtml(avg, 'sm');
        helpfulTotalEl.textContent = String(helpful);
    };

    const updateCount = (visible) => {
        countEl.textContent = `${visible} ${visible === 1 ? 'review' : 'reviews'}`;
    };

    const setRatingPicker = (value) => {
        ratingValue.value = String(value);

        ratingPicker.querySelectorAll('[data-rating-star]').forEach((star) => {
            const starValue = Number(star.dataset.ratingStar);
            star.classList.toggle('text-bronze-500', starValue <= value);
            star.classList.toggle('text-navy-200', starValue > value);
            star.setAttribute('aria-pressed', String(starValue <= value));
        });
    };

    const sortCards = () => {
        const cards = getCards();
        const mode = sortSelect.value;

        cards.sort((a, b) => {
            if (mode === 'helpful') {
                return Number(b.dataset.helpful) - Number(a.dataset.helpful);
            }

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

    ratingPicker?.querySelectorAll('[data-rating-star]').forEach((star) => {
        star.addEventListener('click', () => setRatingPicker(Number(star.dataset.ratingStar)));

        star.addEventListener('mouseenter', () => {
            const hoverValue = Number(star.dataset.ratingStar);
            ratingPicker.querySelectorAll('[data-rating-star]').forEach((s) => {
                const v = Number(s.dataset.ratingStar);
                s.classList.toggle('text-bronze-400', v <= hoverValue);
                s.classList.toggle('text-navy-200', v > hoverValue);
            });
        });

        star.addEventListener('mouseleave', () => setRatingPicker(Number(ratingValue.value)));
    });

    const openDialog = () => {
        dialog.showModal();
        form.querySelector('#review-title')?.focus();
    };

    const closeDialog = () => {
        dialog.close();
        form.reset();
        editingCard = null;
    };

    page.querySelectorAll('[data-review-dialog-close]').forEach((btn) => {
        btn.addEventListener('click', closeDialog);
    });

    dialog?.addEventListener('click', (event) => {
        if (event.target === dialog) {
            closeDialog();
        }
    });

    list?.addEventListener('click', (event) => {
        const editBtn = event.target.closest('[data-review-edit]');
        const deleteBtn = event.target.closest('[data-review-delete]');

        if (editBtn) {
            editingCard = editBtn.closest('[data-review-card]');
            editProductName.textContent = editingCard.dataset.product;
            form.querySelector('#review-title').value = editingCard.dataset.title;
            form.querySelector('#review-body').value = editingCard.dataset.body;
            setRatingPicker(Number(editingCard.dataset.rating));
            openDialog();
        }

        if (deleteBtn) {
            deletingCard = deleteBtn.closest('[data-review-card]');
            deleteProductEl.textContent = deletingCard.dataset.product;
            deleteDialog.showModal();
        }
    });

    form?.addEventListener('submit', (event) => {
        event.preventDefault();

        if (!editingCard) {
            return;
        }

        const title = form.querySelector('#review-title').value.trim();
        const body = form.querySelector('#review-body').value.trim();
        const rating = Number(ratingValue.value);

        if (!title || !body || rating < 1) {
            setStatus('Please add a rating, title, and review text.');
            return;
        }

        editingCard.dataset.title = title;
        editingCard.dataset.body = body;
        editingCard.dataset.rating = String(rating);
        editingCard.querySelector('[data-review-title]').textContent = title;
        editingCard.querySelector('[data-review-body]').textContent = body;
        editingCard.querySelector('[data-review-rating-display]').innerHTML = renderRatingHtml(rating, 'sm');

        updateStats();
        applyFilter();
        setStatus('Review updated successfully.', true);
        closeDialog();
    });

    page.querySelector('[data-delete-cancel]')?.addEventListener('click', () => {
        deleteDialog.close();
        deletingCard = null;
    });

    page.querySelector('[data-delete-confirm]')?.addEventListener('click', () => {
        if (!deletingCard) {
            return;
        }

        const product = deletingCard.dataset.product;
        deletingCard.remove();
        updateStats();
        applyFilter();
        setStatus(`Review for ${product} deleted.`, true);
        deleteDialog.close();
        deletingCard = null;
    });

    deleteDialog?.addEventListener('click', (event) => {
        if (event.target === deleteDialog) {
            deleteDialog.close();
            deletingCard = null;
        }
    });

    sortCards();
    applyFilter();
};
