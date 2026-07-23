const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';

const jsonHeaders = () => ({
    Accept: 'application/json',
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': csrfToken(),
    'X-Requested-With': 'XMLHttpRequest',
});

export const updateWishlistBadge = (count, productIds = null) => {
    document.querySelectorAll('[data-wishlist-count]').forEach((badge) => {
        badge.textContent = String(count);
        badge.hidden = count <= 0;
    });

    document.querySelectorAll('[data-wishlist-count-label]').forEach((label) => {
        label.textContent = `${count} saved ${count === 1 ? 'item' : 'items'}`;
    });

    document.querySelectorAll('[data-wishlist-link]').forEach((link) => {
        link.setAttribute('aria-label', `Wishlist, ${count} ${count === 1 ? 'item' : 'items'}`);
    });

    if (productIds) {
        syncWishlistButtons(productIds);
    }
};

export const syncWishlistButtons = (productIds) => {
    const ids = new Set(productIds.map(Number));

    document.querySelectorAll('[data-wishlist-toggle]').forEach((button) => {
        const productId = Number(button.dataset.productId);
        const active = ids.has(productId);

        button.setAttribute('aria-pressed', String(active));
        button.classList.toggle('border-bronze-500', active);
        button.classList.toggle('bg-bronze-50', active);
        button.classList.toggle('text-bronze-700', active);
        button.classList.toggle('text-red-600', active);
    });
};

const applyWishlistPayload = (payload) => {
    if (payload.wishlist?.item_count !== undefined) {
        updateWishlistBadge(payload.wishlist.item_count, payload.wishlist.product_ids ?? null);
    }
};

export const toggleWishlist = async (productId) => {
    const response = await fetch('/wishlist/toggle', {
        method: 'POST',
        headers: jsonHeaders(),
        body: JSON.stringify({ product_id: productId }),
    });

    const payload = await response.json();

    if (!response.ok) {
        throw new Error(payload.message ?? 'Unable to update wishlist.');
    }

    applyWishlistPayload(payload);

    return payload;
};

export const removeWishlistItem = async (wishlistItemId) => {
    const response = await fetch(`/wishlist/items/${wishlistItemId}`, {
        method: 'DELETE',
        headers: jsonHeaders(),
    });

    const payload = await response.json();

    if (!response.ok) {
        throw new Error(payload.message ?? 'Unable to remove item.');
    }

    applyWishlistPayload(payload);

    return payload;
};

export const moveWishlistItemToCart = async (wishlistItemId) => {
    const response = await fetch(`/wishlist/items/${wishlistItemId}/move-to-cart`, {
        method: 'POST',
        headers: jsonHeaders(),
    });

    const payload = await response.json();

    if (!response.ok) {
        throw new Error(payload.message ?? 'Unable to move item to cart.');
    }

    applyWishlistPayload(payload);

    return payload;
};

export const moveAllWishlistToCart = async () => {
    const response = await fetch('/wishlist/move-all-to-cart', {
        method: 'POST',
        headers: jsonHeaders(),
    });

    const payload = await response.json();

    if (!response.ok) {
        throw new Error(payload.message ?? 'Unable to move items to cart.');
    }

    applyWishlistPayload(payload);

    return payload;
};

export const clearWishlist = async () => {
    const response = await fetch('/wishlist', {
        method: 'DELETE',
        headers: jsonHeaders(),
    });

    const payload = await response.json();

    if (!response.ok) {
        throw new Error(payload.message ?? 'Unable to clear wishlist.');
    }

    applyWishlistPayload(payload);

    return payload;
};
