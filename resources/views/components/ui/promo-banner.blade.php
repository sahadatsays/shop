@props([
    'banners' => collect(),
])

@php
    $items = collect($banners)->values();
    $count = $items->count();
    $gridClass = match (true) {
        $count >= 3 => 'grid-cols-1 md:grid-cols-3',
        $count === 2 => 'grid-cols-1 md:grid-cols-2',
        default => 'grid-cols-1',
    };
@endphp

@if ($items->isNotEmpty())
    <section {{ $attributes->merge(['class' => 'mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8']) }} data-reveal>
        <div class="grid {{ $gridClass }} gap-6">
            @foreach ($items as $banner)
                <a href="{{ $banner->url ?: '#' }}" class="group relative flex min-h-64 flex-col justify-end overflow-hidden rounded-card shadow-card transition-all duration-300 hover:-translate-y-1 hover:shadow-card-hover">
                    @if ($banner->imageUrl())
                        <img src="{{ $banner->imageUrl() }}" alt="{{ $banner->title }}" loading="lazy"
                             class="absolute inset-0 size-full object-cover transition-transform duration-700 group-hover:scale-105">
                    @endif
                    <span class="absolute inset-0 bg-linear-to-t from-navy-950/85 via-navy-950/30 to-transparent" aria-hidden="true"></span>
                    <span class="relative p-8">
                        <span class="block font-display text-2xl font-bold text-white">{{ $banner->title }}</span>
                        @if ($banner->button_label)
                            <span class="mt-4 inline-flex items-center gap-2 rounded-xl bg-white/15 px-4 py-2.5 text-sm font-semibold text-white backdrop-blur-sm transition-colors group-hover:bg-bronze-500">
                                {{ $banner->button_label }}
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                            </span>
                        @endif
                    </span>
                </a>
            @endforeach
        </div>
    </section>
@endif
