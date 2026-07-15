@props([
    'title' => null,
    'padding' => true,
])

<section {{ $attributes->merge(['class' => 'admin-card-interactive rounded-[var(--radius-admin-lg)] border admin-border admin-surface']) }}>
    @if ($title)
        <div class="border-b admin-border px-5 py-4">
            <h2 class="text-sm font-semibold admin-text">{{ $title }}</h2>
        </div>
    @endif
    <div @class(['p-5' => $padding])>{{ $slot }}</div>
</section>
