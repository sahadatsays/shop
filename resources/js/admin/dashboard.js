import ApexCharts from 'apexcharts';
import Alpine from 'alpinejs';

const chartColors = ['#b08968', '#1c2941', '#517b69', '#4f6485', '#9a7355', '#3a4c6b'];

const isDark = () => document.documentElement.classList.contains('dark');

const baseChartOptions = () => {
    const dark = isDark();

    return {
        chart: {
            fontFamily: 'Inter, ui-sans-serif, system-ui, sans-serif',
            toolbar: { show: false },
            animations: { enabled: true, easing: 'easeinout', speed: 600 },
            background: 'transparent',
        },
        theme: { mode: dark ? 'dark' : 'light' },
        grid: { borderColor: dark ? '#1f2937' : '#e2e8f0', strokeDashArray: 4 },
        xaxis: {
            labels: { style: { colors: dark ? '#94a3b8' : '#64748b' } },
            axisBorder: { show: false },
            axisTicks: { show: false },
        },
        yaxis: { labels: { style: { colors: dark ? '#94a3b8' : '#64748b' } } },
        tooltip: { theme: dark ? 'dark' : 'light' },
        colors: chartColors,
        stroke: { curve: 'smooth', width: 2 },
        dataLabels: { enabled: false },
    };
};

const buildOptions = (chart) => {
    const base = baseChartOptions();

    if (chart.type === 'donut') {
        return {
            ...base,
            chart: { ...base.chart, type: 'donut' },
            labels: chart.labels,
            series: chart.series[0]?.data ?? [],
            legend: { position: 'bottom', labels: { colors: isDark() ? '#cbd5e1' : '#475569' } },
            plotOptions: {
                pie: {
                    donut: {
                        size: '68%',
                        labels: {
                            show: true,
                            total: { show: true, label: chart.title, color: isDark() ? '#f1f5f9' : '#0f172a' },
                        },
                    },
                },
            },
        };
    }

    return {
        ...base,
        chart: { ...base.chart, type: chart.type === 'area' ? 'area' : chart.type },
        series: chart.series,
        xaxis: { ...base.xaxis, categories: chart.labels },
        fill: chart.type === 'area'
            ? { type: 'gradient', gradient: { shadeIntensity: 0.3, opacityFrom: 0.45, opacityTo: 0.05, stops: [0, 90, 100] } }
            : undefined,
    };
};

/**
 * Instantiate any ApexCharts declared inside a freshly injected widget body.
 */
const renderChartsWithin = (root) => {
    root.querySelectorAll('[data-widget-chart]').forEach((node) => {
        let payload;

        try {
            payload = JSON.parse(node.getAttribute('data-widget-chart'));
        } catch (error) {
            return;
        }

        const element = document.getElementById(payload.id);

        if (! element) {
            return;
        }

        if (element.__apex) {
            element.__apex.destroy();
        }

        const instance = new ApexCharts(element, buildOptions(payload));
        instance.render();
        element.__apex = instance;
    });
};

/**
 * Kept for backward compatibility with any legacy Alpine usage. Charts are now
 * rendered by the async dashboard engine, so these are lightweight shims.
 */
export const registerDashboardAlpine = () => {
    Alpine.data('dashboardCharts', () => ({ init() {}, charts: [], instances: [] }));
    Alpine.data('dashboardQuickAction', () => ({
        run(action) {
            if (action.href) {
                window.location.href = action.href;
                return;
            }
            window.adminToast?.push({ title: action.label, message: action.description ?? 'Coming soon.', type: 'info' });
        },
    }));
};

