@php
    $initialItems = old('items', []);
@endphp

<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.adminToast?.push({ title: @json($errors->first()), type: 'error' });
            });
        </script>
    @endif

    <div
        x-data="purchaseCreateForm({
            productSearchUrl: @js($productSearchUrl),
            currencySymbol: @js($currencySymbol),
            shippingCents: {{ (int) old('shipping_cents', 0) }},
            discountCents: {{ (int) old('discount_cents', 0) }},
            taxCents: {{ (int) old('tax_cents', 0) }},
            initialItems: @js(collect($initialItems)->map(fn ($item) => [
                'product_id' => (int) ($item['product_id'] ?? 0),
                'name' => $item['name'] ?? ('Product #'.($item['product_id'] ?? '')),
                'sku' => $item['sku'] ?? '',
                'unit_cost_cents' => (int) ($item['unit_cost_cents'] ?? 0),
                'quantity' => (int) ($item['quantity'] ?? 1),
                'discount_cents' => (int) ($item['discount_cents'] ?? 0),
                'tax_cents' => (int) ($item['tax_cents'] ?? 0),
            ])->values()),
        })"
    >
        <x-admin.page-header title="Create purchase" description="Select a supplier and products. Inventory increases only after stock is received.">
            <x-slot:actions>
                <x-admin.button variant="secondary" size="sm" :href="route('admin.purchases.index')">Back</x-admin.button>
            </x-slot:actions>
        </x-admin.page-header>

        <form method="POST" action="{{ route('admin.purchases.store') }}" class="grid gap-6 xl:grid-cols-3" @submit="return onSubmit()">
            @csrf

            <div class="space-y-6 xl:col-span-2">
                <x-admin.form-card title="Purchase details">
                    <div class="grid gap-5 sm:grid-cols-2">
                        <x-admin.select label="Supplier" name="supplier_id" required class="sm:col-span-2">
                            <option value="">Select supplier</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" @selected((string) old('supplier_id') === (string) $supplier->id)>
                                    {{ $supplier->name }}@if ($supplier->company_name) — {{ $supplier->company_name }}@endif
                                </option>
                            @endforeach
                        </x-admin.select>

                        <x-admin.input label="Purchase date" name="purchase_date" type="date" :value="old('purchase_date', now()->toDateString())" required />
                        <x-admin.input label="Expected delivery" name="expected_delivery_date" type="date" :value="old('expected_delivery_date')" />
                        <x-admin.textarea label="Notes" name="notes" :value="old('notes')" class="sm:col-span-2" rows="3" />
                    </div>
                </x-admin.form-card>

                <x-admin.form-card title="Products" description="Set ordered quantities and supplier unit costs. Selling price is not changed.">
                    <div class="relative mb-4">
                        <input
                            type="search"
                            x-model="productQuery"
                            placeholder="Search products by name or SKU…"
                            class="block w-full rounded-[var(--radius-admin)] border admin-border bg-admin-surface px-3.5 py-2.5 text-sm admin-text admin-focus-ring"
                        >
                        <div x-show="productResults.length" x-cloak class="absolute z-20 mt-1 max-h-64 w-full overflow-auto rounded-[var(--radius-admin)] border admin-border bg-admin-surface shadow-lg">
                            <template x-for="product in productResults" :key="product.id">
                                <button type="button" class="flex w-full items-start justify-between gap-3 px-3 py-2 text-left text-sm hover:bg-admin-bg" @click="addProduct(product)">
                                    <span>
                                        <span class="block font-medium admin-text" x-text="product.name"></span>
                                        <span class="block text-xs admin-muted" x-text="product.sku"></span>
                                    </span>
                                    <span class="text-xs admin-muted" x-text="product.cost"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="border-b admin-border text-left text-xs uppercase tracking-wider admin-muted">
                                    <th class="px-2 py-2">Product</th>
                                    <th class="px-2 py-2">Qty</th>
                                    <th class="px-2 py-2">Unit cost (cents)</th>
                                    <th class="px-2 py-2">Disc</th>
                                    <th class="px-2 py-2">Tax</th>
                                    <th class="px-2 py-2">Line</th>
                                    <th class="px-2 py-2"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(item, index) in items" :key="item.product_id">
                                    <tr class="border-b admin-border/60">
                                        <td class="px-2 py-3">
                                            <input type="hidden" :name="`items[${index}][product_id]`" :value="item.product_id">
                                            <div class="font-medium admin-text" x-text="item.name"></div>
                                            <div class="text-xs admin-muted" x-text="item.sku"></div>
                                        </td>
                                        <td class="px-2 py-3">
                                            <input type="number" min="1" x-model.number="item.quantity" :name="`items[${index}][quantity]`" class="w-20 rounded-[var(--radius-admin)] border admin-border bg-admin-surface px-2 py-1.5">
                                        </td>
                                        <td class="px-2 py-3">
                                            <input type="number" min="0" x-model.number="item.unit_cost_cents" :name="`items[${index}][unit_cost_cents]`" class="w-28 rounded-[var(--radius-admin)] border admin-border bg-admin-surface px-2 py-1.5">
                                        </td>
                                        <td class="px-2 py-3">
                                            <input type="number" min="0" x-model.number="item.discount_cents" :name="`items[${index}][discount_cents]`" class="w-20 rounded-[var(--radius-admin)] border admin-border bg-admin-surface px-2 py-1.5">
                                        </td>
                                        <td class="px-2 py-3">
                                            <input type="number" min="0" x-model.number="item.tax_cents" :name="`items[${index}][tax_cents]`" class="w-20 rounded-[var(--radius-admin)] border admin-border bg-admin-surface px-2 py-1.5">
                                        </td>
                                        <td class="px-2 py-3 tabular-nums" x-text="formatMoney(lineNet(item))"></td>
                                        <td class="px-2 py-3 text-right">
                                            <button type="button" class="text-admin-danger" @click="removeItem(index)">Remove</button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    <p x-show="items.length === 0" class="mt-3 text-sm admin-muted">No products added yet.</p>
                </x-admin.form-card>
            </div>

            <div class="space-y-6">
                <x-admin.form-card title="Totals" description="Server recalculates on save.">
                    <div class="space-y-4">
                        <x-admin.input label="Order discount (cents)" name="discount_cents" type="number" min="0" x-model.number="discountCents" />
                        <x-admin.input label="Shipping (cents)" name="shipping_cents" type="number" min="0" x-model.number="shippingCents" />
                        <x-admin.input label="Tax (cents)" name="tax_cents" type="number" min="0" x-model.number="taxCents" />
                        <dl class="space-y-2 border-t admin-border pt-4 text-sm">
                            <div class="flex justify-between"><dt class="admin-muted">Subtotal</dt><dd class="tabular-nums" x-text="formatMoney(subtotalCents)"></dd></div>
                            <div class="flex justify-between font-medium"><dt>Grand total</dt><dd class="tabular-nums" x-text="formatMoney(grandTotalCents)"></dd></div>
                        </dl>
                    </div>
                </x-admin.form-card>

                <div class="flex flex-col gap-2">
                    <x-admin.button type="submit">Save draft</x-admin.button>
                    <x-admin.button type="submit" name="submit" value="1" variant="secondary">Save & submit</x-admin.button>
                    <x-admin.button variant="ghost" :href="route('admin.purchases.index')">Cancel</x-admin.button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.admin>
