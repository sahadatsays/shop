@props([
    'products',
])

<x-admin.card title="Top Products" {{ $attributes }}>
    <ul class="divide-y admin-border">
        @forelse ($products as $index => $product)
            <li class="flex items-center gap-3 py-3 transition-colors duration-150 first:pt-0 last:pb-0 hover:bg-admin-accent-muted/30">
                <span class="flex size-7 shrink-0 items-center justify-center rounded-full bg-admin-accent-muted text-xs font-semibold admin-muted">{{ $index + 1 }}</span>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-medium admin-text">{{ $product->name }}</p>
                    <p class="text-xs admin-muted">{{ $product->category }}</p>
                </div>
                <div class="shrink-0 text-right">
                    <p class="text-sm font-semibold admin-text">{{ $product->revenueFormatted }}</p>
                    <p class="text-[11px] admin-muted">{{ $product->unitsSold }} sold</p>
                </div>
            </li>
        @empty
            <li class="py-6">
                <x-admin.empty-state title="No sales data" description="Top products will rank by units sold." />
            </li>
        @endforelse
    </ul>
</x-admin.card>
