@php
    $selectedType = old('type', request('type', \App\Enums\StockMovementType::AdjustmentIn->value));
    $defaultWarehouse = $warehouses->firstWhere('is_default', true) ?? $warehouses->first();
@endphp

<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header title="Adjust stock" :description="'Update inventory for ' . $product->name">
        <x-slot:actions>
            <x-admin.button variant="secondary" size="sm" :href="route('admin.inventory.show', $product)">Cancel</x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    @if ($errors->any())
        <div class="mb-6 rounded-[var(--radius-admin-lg)] border border-admin-danger/30 bg-red-50 px-4 py-3 dark:bg-red-950/20" role="alert">
            <p class="text-sm font-medium text-admin-danger">Please fix the errors below and try again.</p>
        </div>
    @endif

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="xl:col-span-2">
            <x-admin.form-card title="Stock adjustment" description="Record an increase, decrease, or warehouse recount. All changes are logged.">
                <form method="POST" action="{{ route('admin.inventory.adjust.store', $product) }}" class="space-y-5">
                    @csrf

                    <x-admin.select label="Warehouse" name="warehouse_id" help="Location where stock will be adjusted." required>
                        @foreach ($warehouses as $warehouse)
                            @php
                                $currentQty = $product->warehouseStock->firstWhere('warehouse_id', $warehouse->id)?->quantity ?? 0;
                            @endphp
                            <option value="{{ $warehouse->id }}" @selected((int) old('warehouse_id', $defaultWarehouse?->id) === $warehouse->id)>
                                {{ $warehouse->name }} ({{ $currentQty }} on hand)
                            </option>
                        @endforeach
                    </x-admin.select>

                    <x-admin.select label="Adjustment type" name="type" help="Increase adds units, decrease removes units, recount sets exact quantity." required>
                        @foreach (\App\Enums\StockMovementType::adjustable() as $type)
                            <option value="{{ $type->value }}" @selected($selectedType === $type->value)>
                                {{ $type->label() }}
                            </option>
                        @endforeach
                    </x-admin.select>

                    <x-admin.input
                        label="Quantity"
                        name="quantity"
                        type="number"
                        min="1"
                        :value="old('quantity', 1)"
                        help="Units to add, remove, or set (for recount)."
                        required
                    />

                    <x-admin.input
                        label="Reference"
                        name="reference"
                        :value="old('reference')"
                        placeholder="PO-12345, cycle count, etc."
                        help="Optional reference number for this adjustment."
                    />

                    <x-admin.textarea
                        label="Notes"
                        name="notes"
                        rows="3"
                        placeholder="Reason for adjustment…"
                        help="Optional notes stored in the inventory log."
                    >{{ old('notes') }}</x-admin.textarea>

                    <x-admin.button type="submit">Record adjustment</x-admin.button>
                </form>
            </x-admin.form-card>
        </div>

        <div class="space-y-6">
            <x-admin.form-card title="Current stock">
                <dl>
                    <x-admin.detail-row label="Product" :value="$product->name" />
                    <x-admin.detail-row label="SKU" :value="$product->sku" />
                    <x-admin.detail-row label="Total on hand" :value="$product->stock_quantity" />
                    <x-admin.detail-row label="Status">
                        <x-admin.badge :variant="$product->stockStatusBadgeVariant()" dot>{{ $product->stockStatusLabel() }}</x-admin.badge>
                    </x-admin.detail-row>
                </dl>
            </x-admin.form-card>
        </div>
    </div>
</x-layouts.admin>
