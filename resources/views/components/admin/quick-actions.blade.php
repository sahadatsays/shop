@props([
    'actions' => [],
])

<div class="relative">
    <button type="button"
            data-panel-trigger
            aria-controls="admin-quick-actions"
            aria-expanded="false"
            aria-haspopup="true"
            aria-label="Quick actions"
            class="inline-flex size-11 items-center justify-center rounded-[var(--radius-admin)] admin-text-secondary admin-focus-ring hover:bg-admin-accent-muted/60">
        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path d="M12 5v14M5 12h14"/>
        </svg>
    </button>

    <div id="admin-quick-actions"
         data-admin-panel
         hidden
         aria-hidden="true"
         role="menu"
         aria-label="Quick actions"
         class="absolute right-0 top-full z-50 mt-2 w-56 rounded-[var(--radius-admin-lg)] border admin-border admin-surface p-1.5 shadow-lg">
        @foreach ($actions as $action)
            @if ($action['href'])
                <a href="{{ $action['href'] }}" role="menuitem" class="flex items-center gap-2 rounded-[var(--radius-admin)] px-3 py-2 text-sm transition-colors duration-150 admin-text-secondary admin-focus-ring hover:bg-admin-accent-muted/60 hover:admin-text">
                    <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="{{ $action['icon'] }}"/></svg>
                    {{ $action['label'] }}
                </a>
            @else
                <button type="button" role="menuitem" data-palette-open class="flex w-full items-center gap-2 rounded-[var(--radius-admin)] px-3 py-2 text-left text-sm transition-colors duration-150 admin-text-secondary admin-focus-ring hover:bg-admin-accent-muted/60 hover:admin-text">
                    <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="{{ $action['icon'] }}"/></svg>
                    {{ $action['label'] }}
                </button>
            @endif
        @endforeach
    </div>
</div>
