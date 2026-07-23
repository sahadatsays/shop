import { addToCart } from './cart-api';
import {
    clearWishlist,
    moveAllWishlistToCart,
    moveWishlistItemToCart,
    removeWishlistItem,
} from './wishlist-api';
import { storeToast } from './store-toast';

const REMOVE_ANIMATION_MS = 300;

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
            const payload = await removeWishlistItem(wishlistItemId);
            storeToast.push({ title: payload.message ?? 'Removed from wishlist.', type: 'info' });
        } catch (error) {
            item.classList.remove('opacity-0', 'scale-95');
            storeToast.error(error.message ?? 'Unable to remove item.');
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
                storeToast.success(payload.message ?? 'Moved to cart.');
                removeItemElement(moveButton.closest('[data-wishlist-item]'));
            } catch (error) {
                moveButton.disabled = false;
                storeToast.error(error.message ?? 'Unable to move item to cart.');
            }

            return;
        }

        const addButton = event.target.closest('[data-add-to-cart]');

        if (addButton) {
            event.preventDefault();
            addButton.disabled = true;

            try {
                const payload = await addToCart(Number(addButton.dataset.productId), 1);
                storeToast.success(payload.message ?? 'Added to cart.');
            } catch (error) {
                storeToast.error(error.message ?? 'Unable to add item to cart.');
            } finally {
                addButton.disabled = false;
            }
        }

        const notifyButton = event.target.closest('[data-notify-me]');

        if (notifyButton) {
            const label = notifyButton.querySelector('[data-action-label]');
            label.textContent = "We'll email you \u2713";
            notifyButton.disabled = true;
            storeToast.success("We'll email you when this item is back in stock.");
        }
    });

    wishlist.querySelector('[data-wishlist-clear]')?.addEventListener('click', async () => {
        if (!window.confirm('Remove all saved items from your wishlist?')) {
            return;
        }

        try {
            const payload = await clearWishlist();
            items().forEach((item) => item.remove());
            updateState();
            storeToast.push({ title: payload.message ?? 'Wishlist cleared.', type: 'info' });
        } catch (error) {
            storeToast.error(error.message ?? 'Unable to clear wishlist.');
        }
    });

    wishlist.querySelector('[data-wishlist-add-all]')?.addEventListener('click', async () => {
        const button = wishlist.querySelector('[data-wishlist-add-all]');
        button.disabled = true;

        try {
            const payload = await moveAllWishlistToCart();
            items().forEach((item) => item.remove());
            updateState();

            if (payload.moved > 0) {
                storeToast.success(payload.message ?? 'Items moved to cart.');
                window.location.href = '/cart';
            } else {
                storeToast.warning(payload.message ?? 'No in-stock items were available to move.');
            }
        } catch (error) {
            storeToast.error(error.message ?? 'Unable to move items to cart.');
        } finally {
            button.disabled = false;
        }
    });

    updateState();
};
