import { addToCart, updateCartBadge } from './cart-api';
import {
    clearWishlist,
    moveAllWishlistToCart,
    moveWishlistItemToCart,
    removeWishlistItem,
} from './wishlist-api';

const REMOVE_ANIMATION_MS = 300;

const showAddedFeedback = (button) => {
    const label = button.querySelector('[data-action-label]');

    if (!label || button.disabled) {
        return;
    }

    const original = label.textContent;
    button.disabled = true;
    label.textContent = 'Added \u2713';

    setTimeout(() => {
        label.textContent = original;
        button.disabled = false;
    }, 1600);
};

export const initWishlistPage = () => {
    const wishlist = document.querySelector('[data-wishlist]');

    if (!wishlist) {
        return;
    }

    const grid = wishlist.querySelector('[data-wishlist-grid]');
    const emptyState = wishlist.querySelector('[data-wishlist-empty]');
    const actions = wishlist.querySelector('[data-wishlist-actions]');
    const countLabel = wishlist.querySelector('[data-wishlist-count-label]');

    const items = () => [...grid.querySelectorAll('[data-wishlist-item]')];

    const updateState = () => {
        const count = items().length;
        countLabel.textContent = `${count} saved ${count === 1 ? 'item' : 'items'}`;

        const isEmpty = count === 0;
        emptyState.hidden = !isEmpty;
        actions.hidden = isEmpty;
        grid.hidden = isEmpty;
    };

    const removeItemElement = (item) => {
        item.classList.add('opacity-0', 'scale-95');

        setTimeout(() => {
            item.remove();
            updateState();
        }, REMOVE_ANIMATION_MS);
    };

    const removeItem = async (item, wishlistItemId) => {
        item.classList.add('opacity-0', 'scale-95');

        try {
            await removeWishlistItem(wishlistItemId);
        } catch (error) {
            item.classList.remove('opacity-0', 'scale-95');
            alert(error.message);
            return;
        }

        removeItemElement(item);
    };

    grid.addEventListener('click', async (event) => {
        const removeButton = event.target.closest('[data-wishlist-remove]');

        if (removeButton) {
            event.preventDefault();
            const item = removeButton.closest('[data-wishlist-item]');
            await removeItem(item, removeButton.dataset.wishlistItemId);
            return;
        }

        const moveButton = event.target.closest('[data-wishlist-move-to-cart]');

        if (moveButton) {
            event.preventDefault();
            moveButton.disabled = true;

            try {
                const payload = await moveWishlistItemToCart(moveButton.dataset.wishlistItemId);
                updateCartBadge(payload.cart?.item_count ?? 0);
                showAddedFeedback(moveButton);
                removeItemElement(moveButton.closest('[data-wishlist-item]'));
            } catch (error) {
                moveButton.disabled = false;
                alert(error.message);
            }

            return;
        }

        const addButton = event.target.closest('[data-add-to-cart]');

        if (addButton) {
            event.preventDefault();
            addButton.disabled = true;

            try {
                const payload = await addToCart(Number(addButton.dataset.productId), 1);
                updateCartBadge(payload.cart?.item_count ?? 0);
                showAddedFeedback(addButton);
            } catch (error) {
                addButton.disabled = false;
                alert(error.message);
            }
        }

        const notifyButton = event.target.closest('[data-notify-me]');

        if (notifyButton) {
            const label = notifyButton.querySelector('[data-action-label]');
            label.textContent = "We'll email you \u2713";
            notifyButton.disabled = true;
        }
    });

    wishlist.querySelector('[data-wishlist-clear]')?.addEventListener('click', async () => {
        if (!window.confirm('Remove all saved items from your wishlist?')) {
            return;
        }

        try {
            await clearWishlist();
            items().forEach((item) => item.remove());
            updateState();
        } catch (error) {
            alert(error.message);
        }
    });

    wishlist.querySelector('[data-wishlist-add-all]')?.addEventListener('click', async () => {
        const button = wishlist.querySelector('[data-wishlist-add-all]');
        button.disabled = true;

        try {
            const payload = await moveAllWishlistToCart();
            updateCartBadge(payload.cart?.item_count ?? 0);
            items().forEach((item) => item.remove());
            updateState();

            if (payload.moved > 0) {
                window.location.href = '/cart';
            } else {
                alert(payload.message);
            }
        } catch (error) {
            alert(error.message);
        } finally {
            button.disabled = false;
        }
    });

    updateState();
};
