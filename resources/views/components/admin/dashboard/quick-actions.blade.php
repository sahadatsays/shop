@props([
    'actions' => [],
])

<div {{ $attributes->merge(['class' => 'grid gap-3 sm:grid-cols-2 xl:grid-cols-4']) }} x-data="dashboardQuickAction()">
    @foreach ($actions as $index => $action)
        <button
            type="button"
            @click="run(@js(['label' => $action->label, 'href' => $action->href, 'description' => $action->description]))"
            class="admin-fade-up group flex items-start gap-3 rounded-[var(--radius-admin-lg)] border admin-border admin-surface p-4 text-left transition-all duration-200 admin-focus-ring hover:border-admin-brand/40 hover:shadow-md"
            style="animation-delay: {{ 0.4 + $index * 0.05 }}s"
        >
            <span class="flex size-10 shrink-0 items-center justify-center rounded-[var(--radius-admin)] bg-admin-accent-muted text-admin-brand transition-colors duration-200 group-hover:bg-admin-brand group-hover:text-white">
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                    <path d="{{ $action->icon }}"/>
                </svg>
            </span>
            <span class="min-w-0">
                <span class="block text-sm font-semibold admin-text">{{ $action->label }}</span>
                <span class="mt-0.5 block text-xs admin-muted">{{ $action->description }}</span>
            </span>
        </button>
    @endforeach
</div>
