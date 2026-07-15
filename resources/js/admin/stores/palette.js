let paletteEl = null;
let inputEl = null;
let resultsEl = null;
let allItems = [];
let filteredItems = [];
let activeIndex = 0;

const flattenItems = () => {
    const groups = window.__adminPaletteGroups ?? [];
    allItems = [];

    groups.forEach((group) => {
        group.items.forEach((item) => {
            allItems.push({ ...item, group: group.group });
        });
    });

    filteredItems = [...allItems];
};

const filterItems = (query = '') => {
    const normalized = query.trim().toLowerCase();

    if (!normalized) {
        filteredItems = [...allItems];
        return;
    }

    filteredItems = allItems.filter(
        (item) =>
            item.label.toLowerCase().includes(normalized) ||
            item.keywords.toLowerCase().includes(normalized) ||
            item.group.toLowerCase().includes(normalized),
    );
};

const renderResults = () => {
    if (!resultsEl) {
        return;
    }

    if (filteredItems.length === 0) {
        resultsEl.innerHTML = '<p class="px-3 py-6 text-center text-sm admin-muted">No results found.</p>';
        return;
    }

    let currentGroup = '';
    let html = '';

    filteredItems.forEach((item, index) => {
        if (item.group !== currentGroup) {
            currentGroup = item.group;
            html += `<p class="px-3 pb-1 pt-3 text-xs font-semibold uppercase tracking-wide admin-muted">${currentGroup}</p>`;
        }

        const active = index === activeIndex;
        html += `
            <button type="button" role="option" aria-selected="${active}"
                    data-palette-index="${index}"
                    class="flex w-full items-center justify-between rounded-[var(--radius-admin)] px-3 py-2 text-left text-sm transition-colors duration-100 admin-focus-ring ${active ? 'bg-admin-accent-muted admin-text' : 'admin-text-secondary hover:bg-admin-accent-muted/60'}">
                <span>${item.label}</span>
                ${item.href ? '<span class="text-xs admin-muted">↵</span>' : '<span class="text-xs admin-muted">Soon</span>'}
            </button>
        `;
    });

    resultsEl.innerHTML = html;

    resultsEl.querySelectorAll('[data-palette-index]').forEach((button) => {
        button.addEventListener('click', () => {
            selectItem(Number(button.getAttribute('data-palette-index')));
        });
    });
};

const openPalette = () => {
    if (!paletteEl) {
        return;
    }

    flattenItems();
    activeIndex = 0;

    paletteEl.hidden = false;
    document.body.style.overflow = 'hidden';

    requestAnimationFrame(() => {
        paletteEl.classList.add('is-open');
        inputEl.value = '';
        filterItems('');
        renderResults();
        inputEl?.focus();
    });
};

const closePalette = () => {
    if (!paletteEl) {
        return;
    }

    paletteEl.classList.remove('is-open');
    document.body.style.overflow = '';

    window.setTimeout(() => {
        paletteEl.hidden = true;
    }, 220);
};

const selectItem = (index) => {
    const item = filteredItems[index];

    if (!item) {
        return;
    }

    if (item.href) {
        window.location.href = item.href;
        return;
    }

    closePalette();
    window.adminToast?.push({
        title: item.label,
        message: 'This module is coming soon.',
        type: 'info',
    });
};

export const initAdminPalette = () => {
    paletteEl = document.querySelector('[data-command-palette]');
    inputEl = document.querySelector('[data-palette-input]');
    resultsEl = document.querySelector('[data-palette-results]');

    document.querySelectorAll('[data-palette-open]').forEach((trigger) => {
        trigger.addEventListener('click', openPalette);
    });

    paletteEl?.querySelector('[data-palette-backdrop]')?.addEventListener('click', closePalette);
    paletteEl?.querySelector('[data-palette-close]')?.addEventListener('click', closePalette);

    inputEl?.addEventListener('input', (event) => {
        activeIndex = 0;
        filterItems(event.target.value);
        renderResults();
    });

    document.addEventListener('keydown', (event) => {
        if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') {
            event.preventDefault();
            openPalette();
            return;
        }

        if (paletteEl?.hidden) {
            return;
        }

        if (event.key === 'Escape') {
            closePalette();
        } else if (event.key === 'ArrowDown') {
            event.preventDefault();
            activeIndex = Math.min(activeIndex + 1, filteredItems.length - 1);
            renderResults();
        } else if (event.key === 'ArrowUp') {
            event.preventDefault();
            activeIndex = Math.max(activeIndex - 1, 0);
            renderResults();
        } else if (event.key === 'Enter') {
            event.preventDefault();
            selectItem(activeIndex);
        }
    });
};
