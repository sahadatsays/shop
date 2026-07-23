@props([
    'brands',
])

<x-admin.card title="Featured Brands" {{ $attributes }}>
    <ul class="divide-y admin-border">
        @forelse ($brands as $brand)
            <li class="flex items-center gap-3 py-3 transition-colors duration-150 first:pt-0 last:pb-0 hover:bg-admin-accent-muted/30">
                @if ($brand->logoUrl)
                    <img src="{{ $brand->logoUrl }}" alt="" class="size-9 rounded-[var(--radius-admin)] object-contain bg-admin-bg p-1">
                @else
                    <span class="flex size-9 items-center justify-center rounded-[var(--radius-admin)] bg-admin-accent-muted text-xs font-semibold admin-muted">
                        {{ strtoupper(substr($brand->name, 0, 2)) }}
                    </span>
                @endif
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-medium admin-text">{{ $brand->name }}</p>
                    <p class="text-xs admin-muted">{{ $brand->productCount }} products</p>
                </div>
                <a href="{{ route('admin.brands.index', ['featured' => 1]) }}" class="text-xs font-medium text-admin-info admin-focus-ring rounded px-1">View</a>
            </li>
        @empty
            <li class="py-6">
                <x-admin.empty-state
                    title="No featured brands"
                    description="Mark brands as featured to highlight them here."
                    action-label="Manage brands"
                    :action-href="route('admin.brands.index')"
                />
            </li>
        @endforelse
    </ul>
</x-admin.card>
