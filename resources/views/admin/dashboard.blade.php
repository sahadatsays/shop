<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    @php
        $hiddenWidgets = $widgets->reject(fn ($widget) => $widget->visible)->values();
    @endphp

    <x-admin.page-header
        title="Dashboard"
        description="Business performance overview — dynamically composed from your permitted widgets."
    >
        <x-slot:actions>
            <x-admin.dashboard.date-filter :range="$range" :options="$rangeOptions" :from="$from" :to="$to" />
            <x-admin.button variant="secondary" size="sm" data-dashboard-edit-toggle>
                <svg class="mr-1.5 size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                <span data-edit-label>Customize</span>
            </x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <div
        data-admin-dashboard
        data-edit="false"
        data-widget-endpoint="{{ url('admin/dashboard/widgets') }}"
        data-preferences-url="{{ route('admin.dashboard.preferences.save') }}"
        data-reset-url="{{ route('admin.dashboard.preferences.reset') }}"
        data-range="{{ $range->value }}"
        @if ($from) data-from="{{ $from }}" @endif
        @if ($to) data-to="{{ $to }}" @endif
    >
        <div data-dashboard-edit-bar class="mb-5 hidden rounded-[var(--radius-admin-lg)] border border-dashed admin-border bg-admin-accent-muted/40 p-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <p class="text-sm admin-text-secondary">
                    <span class="font-semibold admin-text">Customize mode.</span>
                    Drag cards by the handle to reorder, pin, collapse, or hide them. Your layout is saved automatically.
                </p>
                <button type="button" data-dashboard-reset class="admin-focus-ring inline-flex items-center gap-1.5 rounded-[var(--radius-admin)] border admin-border admin-surface px-3 py-1.5 text-sm font-medium admin-text hover:bg-admin-accent-muted/60">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M21 12a9 9 0 1 1-3-6.7M21 3v5h-5"/></svg>
                    Reset layout
                </button>
            </div>

            <div data-hidden-widgets @class(['mt-3 flex flex-wrap gap-2 border-t admin-border pt-3', 'hidden' => $hiddenWidgets->isEmpty()])>
                <span class="self-center text-xs font-medium admin-muted">Hidden:</span>
                @foreach ($hiddenWidgets as $hidden)
                    <button
                        type="button"
                        data-widget-action="show"
                        data-widget-key="{{ $hidden->key() }}"
                        class="admin-focus-ring inline-flex items-center gap-1.5 rounded-full border admin-border admin-surface px-3 py-1 text-xs font-medium admin-text hover:border-admin-brand/50"
                    >
                        <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                        {{ $hidden->name() }}
                    </button>
                @endforeach
            </div>
        </div>

        <div class="admin-dashboard-grid" data-widget-grid>
            @forelse ($widgets as $widget)
                <x-admin.dashboard.widget-shell :widget="$widget" />
            @empty
                <div style="--w: 12; --w-md: 12;" data-widget>
                    <x-admin.empty-state
                        title="No widgets to display"
                        description="You don't have any dashboard widgets enabled for your role yet."
                    />
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.admin>
