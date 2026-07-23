@props([
    'title' => null,
    'description' => null,
])

<section {{ $attributes->merge(['class' => 'overflow-hidden rounded-[var(--radius-admin-lg)] border admin-border admin-surface shadow-sm']) }}>
    @if ($title || $description)
        <div class="border-b admin-border bg-admin-bg/40 px-5 py-4 sm:px-6">
            @if ($title)
                <h2 class="text-sm font-semibold admin-text">{{ $title }}</h2>
            @endif
            @if ($description)
                <p class="mt-0.5 text-xs admin-muted">{{ $description }}</p>
            @endif
        </div>
    @endif
    <div class="p-5 sm:p-6">
        {{ $slot }}
    </div>
</section>
