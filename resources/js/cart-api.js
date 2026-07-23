const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';

const jsonHeaders = () => ({
    Accept: 'application/json',
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': csrfToken(),
    'X-Requested-With': 'XMLHttpRequest',
});

export const updateCartBadge = (count) => {
    document.querySelectorAll('[data-cart-count]').forEach((badge) => {
        badge.textContent = String(count);
        badge.hidden = count <= 0;
    });

    document.querySelectorAll('[data-cart-count-label]').forEach((label) => {
        label.textContent = `${count} ${count === 1 ? 'item' : 'items'}`;
    });

    document.querySelectorAll('[data-cart-link]').forEach((link) => {
        link.setAttribute('aria-label', `Cart, ${count} ${count === 1 ? 'item' : 'items'}`);
    });
};

export const addToCart = async (productId, quantity = 1) => {
    const response = await fetch('/cart/items', {
        method: 'POST',
        headers: jsonHeaders(),
        body: JSON.stringify({ product_id: productId, quantity }),
    });

    const payload = await response.json();

    if (!response.ok) {
        throw new Error(payload.message ?? 'Unable to add item to cart.');
    }

    if (payload.cart?.item_count !== undefined) {
        updateCartBadge(payload.cart.item_count);
    }

    return payload;
};

export const updateCartItem = async (cartItemId, quantity) => {
    const response = await fetch(`/cart/items/${cartItemId}`, {
        method: 'PATCH',
        headers: jsonHeaders(),
        body: JSON.stringify({ quantity }),
    });

    const payload = await response.json();

    if (!response.ok) {
        throw new Error(payload.message ?? 'Unable to update cart.');
    }

    if (payload.cart?.item_count !== undefined) {
        updateCartBadge(payload.cart.item_count);
    }

    return payload;
};

export const removeCartItem = async (cartItemId) => {
    const response = await fetch(`/cart/items/${cartItemId}`, {
        method: 'DELETE',
        headers: jsonHeaders(),
    });

    const payload = await response.json();

    if (!response.ok) {
        throw new Error(payload.message ?? 'Unable to remove item.');
    }

    if (payload.cart?.item_count !== undefined) {
        updateCartBadge(payload.cart.item_count);
    }

    return payload;
};

export const saveCart = async () => {
    const response = await fetch('/cart/save', {
        method: 'POST',
        headers: jsonHeaders(),
    });

    const payload = await response.json();

    if (!response.ok) {
        throw new Error(payload.message ?? 'Unable to save cart.');
    }

    return payload;
};
