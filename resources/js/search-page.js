const STORAGE_KEY = 'valor-recent-searches';
const MAX_RECENT = 6;

const readRecent = () => {
    try {
        return JSON.parse(localStorage.getItem(STORAGE_KEY)) ?? [];
    } catch {
        return [];
    }
};

const writeRecent = (items) => {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(items.slice(0, MAX_RECENT)));
};

export const initSearchPage = () => {
    const input = document.querySelector('[data-live-search]');
    const resultsGrid = document.querySelector('[data-search-results]');

    if (!input || !resultsGrid) {
        return;
    }

    const cards = [...resultsGrid.querySelectorAll('[data-result-name]')];
    const countEl = document.querySelector('[data-results-count]');
    const emptyState = document.querySelector('[data-search-empty]');
    const emptyQuery = document.querySelector('[data-empty-query]');
    const clearButton = document.querySelector('[data-search-clear]');
    const filterChips = [...document.querySelectorAll('[data-live-filter]')];
    const recentBlock = document.querySelector('[data-recent-searches-block]');
    const recentList = document.querySelector('[data-recent-searches]');

    let activeCategory = 'all';

    /* ---- Live filtering ---- */

    const applyFilters = () => {
        const query = input.value.trim().toLowerCase();
        let visible = 0;

        cards.forEach((card) => {
            const matchesQuery = query === '' || card.dataset.resultName.includes(query);
            const matchesCategory = activeCategory === 'all' || card.dataset.resultCategory === activeCategory;
            const show = matchesQuery && matchesCategory;
            card.hidden = !show;

            if (show) {
                visible += 1;
            }
        });

        countEl.innerHTML = `Showing <span class="font-semibold text-navy-900">${visible}</span> ${visible === 1 ? 'result' : 'results'}`;
        clearButton.hidden = query === '';

        const isEmpty = visible === 0;
        resultsGrid.hidden = isEmpty;
        emptyState.hidden = !isEmpty;

        if (isEmpty) {
            emptyQuery.textContent = input.value.trim();
        }
    };

    input.addEventListener('input', applyFilters);

    clearButton.addEventListener('click', () => {
        input.value = '';
        applyFilters();
        input.focus();
    });

    document.querySelector('[data-search-reset]')?.addEventListener('click', () => {
        input.value = '';
        setCategory('all');
        input.focus();
    });

    /* ---- Category chips ---- */

    const setCategory = (category) => {
        activeCategory = category;
        filterChips.forEach((chip) => {
            chip.setAttribute('aria-pressed', String(chip.dataset.liveFilter === category));
        });
        applyFilters();
    };

    filterChips.forEach((chip) => {
        chip.addEventListener('click', () => setCategory(chip.dataset.liveFilter));
    });

    /* ---- Recent searches ---- */

    const renderRecent = () => {
        const items = readRecent();
        recentBlock.hidden = items.length === 0;
        recentList.innerHTML = '';

        items.forEach((term) => {
            const li = document.createElement('li');
            li.className = 'flex items-center overflow-hidden rounded-full border border-navy-200 bg-surface';

            const search = document.createElement('button');
            search.type = 'button';
            search.className = 'py-1.5 pl-3.5 text-sm font-medium text-navy-700 transition-colors duration-200 hover:text-navy-900';
            search.textContent = term;
            search.addEventListener('click', () => {
                input.value = term;
                applyFilters();
                input.focus();
            });

            const remove = document.createElement('button');
            remove.type = 'button';
            remove.setAttribute('aria-label', `Remove "${term}" from recent searches`);
            remove.className = 'flex size-7 items-center justify-center px-4 text-navy-400 transition-colors duration-200 hover:text-red-600';
            remove.innerHTML = '<svg class="size-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg>';
            remove.addEventListener('click', () => {
                writeRecent(readRecent().filter((item) => item !== term));
                renderRecent();
            });

            li.append(search, remove);
            recentList.append(li);
        });
    };

    const saveRecent = (term) => {
        const normalized = term.trim();

        if (normalized === '') {
            return;
        }

        writeRecent([normalized, ...readRecent().filter((item) => item !== normalized)]);
        renderRecent();
    };

    document.querySelector('[data-recent-clear]')?.addEventListener('click', () => {
        writeRecent([]);
        renderRecent();
    });

    /* ---- Suggestions & popular searches ---- */

    document.querySelectorAll('[data-search-suggestion]').forEach((button) => {
        button.addEventListener('click', () => {
            input.value = button.dataset.searchSuggestion;
            applyFilters();
            saveRecent(input.value);
            input.focus();
        });
    });

    /* ---- Form submit: filter in place, remember the term ---- */

    document.querySelector('[data-search-form]')?.addEventListener('submit', (event) => {
        event.preventDefault();
        applyFilters();
        saveRecent(input.value);
    });

    /* ---- Initial state (supports ?q= links from the header search) ---- */

    const initialQuery = new URLSearchParams(window.location.search).get('q');

    if (initialQuery) {
        input.value = initialQuery;
        saveRecent(initialQuery);
    }

    renderRecent();
    applyFilters();
};
