@props([
    'title' => null,
    'description' => null,
])

<section {{ $attributes->merge(['class' => 'space-y-4']) }}>
    @if ($title || $description)
        <div>
            @if ($title)
                <h2 class="text-base font-semibold admin-text">{{ $title }}</h2>
            @endif
            @if ($description)
                <p class="mt-1 text-sm admin-muted">{{ $description }}</p>
            @endif
        </div>
    @endif
    {{ $slot }}
</section>
