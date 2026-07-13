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

    const remainingColumns = () => [...table.querySelectorAll('thead [data-col]')].map((th) => th.dataset.col);

    const applyDiffHighlight = () => {
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
        countLabel.textContent = `${count} ${count === 1 ? 'product' : 'products'}`;

        const isEmpty = count === 0;
        scroller.hidden = isEmpty;
        emptyState.hidden = !isEmpty;
        diffToggle.closest('label').hidden = isEmpty;
        applyDiffHighlight();
    };

    table.addEventListener('click', (event) => {
        const removeButton = event.target.closest('[data-compare-remove]');

        if (!removeButton) {
            return;
        }

        const column = removeButton.closest('[data-col]').dataset.col;
        table.querySelectorAll(`[data-col="${column}"]`).forEach((cell) => cell.remove());
        updateState();
    });

    diffToggle.addEventListener('change', applyDiffHighlight);
};
