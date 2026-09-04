<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.adminToast?.push({ title: @json(session('success')), type: 'success' });
            });
        </script>
    @endif

    <x-admin.page-header :title="$warehouse->name" description="Warehouse location details and inventory summary.">
        <x-slot:actions>
            <x-admin.button variant="secondary" size="sm" :href="route('admin.inventory.movements', ['warehouse_id' => $warehouse->id])">Stock history</x-admin.button>
            @if (auth('admin')->user()?->hasPermission('warehouses.manage'))
                <x-admin.button size="sm" :href="route('admin.warehouses.edit', $warehouse)">Edit warehouse</x-admin.button>
            @endif
        </x-slot:actions>
    </x-admin.page-header>

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <x-admin.form-card title="Overview">
                <dl>
                    <x-admin.detail-row label="Name" :value="$warehouse->name" />
                    <x-admin.detail-row label="Code">
                        <code class="rounded bg-admin-bg px-1.5 py-0.5 font-mono text-xs">{{ $warehouse->code }}</code>
                    </x-admin.detail-row>
                    <x-admin.detail-row label="Address" :value="$warehouse->address" />
                    <x-admin.detail-row label="City" :value="$warehouse->city" />
                    <x-admin.detail-row label="State" :value="$warehouse->state" />
                    <x-admin.detail-row label="Country" :value="$warehouse->country" />
                    <x-admin.detail-row label="Status">
                        <x-admin.badge :variant="$warehouse->is_active ? 'success' : 'muted'" dot>{{ $warehouse->is_active ? 'Active' : 'Inactive' }}</x-admin.badge>
                    </x-admin.detail-row>
                    <x-admin.detail-row label="Default">
                        @if ($warehouse->is_default)
                            <x-admin.badge variant="brand">Default warehouse</x-admin.badge>
                        @else
                            <span class="admin-muted">No</span>
                        @endif
                    </x-admin.detail-row>
                    <x-admin.detail-row label="Sort order" :value="$warehouse->sort_order" />
                    <x-admin.detail-row label="Created" :value="$warehouse->created_at?->format('M j, Y g:i A')" />
                </dl>
            </x-admin.form-card>
        </div>

        <div class="space-y-6">
            <x-admin.form-card title="Inventory">
                <dl>
                    <x-admin.detail-row label="Total units" :value="(int) ($warehouse->total_stock ?? 0)" />
                    <x-admin.detail-row label="SKU locations" :value="$warehouse->stock_levels_count" />
                    <x-admin.detail-row label="Stock movements" :value="$warehouse->movements_count" />
                </dl>
                <div class="mt-4">
                    <x-admin.button variant="secondary" size="sm" class="w-full" :href="route('admin.inventory.index', ['warehouse_id' => $warehouse->id])">View inventory</x-admin.button>
                </div>
            </x-admin.form-card>

            @if (auth('admin')->user()?->hasPermission('warehouses.manage'))
                <form method="POST" action="{{ route('admin.warehouses.destroy', $warehouse) }}" onsubmit="return confirm('Delete this warehouse permanently?')">
                    @csrf
                    @method('DELETE')
                    <x-admin.button type="submit" variant="danger-ghost" class="w-full">Delete warehouse</x-admin.button>
                </form>
            @endif
        </div>
    </div>
</x-layouts.admin>
