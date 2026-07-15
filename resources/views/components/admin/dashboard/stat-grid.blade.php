@props([
    'stats' => [],
])

<div {{ $attributes->merge(['class' => 'grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4']) }}>
    @foreach ($stats as $index => $stat)
        <x-admin.stat-card
            class="admin-fade-up"
            style="animation-delay: {{ ($index + 1) * 0.05 }}s"
            :label="$stat->label"
            :value="$stat->value"
            :change="$stat->change"
            :trend="$stat->trend"
            :icon="$stat->icon"
        />
    @endforeach
</div>
