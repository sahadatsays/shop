@php
    $discount ??= null;
    $formatDatetimeLocal = fn ($value) => $value ? $value->format('Y-m-d\TH:i') : '';
    $minOrder = old('min_order', $discount?->min_order_cents ? number_format($discount->min_order_cents / 100, 2, '.', '') : '');
@endphp

@if ($errors->any())
    <div class="mb-6 rounded-[var(--radius-admin-lg)] border border-admin-danger/30 bg-red-50 px-4 py-3 dark:bg-red-950/20" role="alert">
        <p class="text-sm font-medium text-admin-danger">Please fix the errors below and try again.</p>
    </div>
@endif

<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <x-admin.form-card title="Discount details" description="Code, value, and eligibility rules.">
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-admin.input
                        label="Code"
                        name="code"
                        :value="old('code', $discount?->code ?? '')"
                        placeholder="VALOR10"
                        help="Unique coupon code (stored uppercase)."
                        required
                        class="font-mono uppercase sm:col-span-2"
                    />

                    <x-admin.input
                        label="Name"
                        name="name"
                        :value="old('name', $discount?->name ?? '')"
                        placeholder="Valor 10% Off"
                        required
                        class="sm:col-span-2"
                    />

                    <x-admin.textarea
                        label="Description"
                        name="description"
                        rows="3"
                        placeholder="Optional internal or customer-facing notes…"
                        class="sm:col-span-2"
                    >{{ old('description', $discount?->description ?? '') }}</x-admin.textarea>

                    <x-admin.select label="Type" name="type" required>
                        @foreach (\App\Enums\DiscountType::cases() as $type)
                            <option value="{{ $type->value }}" @selected(old('type', $discount?->type?->value ?? 'percent') === $type->value)>
                                {{ $type->label() }}
                            </option>
                        @endforeach
                    </x-admin.select>

                    <x-admin.input
                        label="Value"
                        name="value"
                        type="number"
                        min="1"
                        :value="old('value', $discount?->value ?? '')"
                        placeholder="10"
                        help="Percent (e.g. 10) or fixed amount in cents (e.g. 1000 = $10)."
                        required
                    />

                    <x-admin.input
                        label="Minimum order"
                        name="min_order"
                        type="number"
                        step="0.01"
                        min="0"
                        :value="$minOrder"
                        placeholder="50.00"
                        help="Optional minimum subtotal in USD."
                    />

                    <x-admin.input
                        label="Max uses"
                        name="max_uses"
                        type="number"
                        min="1"
                        :value="old('max_uses', $discount?->max_uses ?? '')"
                        placeholder="Unlimited"
                        help="Leave blank for unlimited redemptions."
                    />
                </div>
            </x-admin.form-card>

            <x-admin.form-card title="Schedule" description="Optional start and end dates.">
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-admin.input
                        label="Starts at"
                        name="starts_at"
                        type="datetime-local"
                        :value="old('starts_at', $formatDatetimeLocal($discount?->starts_at))"
                    />
                    <x-admin.input
                        label="Ends at"
                        name="ends_at"
                        type="datetime-local"
                        :value="old('ends_at', $formatDatetimeLocal($discount?->ends_at))"
                    />
                </div>
            </x-admin.form-card>
        </div>

        <div class="space-y-6">
            <x-admin.form-card title="Status">
                <x-admin.checkbox
                    label="Active"
                    name="is_active"
                    :checked="(bool) old('is_active', $discount?->is_active ?? true)"
                    help="Inactive discounts cannot be redeemed."
                />
            </x-admin.form-card>

            <div class="flex flex-col gap-2 sm:flex-row">
                <x-admin.button type="submit" class="flex-1">{{ $submitLabel }}</x-admin.button>
                <x-admin.button variant="secondary" :href="route('admin.discounts.index')" class="flex-1">Cancel</x-admin.button>
            </div>
        </div>
    </div>
</form>
