export const initOrdersPage = () => {
    const page = document.querySelector('[data-orders]');

    if (!page) {
        return;
    }

    const filters = [...page.querySelectorAll('[data-orders-filter]')];
    const cards = [...page.querySelectorAll('[data-order-card]')];
    const countLabel = page.querySelector('[data-orders-count]');
    const emptyState = page.querySelector('[data-orders-empty]');

    filters.forEach((button) => {
        button.addEventListener('click', () => {
            const status = button.dataset.ordersFilter;

            filters.forEach((sibling) => sibling.setAttribute('aria-pressed', String(sibling === button)));

            let visible = 0;

            cards.forEach((card) => {
                const show = status === 'All' || card.dataset.status === status;
                card.hidden = !show;
                visible += show ? 1 : 0;
            });

            countLabel.textContent = `${visible} ${visible === 1 ? 'order' : 'orders'}`;
            emptyState.hidden = visible > 0;
        });
    });
};
