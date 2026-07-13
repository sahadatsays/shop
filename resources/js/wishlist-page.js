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

    const removeItem = (item) => {
        item.classList.add('opacity-0', 'scale-95');

        setTimeout(() => {
            item.remove();
            updateState();
        }, REMOVE_ANIMATION_MS);
    };

    grid.addEventListener('click', (event) => {
        const removeButton = event.target.closest('[data-wishlist-remove]');

        if (removeButton) {
            removeItem(removeButton.closest('[data-wishlist-item]'));
            return;
        }

        const addButton = event.target.closest('[data-add-to-cart]');

        if (addButton) {
            showAddedFeedback(addButton);
            return;
        }

        const notifyButton = event.target.closest('[data-notify-me]');

        if (notifyButton) {
            const label = notifyButton.querySelector('[data-action-label]');
            label.textContent = "We'll email you \u2713";
            notifyButton.disabled = true;
        }
    });

    wishlist.querySelector('[data-wishlist-clear]')?.addEventListener('click', () => {
        items().forEach(removeItem);
    });

    wishlist.querySelector('[data-wishlist-add-all]')?.addEventListener('click', () => {
        grid.querySelectorAll('[data-add-to-cart]').forEach(showAddedFeedback);
    });
};
