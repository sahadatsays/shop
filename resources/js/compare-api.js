const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';

const jsonHeaders = () => ({
    Accept: 'application/json',
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': csrfToken(),
    'X-Requested-With': 'XMLHttpRequest',
});

export const updateCompareBadge = (count, productIds = null) => {
    document.querySelectorAll('[data-compare-count-badge]').forEach((badge) => {
        badge.textContent = String(count);
        badge.hidden = count <= 0;
    });

    document.querySelectorAll('[data-compare-link]').forEach((link) => {
        link.setAttribute('aria-label', `Compare products, ${count} ${count === 1 ? 'item' : 'items'}`);
    });

    if (productIds) {
        syncCompareButtons(productIds);
    }
};

export const syncCompareButtons = (productIds) => {
    const ids = new Set(productIds.map(Number));

    document.querySelectorAll('[data-compare-toggle]').forEach((button) => {
        const productId = Number(button.dataset.productId);
        const active = ids.has(productId);

        button.setAttribute('aria-pressed', String(active));
        button.classList.toggle('border-olive-600', active);
        button.classList.toggle('bg-olive-50', active);
        button.classList.toggle('text-olive-700', active);
        button.classList.toggle('aria-pressed:text-olive-700', active);
    });
};

const applyComparePayload = (payload) => {
    if (payload.compare?.item_count !== undefined) {
        updateCompareBadge(payload.compare.item_count, payload.compare.product_ids ?? null);
    }
};

export const toggleCompare = async (productId) => {
    const response = await fetch('/compare/toggle', {
        method: 'POST',
        headers: jsonHeaders(),
        body: JSON.stringify({ product_id: productId }),
    });

    const payload = await response.json();

    if (!response.ok) {
        throw new Error(payload.message ?? 'Unable to update compare list.');
    }

    applyComparePayload(payload);

    return payload;
};

export const removeCompareItem = async (compareItemId) => {
    const response = await fetch(`/compare/items/${compareItemId}`, {
        method: 'DELETE',
        headers: jsonHeaders(),
    });

    const payload = await response.json();

    if (!response.ok) {
        throw new Error(payload.message ?? 'Unable to remove product from compare.');
    }

    applyComparePayload(payload);

    return payload;
};

export const clearCompare = async () => {
    const response = await fetch('/compare', {
        method: 'DELETE',
        headers: jsonHeaders(),
    });

    const payload = await response.json();

    if (!response.ok) {
        throw new Error(payload.message ?? 'Unable to clear compare list.');
    }

    applyComparePayload(payload);

    return payload;
};
