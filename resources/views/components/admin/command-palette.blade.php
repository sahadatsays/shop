<div data-command-palette hidden role="dialog" aria-modal="true" aria-label="Command palette" class="fixed inset-0 z-[70]">
    <div data-palette-backdrop class="admin-palette-backdrop absolute inset-0 bg-black/40"></div>
    <div class="admin-palette-container relative mx-auto mt-[10vh] w-full max-w-xl px-4">
        <div class="overflow-hidden rounded-[var(--radius-admin-lg)] border admin-border admin-surface shadow-2xl">
            <div class="flex items-center gap-2 border-b admin-border px-4">
                <svg class="size-4 admin-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <circle cx="11" cy="11" r="7"/><path d="m20 20-3-3"/>
                </svg>
                <input type="search"
                       data-palette-input
                       role="combobox"
                       aria-expanded="true"
                       aria-controls="palette-results"
                       aria-autocomplete="list"
                       placeholder="Search pages, actions, records…"
                       class="h-12 w-full bg-transparent text-sm admin-text placeholder:admin-muted focus:outline-none">
                <button type="button" data-palette-close class="rounded p-1 admin-muted transition-colors duration-150 admin-focus-ring hover:admin-text" aria-label="Close command palette">
                    <span class="rounded border admin-border px-1.5 py-0.5 text-[10px]">Esc</span>
                </button>
            </div>
            <div id="palette-results" data-palette-results role="listbox" class="admin-scrollbar max-h-80 overflow-y-auto p-2"></div>
        </div>
    </div>
</div>
