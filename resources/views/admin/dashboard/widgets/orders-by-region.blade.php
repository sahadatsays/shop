@php
    $regions = $data['regions'] ?? [];
    $maxOrders = collect($regions)->max('orders') ?: 1;
@endphp

@if (empty($regions))
    <x-admin.empty-state title="No regional data" description="Orders with shipping locations will appear here." />
@else
    <p class="mb-3 text-xs admin-muted">
        Aggregated by shipping region. Future-ready for an interactive district map.
    </p>
    <ul class="space-y-2.5">
        @foreach ($regions as $region)
            <li>
                <div class="flex items-center justify-between gap-3 text-sm">
                    <span class="truncate font-medium admin-text">{{ $region['region'] }}</span>
                    <span class="shrink-0 admin-text-secondary">{{ $region['orders'] }} orders · {{ $region['revenue'] }} · {{ $region['customers'] }} cust.</span>
                </div>
                <div class="mt-1 h-1.5 w-full overflow-hidden rounded-full bg-admin-accent-muted">
                    <div class="h-full rounded-full bg-admin-brand" style="width: {{ max(4, (int) round($region['orders'] / $maxOrders * 100)) }}%"></div>
                </div>
            </li>
        @endforeach
    </ul>
@endif
