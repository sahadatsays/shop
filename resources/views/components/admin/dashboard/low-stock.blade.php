@props([
    'products',
])

<x-admin.card title="Low Stock Alerts" {{ $attributes }}>
    <ul class="divide-y admin-border">
        @forelse ($products as $product)
            <li class="flex items-center justify-between gap-3 py-3 transition-colors duration-150 first:pt-0 last:pb-0 hover:bg-admin-accent-muted/30">
                <div class="min-w-0">
                    <a href="{{ route('admin.inventory.show', $product->productId) }}" class="truncate text-sm font-medium admin-text hover:text-admin-brand">{{ $product->name }}</a>
                    <p class="text-xs admin-muted">Threshold: {{ $product->threshold }}</p>
                </div>
                <x-admin.badge :variant="$product->stockQuantity === 0 ? 'danger' : 'warning'">
                    {{ $product->stockQuantity }} left
                </x-admin.badge>
            </li>
        @empty
            <li class="py-6">
                <p class="text-center text-sm admin-muted">All products are adequately stocked.</p>
            </li>
        @endforelse
    </ul>
</x-admin.card>