export const initAdminDashboard = () => {
    const root = document.querySelector('[data-admin-dashboard]');

    if (! root) {
        return;
    }

    const grid = root.querySelector('[data-widget-grid]');
    const endpoint = root.dataset.widgetEndpoint;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    const rangeQuery = () => {
        const params = new URLSearchParams({ range: root.dataset.range || 'last_30_days' });
        if (root.dataset.from) params.set('from', root.dataset.from);
        if (root.dataset.to) params.set('to', root.dataset.to);
        return params.toString();
    };

    const loadWidget = async (widgetEl, force = false) => {
        if (widgetEl.dataset.hasProvider !== 'true') return;
        if (widgetEl.dataset.loaded === 'true' && ! force) return;

        const key = widgetEl.dataset.widgetKey;
        const body = widgetEl.querySelector('[data-widget-body]');
        if (! body) return;

        widgetEl.dataset.loading = 'true';

        try {
            const response = await fetch(`${endpoint}/${key}?${rangeQuery()}`, {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });

            if (! response.ok) throw new Error(`HTTP ${response.status}`);

            const data = await response.json();
            body.innerHTML = data.html;
            widgetEl.dataset.loaded = 'true';
            renderChartsWithin(body);
        } catch (error) {
            body.innerHTML = '<p class="text-sm text-admin-danger">Unable to load this widget. Try refreshing.</p>';
        } finally {
            widgetEl.dataset.loading = 'false';
        }
    };

    // ---- Lazy loading -------------------------------------------------------
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                loadWidget(entry.target);
                observer.unobserve(entry.target);
            }
        });
    }, { rootMargin: '200px' });

    grid.querySelectorAll('[data-widget]').forEach((el) => {
        if (el.dataset.visible !== 'false') {
            observer.observe(el);
        }
    });

    // ---- Auto refresh -------------------------------------------------------
    grid.querySelectorAll('[data-widget][data-refresh]').forEach((el) => {
        const seconds = parseInt(el.dataset.refresh, 10);
        if (! seconds || seconds <= 0) return;

        window.setInterval(() => {
            if (el.dataset.loaded === 'true' && el.dataset.collapsed !== 'true' && el.dataset.visible !== 'false') {
                loadWidget(el, true);
            }
        }, seconds * 1000);
    });

    // ---- Preferences persistence -------------------------------------------
    const savePreferences = (items) => {
        fetch(root.dataset.preferencesUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ widgets: items }),
        }).catch(() => {});
    };

    const savePref = (key, attributes) => savePreferences([{ key, ...attributes }]);

    const persistPositions = () => {
        const items = [...grid.querySelectorAll('[data-widget][data-visible="true"]')].map((el, index) => ({
            key: el.dataset.widgetKey,
            position: index * 10,
        }));
        if (items.length) savePreferences(items);
    };

    // ---- Hidden widgets tray ------------------------------------------------
    const editBar = root.querySelector('[data-dashboard-edit-bar]');
    const hiddenTray = root.querySelector('[data-hidden-widgets]');

    const addHiddenChip = (key, name) => {
        if (! hiddenTray) return;
        if (hiddenTray.querySelector(`[data-widget-key="${key}"]`)) return;

        const chip = document.createElement('button');
        chip.type = 'button';
        chip.dataset.widgetAction = 'show';
        chip.dataset.widgetKey = key;
        chip.className = 'admin-focus-ring inline-flex items-center gap-1.5 rounded-full border admin-border admin-surface px-3 py-1 text-xs font-medium admin-text hover:border-admin-brand/50';
        chip.innerHTML = `<svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>${name}`;
        hiddenTray.appendChild(chip);
        hiddenTray.classList.remove('hidden');
    };

    const removeHiddenChip = (key) => {
        const chip = hiddenTray?.querySelector(`[data-widget-action="show"][data-widget-key="${key}"]`);
        chip?.remove();
        if (hiddenTray && hiddenTray.querySelectorAll('[data-widget-action="show"]').length === 0) {
            hiddenTray.classList.add('hidden');
        }
    };

    const showWidget = (key) => {
        const widgetEl = grid.querySelector(`[data-widget][data-widget-key="${key}"]`);
        if (! widgetEl) return;
        widgetEl.classList.remove('hidden');
        widgetEl.dataset.visible = 'true';
        removeHiddenChip(key);
        loadWidget(widgetEl);
        savePref(key, { is_visible: true });
        persistPositions();
    };

    // ---- Action delegation --------------------------------------------------
    root.addEventListener('click', (event) => {
        const button = event.target.closest('[data-widget-action]');
        if (! button || ! root.contains(button)) return;

        const action = button.dataset.widgetAction;

        if (action === 'show') {
            showWidget(button.dataset.widgetKey);
            return;
        }

        const widgetEl = button.closest('[data-widget]');
        if (! widgetEl) return;
        const key = widgetEl.dataset.widgetKey;

        if (action === 'collapse') {
            const collapsed = widgetEl.dataset.collapsed !== 'true';
            widgetEl.dataset.collapsed = collapsed ? 'true' : 'false';
            button.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
            const chevron = widgetEl.querySelector('[data-widget-chevron]');
            if (chevron) chevron.style.transform = collapsed ? 'rotate(-90deg)' : '';
            if (! collapsed && widgetEl.dataset.loaded !== 'true') loadWidget(widgetEl);
            savePref(key, { is_collapsed: collapsed });
        } else if (action === 'pin') {
            const pinned = widgetEl.dataset.pinned !== 'true';
            widgetEl.dataset.pinned = pinned ? 'true' : 'false';
            button.setAttribute('aria-pressed', pinned ? 'true' : 'false');
            if (pinned) grid.prepend(widgetEl);
            savePref(key, { is_pinned: pinned });
            persistPositions();
        } else if (action === 'hide') {
            const name = widgetEl.querySelector('h2')?.textContent?.trim() ?? key;
            widgetEl.classList.add('hidden');
            widgetEl.dataset.visible = 'false';
            addHiddenChip(key, name);
            savePref(key, { is_visible: false });
        } else if (action === 'refresh') {
            loadWidget(widgetEl, true);
        }
    });

    // ---- Quick actions with no destination ---------------------------------
    root.addEventListener('click', (event) => {
        const quick = event.target.closest('[data-quick-action]');
        if (! quick) return;
        window.adminToast?.push({ title: quick.dataset.quickAction, message: 'This module is coming soon.', type: 'info' });
    });

    // ---- Edit mode ----------------------------------------------------------
    const editToggle = document.querySelector('[data-dashboard-edit-toggle]');
    editToggle?.addEventListener('click', () => {
        const on = root.dataset.edit !== 'true';
        root.dataset.edit = on ? 'true' : 'false';
        editBar?.classList.toggle('hidden', ! on);
        const label = editToggle.querySelector('[data-edit-label]');
        if (label) label.textContent = on ? 'Done' : 'Customize';
        grid.querySelectorAll('[data-widget]').forEach((el) => el.setAttribute('draggable', on ? 'true' : 'false'));
    });

    // ---- Reset --------------------------------------------------------------
    root.querySelector('[data-dashboard-reset]')?.addEventListener('click', async () => {
        try {
            await fetch(root.dataset.resetUrl, {
                method: 'POST',
                headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
            });
            window.location.reload();
        } catch (error) {
            window.adminToast?.push({ title: 'Reset failed', type: 'danger' });
        }
    });

    // ---- Drag & drop reorder -----------------------------------------------
    let dragged = null;

    grid.addEventListener('dragstart', (event) => {
        const widgetEl = event.target.closest('[data-widget]');
        if (! widgetEl || root.dataset.edit !== 'true') return;
        dragged = widgetEl;
        widgetEl.classList.add('is-dragging');
        event.dataTransfer.effectAllowed = 'move';
    });

    grid.addEventListener('dragend', () => {
        if (! dragged) return;
        dragged.classList.remove('is-dragging');
        grid.querySelectorAll('.is-drop-target').forEach((el) => el.classList.remove('is-drop-target'));
        dragged = null;
    });

    grid.addEventListener('dragover', (event) => {
        if (! dragged) return;
        event.preventDefault();
        const target = event.target.closest('[data-widget]');
        grid.querySelectorAll('.is-drop-target').forEach((el) => el.classList.remove('is-drop-target'));
        if (target && target !== dragged) target.classList.add('is-drop-target');
    });

    grid.addEventListener('drop', (event) => {
        if (! dragged) return;
        event.preventDefault();
        const target = event.target.closest('[data-widget]');
        if (! target || target === dragged) return;

        const widgets = [...grid.querySelectorAll('[data-widget]')];
        const draggedIndex = widgets.indexOf(dragged);
        const targetIndex = widgets.indexOf(target);

        if (draggedIndex < targetIndex) {
            target.after(dragged);
        } else {
            target.before(dragged);
        }

        target.classList.remove('is-drop-target');
        persistPositions();
    });

    // ---- Date filter custom range toggle -----------------------------------
    const dateFilter = document.querySelector('[data-dashboard-date-filter]');
    if (dateFilter) {
        const select = dateFilter.querySelector('[data-range-select]');
        const custom = dateFilter.querySelector('[data-custom-range]');
        select?.addEventListener('change', () => {
            if (select.value === 'custom') {
                custom?.classList.remove('hidden');
            } else {
                dateFilter.requestSubmit ? dateFilter.requestSubmit() : dateFilter.submit();
            }
        });
    }
};

export { Alpine };
