@props([
    'title' => 'Something went wrong',
    'message' => 'We could not load this data. Please try again.',
])

<div {{ $attributes->merge(['class' => 'rounded-[var(--radius-admin-lg)] border border-red-200 bg-red-50 p-6 dark:border-red-900 dark:bg-red-950/30']) }} role="alert">
    <div class="flex items-start gap-3">
        <span class="mt-0.5 flex size-8 shrink-0 items-center justify-center rounded-full bg-red-100 text-admin-danger dark:bg-red-950">
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 9v4M12 17h.01"/><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/></svg>
        </span>
        <div class="min-w-0 flex-1">
            <h3 class="text-sm font-semibold text-admin-danger">{{ $title }}</h3>
            <p class="mt-1 text-sm admin-text-secondary">{{ $message }}</p>
            @if (isset($actions))
                <div class="mt-4 flex gap-2">{{ $actions }}</div>
            @endif
        </div>
    </div>
</div>
