<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header :title="$product->name" description="Stock levels, warehouse breakdown, and recent movements.">
        <x-slot:actions>
            <x-admin.button variant="secondary" size="sm" :href="route('admin.inventory.index')">Back</x-admin.button>
            <x-admin.button size="sm" :href="route('admin.inventory.adjust', $product)">Adjust stock</x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <x-admin.form-card title="Stock overview">
                <dl>
                    <x-admin.detail-row label="SKU">
                        <code class="rounded bg-admin-bg px-1.5 py-0.5 font-mono text-xs">{{ $product->sku }}</code>
                    </x-admin.detail-row>
                    <x-admin.detail-row label="Total on hand" :value="$product->stock_quantity" />
                    <x-admin.detail-row label="Low stock threshold" :value="$product->low_stock_threshold" />
                    <x-admin.detail-row label="Status">
                        <x-admin.badge :variant="$product->stockStatusBadgeVariant()" dot>{{ $product->stockStatusLabel() }}</x-admin.badge>
                    </x-admin.detail-row>
                    <x-admin.detail-row label="Category" :value="$product->category?->name" />
                    <x-admin.detail-row label="Brand" :value="$product->brand?->name" />
                </dl>
            </x-admin.form-card>

            <x-admin.form-card title="Warehouse stock" description="Per-location quantities — ready for multi-warehouse fulfillment.">
                @if ($product->warehouseStock->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead>
                                <tr class="border-b admin-border bg-admin-bg/40">
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider admin-muted">Warehouse</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider admin-muted">Location</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider admin-muted">Quantity</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-admin-border/60">
                                @foreach ($product->warehouseStock as $stock)
                                    <tr>
                                        <td class="px-4 py-3 font-medium admin-text">{{ $stock->warehouse->name }}</td>
                                        <td class="px-4 py-3 admin-text-secondary">{{ $stock->warehouse->displayLocation() }}</td>
                                        <td class="px-4 py-3 tabular-nums admin-text">{{ $stock->quantity }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm admin-muted">No warehouse stock recorded yet. Use Adjust stock to set initial inventory.</p>
                @endif
            </x-admin.form-card>

            <x-admin.form-card title="Recent movements">
                @if ($product->stockMovements->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead>
                                <tr class="border-b admin-border bg-admin-bg/40">
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider admin-muted">Date</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider admin-muted">Type</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider admin-muted">Change</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider admin-muted">After</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider admin-muted">Warehouse</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-admin-border/60">
                                @foreach ($product->stockMovements as $movement)
                                    <tr>
                                        <td class="px-4 py-3 admin-text-secondary">{{ $movement->created_at?->format('M j, Y g:i A') }}</td>
                                        <td class="px-4 py-3">
                                            <x-admin.badge :variant="$movement->type->badgeVariant()">{{ $movement->type->label() }}</x-admin.badge>
                                        </td>
                                        <td class="px-4 py-3 tabular-nums font-medium admin-text">{{ $movement->formattedChange() }}</td>
                                        <td class="px-4 py-3 tabular-nums admin-text-secondary">{{ $movement->quantity_after }}</td>
                                        <td class="px-4 py-3 admin-text-secondary">{{ $movement->warehouse->code }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        <x-admin.button variant="secondary" size="sm" :href="route('admin.inventory.movements', ['product_id' => $product->id])">View full history</x-admin.button>
                    </div>
                @else
                    <p class="text-sm admin-muted">No stock movements recorded yet.</p>
                @endif
            </x-admin.form-card>
        </div>

        <div class="space-y-6">
            <x-admin.form-card title="Quick actions">
                <div class="flex flex-col gap-2">
                    <x-admin.button :href="route('admin.inventory.adjust', ['product' => $product, 'type' => 'adjustment_in'])" variant="secondary" class="w-full">Increase stock</x-admin.button>
                    <x-admin.button :href="route('admin.inventory.adjust', ['product' => $product, 'type' => 'adjustment_out'])" variant="secondary" class="w-full">Decrease stock</x-admin.button>
                    <x-admin.button :href="route('admin.products.show', $product)" variant="ghost" class="w-full">View product</x-admin.button>
                </div>
            </x-admin.form-card>
        </div>
    </div>
</x-layouts.admin>
