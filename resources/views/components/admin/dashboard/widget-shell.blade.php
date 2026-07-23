@props([
    'widget',
])

@php
    $columns = (int) ($widget->width ?? 6);
    $tabletSpan = $columns <= 6 ? 6 : 12;
    $key = $widget->key();
    $refresh = $widget->refreshInterval();
@endphp

<section
    data-widget
    data-widget-key="{{ $key }}"
    data-position="{{ $widget->position }}"
    data-collapsed="{{ $widget->collapsed ? 'true' : 'false' }}"
    data-pinned="{{ $widget->pinned ? 'true' : 'false' }}"
    data-has-provider="{{ $widget->hasProvider ? 'true' : 'false' }}"
    data-widget-type="{{ $widget->type()->value }}"
    data-visible="{{ $widget->visible ? 'true' : 'false' }}"
    @if ($refresh) data-refresh="{{ $refresh }}" @endif
    style="--w: {{ $columns }}; --w-md: {{ $tabletSpan }};"
    @class([
        'admin-fade-up admin-card-interactive rounded-[var(--radius-admin-lg)] border admin-border admin-surface',
        'hidden' => ! $widget->visible,
    ])
    aria-label="{{ $widget->name() }} widget"
>
    <header class="flex items-center gap-2 border-b admin-border px-4 py-3">
        <button
            type="button"
            data-widget-handle
            class="admin-focus-ring -ml-1 flex size-7 items-center justify-center rounded-[var(--radius-admin)] admin-muted hover:admin-text"
            aria-label="Drag to reorder"
            tabindex="-1"
        >
            <svg class="size-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="9" cy="6" r="1.6"/><circle cx="15" cy="6" r="1.6"/><circle cx="9" cy="12" r="1.6"/><circle cx="15" cy="12" r="1.6"/><circle cx="9" cy="18" r="1.6"/><circle cx="15" cy="18" r="1.6"/></svg>
        </button>

        @if ($widget->widget->icon)
            <span class="flex size-7 items-center justify-center rounded-[var(--radius-admin)] bg-admin-accent-muted text-admin-brand">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="{{ $widget->widget->icon }}"/></svg>
            </span>
        @endif

        <h2 class="truncate text-sm font-semibold admin-text">{{ $widget->name() }}</h2>

        <div class="ml-auto flex items-center gap-0.5">
            @if ($widget->hasProvider)
                <button type="button" data-widget-action="refresh" class="admin-focus-ring flex size-7 items-center justify-center rounded-[var(--radius-admin)] admin-muted hover:admin-text hover:bg-admin-accent-muted/60" aria-label="Refresh widget" title="Refresh">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M21 12a9 9 0 1 1-3-6.7M21 3v5h-5"/></svg>
                </button>
            @endif

            <button type="button" data-widget-action="pin" aria-pressed="{{ $widget->pinned ? 'true' : 'false' }}" @class(['admin-focus-ring flex size-7 items-center justify-center rounded-[var(--radius-admin)] hover:bg-admin-accent-muted/60', 'text-admin-brand' => $widget->pinned, 'admin-muted hover:admin-text' => ! $widget->pinned]) aria-label="Pin widget" title="Pin">
                <svg class="size-4" viewBox="0 0 24 24" fill="{{ $widget->pinned ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M12 17v5M9 3h6l-1 7 3 3H7l3-3-1-7Z"/></svg>
            </button>

            <button type="button" data-widget-action="collapse" aria-expanded="{{ $widget->collapsed ? 'false' : 'true' }}" class="admin-focus-ring flex size-7 items-center justify-center rounded-[var(--radius-admin)] admin-muted hover:admin-text hover:bg-admin-accent-muted/60" aria-label="Collapse widget" title="Collapse">
                <svg class="size-4 transition-transform duration-200" data-widget-chevron viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
            </button>

            <button type="button" data-widget-action="hide" class="admin-focus-ring flex size-7 items-center justify-center rounded-[var(--radius-admin)] admin-muted hover:text-admin-danger hover:bg-admin-accent-muted/60" aria-label="Hide widget" title="Hide">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M9.9 4.24A9.1 9.1 0 0 1 12 4c7 0 10 8 10 8a13 13 0 0 1-1.67 2.68M6.6 6.6C3.6 8.3 2 12 2 12s3 8 10 8a9.3 9.3 0 0 0 5.4-1.6M1 1l22 22M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>
            </button>
        </div>
    </header>

    <div data-widget-body class="p-5">
        @if (! $widget->hasProvider)
            <x-admin.empty-state
                title="Widget unavailable"
                description="No provider is registered for this widget key yet."
            />
        @else
            <div class="space-y-3" aria-hidden="true">
                <div class="admin-widget-skeleton h-4 w-1/3"></div>
                <div class="admin-widget-skeleton h-24 w-full"></div>
                <div class="admin-widget-skeleton h-4 w-2/3"></div>
            </div>
        @endif
    </div>
</section>
