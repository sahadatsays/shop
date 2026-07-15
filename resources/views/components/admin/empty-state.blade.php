@props([
    'title',
    'description' => null,
    'actionLabel' => null,
    'actionHref' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center rounded-[var(--radius-admin-lg)] border border-dashed admin-border px-6 py-12 text-center']) }}>
    <div class="flex size-12 items-center justify-center rounded-full bg-admin-accent-muted admin-muted">
        <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
            <path d="M12 8v8M8 12h8"/><circle cx="12" cy="12" r="9"/>
        </svg>
    </div>
    <h3 class="mt-4 text-sm font-semibold admin-text">{{ $title }}</h3>
    @if ($description)
        <p class="mt-1 max-w-sm text-sm admin-muted">{{ $description }}</p>
    @endif
    @if ($actionLabel)
        <div class="mt-4">
            <x-admin.button :href="$actionHref" size="sm">{{ $actionLabel }}</x-admin.button>
        </div>
    @endif
</div>
