<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header title="Stock history" description="Complete inventory log of all stock movements across products and warehouses.">
        <x-slot:actions>
            <x-admin.button variant="secondary" size="sm" :href="route('admin.inventory.index')">Inventory</x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.data-table>
        <x-slot:toolbar>
            <form method="GET" action="{{ route('admin.inventory.movements') }}" class="flex flex-col gap-2 lg:flex-row lg:items-center">
                <select name="type" class="rounded-[var(--radius-admin)] border admin-border bg-admin-surface px-3 py-2.5 text-sm admin-text admin-focus-ring">
                    <option value="">All types</option>
                    @foreach (\App\Enums\StockMovementType::cases() as $type)
                        <option value="{{ $type->value }}" @selected(($filters['type'] ?? null) === $type->value)>{{ $type->label() }}</option>
                    @endforeach
                </select>
                <select name="warehouse_id" class="rounded-[var(--radius-admin)] border admin-border bg-admin-surface px-3 py-2.5 text-sm admin-text admin-focus-ring">
                    <option value="">All warehouses</option>
                    @foreach ($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" @selected((int) ($filters['warehouse_id'] ?? 0) === $warehouse->id)>{{ $warehouse->name }}</option>
                    @endforeach
                </select>
                <div class="relative min-w-0 flex-1">
                    <svg class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 admin-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3-3"/></svg>
                    <input
                        type="search"
                        name="search"
                        value="{{ $filters['search'] ?? '' }}"
                        placeholder="Search product, SKU, reference…"
                        class="block w-full rounded-[var(--radius-admin)] border admin-border bg-admin-surface py-2.5 pl-9 pr-3 text-sm admin-text placeholder:admin-muted admin-focus-ring"
                    >
                </div>
                <x-admin.button type="submit" variant="secondary" size="sm">Filter</x-admin.button>
            </form>
        </x-slot:toolbar>

        <thead>
            <tr class="border-b admin-border bg-admin-bg/40">
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Date</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Product</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Type</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Change</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Before → After</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Warehouse</th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider admin-muted">Notes</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-admin-border/60">
            @forelse ($movements as $movement)
                <tr class="transition-colors hover:bg-admin-bg/60">
                    <td class="px-6 py-4 text-sm admin-text-secondary whitespace-nowrap">{{ $movement->created_at?->format('M j, Y g:i A') }}</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.inventory.show', $movement->product) }}" class="font-medium admin-text hover:text-admin-brand">{{ $movement->product->name }}</a>
                        <p class="font-mono text-xs admin-muted">{{ $movement->product->sku }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <x-admin.badge :variant="$movement->type->badgeVariant()">{{ $movement->type->label() }}</x-admin.badge>
                    </td>
                    <td class="px-6 py-4 tabular-nums font-medium admin-text">{{ $movement->formattedChange() }}</td>
                    <td class="px-6 py-4 tabular-nums text-sm admin-text-secondary">{{ $movement->quantity_before }} → {{ $movement->quantity_after }}</td>
                    <td class="px-6 py-4 text-sm admin-text-secondary">{{ $movement->warehouse->code }}</td>
                    <td class="px-6 py-4 text-sm admin-text-secondary">
                        {{ $movement->notes ?: ($movement->reference ?: '—') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-14">
                        <x-admin.empty-state
                            title="No movements yet"
                            description="Stock adjustments and initial stock entries will appear here."
                        />
                    </td>
                </tr>
            @endforelse
        </tbody>

        <x-slot:footer>
            <x-admin.pagination :paginator="$movements" />
        </x-slot:footer>
    </x-admin.data-table>
</x-layouts.admin>
