import { removeCompareItem } from './compare-api';

const DIFF_CLASSES = ['bg-bronze-50/70'];

export const initComparePage = () => {
    const compare = document.querySelector('[data-compare]');

    if (!compare) {
        return;
    }

    const table = compare.querySelector('[data-compare-table]');
    const scroller = compare.querySelector('[data-compare-scroller]');
    const emptyState = compare.querySelector('[data-compare-empty]');
    const countLabel = compare.querySelector('[data-compare-count]');
    const diffToggle = compare.querySelector('[data-compare-diff-toggle]');

    if (!table) {
        return;
    }

    const remainingColumns = () => [...table.querySelectorAll('thead [data-col]')].map((th) => th.dataset.col);

    const applyDiffHighlight = () => {
        if (!diffToggle) {
            return;
        }

        const highlight = diffToggle.checked;

        table.querySelectorAll('[data-compare-row]').forEach((row) => {
            const cells = [...row.querySelectorAll('[data-compare-value]')];
            const values = cells.map((cell) => cell.textContent.trim().replace(/\s+/g, ' '));
            const differs = new Set(values).size > 1;

            cells.forEach((cell) => {
                DIFF_CLASSES.forEach((cls) => cell.classList.toggle(cls, highlight && differs));
            });
        });
    };

    const updateState = () => {
        const count = remainingColumns().length;

        if (countLabel) {
            countLabel.textContent = `${count} ${count === 1 ? 'product' : 'products'}`;
        }

        const isEmpty = count === 0;

        if (scroller) {
            scroller.hidden = isEmpty;
        }

        if (emptyState) {
            emptyState.hidden = !isEmpty;
        }

        if (diffToggle?.closest('label')) {
            diffToggle.closest('label').hidden = isEmpty;
        }

        applyDiffHighlight();

        if (isEmpty) {
            window.location.reload();
        }
    };

    table.addEventListener('click', async (event) => {
        const removeButton = event.target.closest('[data-compare-remove]');

        if (!removeButton) {
            return;
        }

        const compareItemId = removeButton.dataset.compareItemId;

        if (!compareItemId) {
            return;
        }

        removeButton.disabled = true;

        try {
            await removeCompareItem(Number(compareItemId));

            const column = removeButton.closest('[data-col]').dataset.col;
            table.querySelectorAll(`[data-col="${column}"]`).forEach((cell) => cell.remove());
            updateState();
        } catch (error) {
            alert(error.message);
            removeButton.disabled = false;
        }
    });

    diffToggle?.addEventListener('change', applyDiffHighlight);
};
