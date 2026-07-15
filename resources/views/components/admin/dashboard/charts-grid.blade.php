@props([
    'charts' => [],
])

@php
    $chartPayload = collect($charts)->map(fn ($chart) => $chart->toArray())->values()->all();
@endphp

<div
    x-data="dashboardCharts()"
    x-init="init(@js($chartPayload))"
    x-cloak
    {{ $attributes->merge(['class' => 'grid gap-6 lg:grid-cols-2 xl:grid-cols-3']) }}
>
    @foreach ($charts as $index => $chart)
        <x-admin.card
            :title="$chart->title"
            class="admin-fade-up admin-card-interactive"
            style="animation-delay: {{ 0.25 + $index * 0.05 }}s"
        >
            <div
                x-ref="{{ $chart->id }}"
                class="min-h-[260px] w-full"
                role="img"
                aria-label="{{ $chart->title }} chart"
            ></div>
        </x-admin.card>
    @endforeach
</div>
