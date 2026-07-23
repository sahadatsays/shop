@php
    $products = $data['products'] ?? collect();
@endphp

@if ($products->isEmpty())
    <x-admin.empty-state title="Stock looks healthy" description="No products are at or below their low-stock threshold." />
@else
    <ul class="divide-y admin-border">
        @foreach ($products as $product)
            <li class="flex items-center justify-between gap-3 py-2.5">
                <div class="min-w-0">
                    <a href="{{ $product['url'] }}" class="block truncate text-sm font-medium admin-text hover:text-admin-brand">{{ $product['name'] }}</a>
                    <p class="text-xs admin-muted">SKU {{ $product['sku'] }}</p>
                </div>
                <x-admin.badge :variant="$product['stock'] === 0 ? 'danger' : 'warning'">
                    {{ $product['stock'] }} / {{ $product['threshold'] }}
                </x-admin.badge>
            </li>
        @endforeach
    </ul>
@endif
