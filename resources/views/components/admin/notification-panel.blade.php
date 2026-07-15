<div class="relative">
    <button type="button"
            data-panel-trigger
            aria-controls="admin-notifications"
            aria-expanded="false"
            aria-haspopup="true"
            aria-label="Notifications, 3 unread"
            class="relative inline-flex size-11 items-center justify-center rounded-[var(--radius-admin)] admin-text-secondary admin-focus-ring hover:bg-admin-accent-muted/60">
        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path d="M6 9a6 6 0 0 1 12 0c0 5 2 6 2 6H4s2-1 2-6M10 19a2 2 0 0 0 4 0"/>
        </svg>
        <span class="absolute right-2 top-2 size-2 animate-pulse rounded-full bg-admin-danger" aria-hidden="true"></span>
    </button>

    <div id="admin-notifications"
         data-admin-panel
         hidden
         aria-hidden="true"
         role="complementary"
         aria-label="Notification center"
         class="fixed inset-x-0 bottom-0 z-50 max-h-[80vh] overflow-hidden rounded-t-[var(--radius-admin-lg)] border admin-border admin-surface shadow-xl sm:absolute sm:inset-auto sm:right-0 sm:top-full sm:mt-2 sm:w-96 sm:rounded-[var(--radius-admin-lg)]">
        <div class="flex items-center justify-between border-b admin-border px-4 py-3">
            <h2 class="text-sm font-semibold admin-text">Notifications</h2>
            <button type="button" class="rounded px-2 py-1 text-xs font-medium text-admin-info transition-colors duration-150 admin-focus-ring hover:bg-blue-50 dark:hover:bg-blue-950/30">Mark all read</button>
        </div>
        <div class="admin-scrollbar max-h-80 overflow-y-auto p-2" aria-live="polite">
            <p class="px-2 py-1 text-xs font-semibold uppercase tracking-wide admin-muted">Today</p>
            @foreach ([
                ['title' => 'New order #4821', 'body' => 'Valor Field Jacket — $189.00', 'time' => '12m ago', 'type' => 'order'],
                ['title' => 'Low stock alert', 'body' => 'Heritage Wool Beanie — 4 left', 'time' => '1h ago', 'type' => 'inventory'],
            ] as $notification)
                <button type="button" class="flex w-full gap-3 rounded-[var(--radius-admin)] px-3 py-2.5 text-left transition-colors duration-150 admin-focus-ring hover:bg-admin-accent-muted/60">
                    <span class="mt-1 size-2 shrink-0 rounded-full bg-admin-brand"></span>
                    <span class="min-w-0">
                        <span class="block text-sm font-medium admin-text">{{ $notification['title'] }}</span>
                        <span class="block truncate text-xs admin-muted">{{ $notification['body'] }}</span>
                        <span class="mt-0.5 block text-[11px] admin-muted">{{ $notification['time'] }}</span>
                    </span>
                </button>
            @endforeach
            <p class="px-2 py-2 text-xs font-semibold uppercase tracking-wide admin-muted">Earlier</p>
            <button type="button" class="flex w-full gap-3 rounded-[var(--radius-admin)] px-3 py-2.5 text-left admin-focus-ring hover:bg-admin-accent-muted/60">
                <span class="mt-1 size-2 shrink-0 rounded-full bg-admin-muted"></span>
                <span class="min-w-0">
                    <span class="block text-sm font-medium admin-text">Support ticket resolved</span>
                    <span class="block text-xs admin-muted">Ticket #1198 closed by Alex</span>
                </span>
            </button>
        </div>
    </div>
</div>
