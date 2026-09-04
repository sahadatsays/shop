@php
    use App\Support\Money;

    $defaultShippingKey = old('shipping_method', array_key_first($shippingMethods) ?: '');
    $defaultShipping = $shippingMethods[$defaultShippingKey] ?? collect($shippingMethods)->first();
    $defaultShippingAmount = old(
        'shipping_amount',
        isset($defaultShipping['cost_amount'])
            ? number_format((float) $defaultShipping['cost_amount'], 2, '.', '')
            : number_format(Money::toAmount((int) ($defaultShipping['cost_cents'] ?? 0)), 2, '.', ''),
    );
    $shippingRates = collect($shippingMethods)
        ->mapWithKeys(fn (array $method, string $key) => [$key => (float) ($method['cost_amount'] ?? Money::toAmount((int) ($method['cost_cents'] ?? 0)))])
        ->all();
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
        x-data="orderCreateForm({
            customerSearchUrl: @js(route('admin.orders.search.customers')),
            productSearchUrl: @js(route('admin.orders.search.products')),
            taxRate: {{ $taxRate }},
            currencySymbol: @js($currencySymbol),
            defaultShippingMethod: @js($defaultShippingKey),
            defaultShippingAmount: {{ (float) $defaultShippingAmount }},
            shippingRates: @js($shippingRates),
        })"
    >
        <x-admin.page-header title="Create order" description="Create a customer order on behalf of phone, walk-in, or social channel sales.">
            <x-slot:actions>
                <x-admin.button variant="secondary" size="sm" :href="route('admin.orders.index')">Cancel</x-admin.button>
            </x-slot:actions>
        </x-admin.page-header>

        <form method="POST" action="{{ route('admin.orders.store') }}" class="grid gap-6 xl:grid-cols-3" @submit="return onSubmit()">
            @csrf
            <input type="hidden" name="idempotency_key" value="{{ old('idempotency_key', $idempotencyKey) }}">

            <div class="space-y-6 xl:col-span-2">
                <x-admin.form-card title="Customer" description="Select an existing customer or create one for this order.">
                    <div class="mb-4 flex gap-2">
                        <label class="inline-flex items-center gap-2 rounded-[var(--radius-admin)] border admin-border px-3 py-2 text-sm">
                            <input type="radio" name="customer_mode" value="existing" x-model="customerMode" @checked(old('customer_mode', 'existing') === 'existing')">
                            Existing customer
                        </label>
                        <label class="inline-flex items-center gap-2 rounded-[var(--radius-admin)] border admin-border px-3 py-2 text-sm">
                            <input type="radio" name="customer_mode" value="new" x-model="customerMode" @checked(old('customer_mode') === 'new')">
                            New customer
                        </label>
                    </div>

                    <div x-show="customerMode === 'existing'" class="space-y-3" x-cloak>
                        <input type="hidden" name="customer_id" :value="selectedCustomer?.id || @js(old('customer_id'))">
                        <x-admin.input
                            label="Search customers"
                            name="customer_search"
                            x-model="customerQuery"
                            placeholder="Name, email, or phone…"
                        />
                        <div class="rounded-[var(--radius-admin)] border admin-border divide-y divide-admin-border/60" x-show="customerResults.length">
                            <template x-for="customer in customerResults" :key="customer.id">
                                <button type="button" class="flex w-full items-start gap-3 px-3 py-2 text-left hover:bg-admin-bg/60" @click="selectCustomer(customer)">
                                    <div>
                                        <p class="text-sm font-medium admin-text" x-text="customer.name"></p>
                                        <p class="text-xs admin-muted" x-text="customer.email"></p>
                                    </div>
                                </button>
                            </template>
                        </div>
                        <div x-show="selectedCustomer" class="rounded-[var(--radius-admin)] bg-admin-bg/50 p-3 text-sm">
                            <p class="font-medium admin-text" x-text="selectedCustomer?.name"></p>
                            <p class="admin-muted" x-text="selectedCustomer?.email"></p>
                            <p class="admin-muted" x-text="selectedCustomer?.phone || 'No phone'"></p>
                        </div>
                    </div>

                    <div x-show="customerMode === 'new'" class="grid gap-4 sm:grid-cols-2" x-cloak>
                        <x-admin.input label="Name" name="new_customer[name]" :value="old('new_customer.name')" />
                        <x-admin.input label="Email" name="new_customer[email]" type="email" :value="old('new_customer.email')" />
                        <x-admin.input label="Phone" name="new_customer[phone]" :value="old('new_customer.phone')" class="sm:col-span-2" />
                    </div>
                </x-admin.form-card>

                <x-admin.form-card title="Products" description="Search by name, SKU, barcode, or category. Stock is validated on submit.">
                    <x-admin.input
                        label="Add product"
                        name="product_search"
                        x-model="productQuery"
                        placeholder="Search products…"
                    />
                    <div class="mt-2 rounded-[var(--radius-admin)] border admin-border divide-y divide-admin-border/60" x-show="productResults.length">
                        <template x-for="product in productResults" :key="product.id">
                            <button type="button" class="flex w-full items-center gap-3 px-3 py-2 text-left hover:bg-admin-bg/60" @click="addProduct(product)">
                                <img x-show="product.image" :src="product.image" alt="" class="size-10 rounded object-cover">
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium admin-text" x-text="product.name"></p>
                                    <p class="text-xs admin-muted">
                                        <span x-text="product.sku"></span> · <span x-text="product.price"></span> · Stock <span x-text="product.stock_quantity"></span>
                                    </p>
                                </div>
                            </button>
                        </template>
                    </div>

                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead>
                                <tr class="border-b admin-border bg-admin-bg/40">
                                    <th class="px-3 py-2 text-xs font-semibold uppercase tracking-wider admin-muted">Product</th>
                                    <th class="px-3 py-2 text-xs font-semibold uppercase tracking-wider admin-muted">Qty</th>
                                    <th class="px-3 py-2 text-xs font-semibold uppercase tracking-wider admin-muted">Unit</th>
                                    <th class="px-3 py-2 text-xs font-semibold uppercase tracking-wider admin-muted">Disc.</th>
                                    <th class="px-3 py-2 text-xs font-semibold uppercase tracking-wider admin-muted">Line</th>
                                    <th class="px-3 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-admin-border/60">
                                <template x-for="(item, index) in items" :key="item.product_id">
                                    <tr>
                                        <td class="px-3 py-3">
                                            <input type="hidden" :name="`items[${index}][product_id]`" :value="item.product_id">
                                            <input type="hidden" :name="`items[${index}][unit_price_cents]`" :value="item.unit_price_cents">
                                            <p class="font-medium admin-text" x-text="item.name"></p>
                                            <p class="text-xs admin-muted" x-text="item.sku"></p>
                                        </td>
                                        <td class="px-3 py-3">
                                            <input type="number" min="1" :max="item.stock_quantity" x-model.number="item.quantity" :name="`items[${index}][quantity]`" class="w-20 rounded-[var(--radius-admin)] border admin-border bg-admin-surface px-2 py-1.5 text-sm">
                                        </td>
                                        <td class="px-3 py-3 tabular-nums" x-text="formatMoney(item.unit_price_cents)"></td>
                                        <td class="px-3 py-3">
                                            <input type="number" min="0" step="0.01" x-model.number="item.discount_amount" :name="`items[${index}][discount_amount]`" class="w-24 rounded-[var(--radius-admin)] border admin-border bg-admin-surface px-2 py-1.5 text-sm" placeholder="0.00">
                                        </td>
                                        <td class="px-3 py-3 tabular-nums font-medium" x-text="formatMoney(lineNet(item))"></td>
                                        <td class="px-3 py-3 text-right">
                                            <button type="button" class="text-xs text-admin-danger" @click="removeItem(index)">Remove</button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                        <p class="mt-3 text-sm admin-muted" x-show="items.length === 0">No products added yet.</p>
                    </div>
                </x-admin.form-card>

                <x-admin.form-card title="Shipping address">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <x-admin.input label="First name" name="shipping_address[first_name]" x-ref="shipping_first_name" :value="old('shipping_address.first_name')" required />
                        <x-admin.input label="Last name" name="shipping_address[last_name]" x-ref="shipping_last_name" :value="old('shipping_address.last_name')" required />
                        <x-admin.input label="Address line 1" name="shipping_address[line1]" x-ref="shipping_line1" :value="old('shipping_address.line1')" class="sm:col-span-2" required />
                        <x-admin.input label="Address line 2" name="shipping_address[line2]" x-ref="shipping_line2" :value="old('shipping_address.line2')" class="sm:col-span-2" />
                        <x-admin.input label="City" name="shipping_address[city]" x-ref="shipping_city" :value="old('shipping_address.city')" required />
                        <x-admin.input label="State" name="shipping_address[state]" x-ref="shipping_state" :value="old('shipping_address.state')" required />
                        <x-admin.input label="Postal code" name="shipping_address[postal_code]" x-ref="shipping_postal_code" :value="old('shipping_address.postal_code')" required />
                        <x-admin.input label="Country" name="shipping_address[country]" x-ref="shipping_country" :value="old('shipping_address.country', 'Bangladesh')" required />
                        <x-admin.input label="Phone" name="shipping_address[phone]" x-ref="shipping_phone" :value="old('shipping_address.phone')" class="sm:col-span-2" />
                    </div>
                </x-admin.form-card>
            </div>

            <div class="space-y-6">
                <x-admin.form-card title="Order options">
                    <div class="space-y-4">
                        <x-admin.select label="Order source" name="source" required>
                            @foreach ($sources as $source)
                                <option value="{{ $source->value }}" @selected(old('source', 'admin') === $source->value)>{{ $source->label() }}</option>
                            @endforeach
                        </x-admin.select>

                        <x-admin.select label="Shipping method" name="shipping_method" x-model="shippingMethod">
                            <option value="">Custom / none</option>
                            @foreach ($shippingMethods as $key => $method)
                                <option value="{{ $key }}" @selected($defaultShippingKey === $key)>{{ $method['label'] }} ({{ $method['price'] }})</option>
                            @endforeach
                        </x-admin.select>

                        <x-admin.input label="Shipping charge (BDT)" name="shipping_amount" type="number" min="0" step="0.01" x-model.number="shippingAmount" :value="old('shipping_amount', $defaultShippingAmount)" required />

                        <x-admin.select label="Order discount type" name="order_discount_type" x-model="orderDiscountType">
                            <option value="">No order discount</option>
                            <option value="fixed" @selected(old('order_discount_type') === 'fixed')">Fixed amount</option>
                            <option value="percent" @selected(old('order_discount_type') === 'percent')">Percentage</option>
                        </x-admin.select>

                        <x-admin.input label="Order discount value" name="order_discount_value" type="number" min="0" step="0.01" x-model="orderDiscountValue" :value="old('order_discount_value')" help="Fixed = currency amount. Percent = 0–100." />

                        <x-admin.select label="Payment method" name="payment_method" required>
                            @foreach ($paymentMethods as $method)
                                <option value="{{ $method->value }}" @selected(old('payment_method', 'cash') === $method->value)>{{ $method->label() }}</option>
                            @endforeach
                        </x-admin.select>

                        <x-admin.input label="Initial payment (BDT)" name="initial_payment_amount" type="number" min="0" step="0.01" x-model.number="initialPaymentAmount" :value="old('initial_payment_amount', 0)" help="Leave 0 for unpaid / COD-style pending." />
                        <x-admin.input label="Transaction reference" name="transaction_reference" :value="old('transaction_reference')" />
                        <x-admin.textarea label="Internal notes" name="admin_notes" rows="3">{{ old('admin_notes') }}</x-admin.textarea>
                    </div>
                </x-admin.form-card>

                <x-admin.form-card title="Order summary" description="Server recalculates totals on submit. Browser values are preview only.">
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between"><dt class="admin-muted">Subtotal</dt><dd class="tabular-nums" x-text="formatMoney(subtotalCents)"></dd></div>
                        <div class="flex justify-between"><dt class="admin-muted">Order discount</dt><dd class="tabular-nums" x-text="formatMoney(orderDiscountCents)"></dd></div>
                        <div class="flex justify-between"><dt class="admin-muted">Shipping</dt><dd class="tabular-nums" x-text="formatMoney(shippingCents)"></dd></div>
                        <div class="flex justify-between"><dt class="admin-muted">Tax</dt><dd class="tabular-nums" x-text="formatMoney(taxCents)"></dd></div>
                        <div class="flex justify-between border-t admin-border pt-2 font-semibold"><dt>Grand total</dt><dd class="tabular-nums" x-text="formatMoney(totalCents)"></dd></div>
                        <div class="flex justify-between"><dt class="admin-muted">Initial payment</dt><dd class="tabular-nums" x-text="formatMoney(initialPaymentCents)"></dd></div>
                        <div class="flex justify-between"><dt class="admin-muted">Due</dt><dd class="tabular-nums" x-text="formatMoney(Math.max(0, totalCents - initialPaymentCents))"></dd></div>
                    </dl>
                    <x-admin.button type="submit" class="mt-4 w-full" x-bind:disabled="submitting || items.length === 0">
                        <span x-show="!submitting">Create order</span>
                        <span x-show="submitting" x-cloak>Creating…</span>
                    </x-admin.button>
                </x-admin.form-card>
            </div>
        </form>
    </div>
</x-layouts.admin>
