@php
    $warehouse = $warehouse ?? null;
    $isActive = old('is_active', $warehouse->is_active ?? true);
    $isDefault = old('is_default', $warehouse->is_default ?? false);
@endphp

@if ($errors->any())
    <div class="mb-6 rounded-[var(--radius-admin-lg)] border border-admin-danger/30 bg-red-50 px-4 py-3 dark:bg-red-950/20" role="alert">
        <p class="text-sm font-medium text-admin-danger">Please fix the errors below and try again.</p>
    </div>
@endif

<div class="grid gap-6 xl:grid-cols-3">
    <div class="space-y-6 xl:col-span-2">
        <x-admin.form-card title="Location" description="Warehouse identity and address details.">
            <div class="grid gap-5 sm:grid-cols-2">
                <x-admin.input
                    label="Name"
                    name="name"
                    :value="old('name', $warehouse->name ?? '')"
                    placeholder="Dhaka Central Warehouse"
                    required
                    class="sm:col-span-2"
                />

                <x-admin.input
                    label="Code"
                    name="code"
                    :value="old('code', $warehouse->code ?? '')"
                    placeholder="DAC-01"
                    help="Short unique code used in inventory logs."
                    required
                />

                <x-admin.input
                    label="Sort order"
                    name="sort_order"
                    type="number"
                    min="0"
                    :value="old('sort_order', $warehouse->sort_order ?? 0)"
                    help="Lower numbers appear first in lists."
                />

                <x-admin.input
                    label="Address"
                    name="address"
                    :value="old('address', $warehouse->address ?? '')"
                    placeholder="1200 Logistics Parkway"
                    class="sm:col-span-2"
                />

                <x-admin.input
                    label="City"
                    name="city"
                    :value="old('city', $warehouse->city ?? '')"
                    placeholder="Dhaka"
                />

                <x-admin.input
                    label="State"
                    name="state"
                    :value="old('state', $warehouse->state ?? '')"
                    placeholder="Dhaka"
                />

                <x-admin.input
                    label="Country"
                    name="country"
                    :value="old('country', $warehouse->country ?? 'BD')"
                    placeholder="BD"
                    help="Two-letter country code."
                    class="sm:col-span-2"
                />
            </div>
        </x-admin.form-card>
    </div>

    <div class="space-y-6">
        <x-admin.form-card title="Settings" description="Control availability and default fulfillment location.">
            <div class="space-y-4">
                <label class="flex items-center gap-3">
                    <input type="hidden" name="is_active" value="0">
                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        @checked(filter_var($isActive, FILTER_VALIDATE_BOOLEAN))
                        class="size-4 rounded border admin-border accent-admin-brand admin-focus-ring"
                    >
                    <span class="text-sm admin-text">Active warehouse</span>
                </label>

                <label class="flex items-center gap-3">
                    <input type="hidden" name="is_default" value="0">
                    <input
                        type="checkbox"
                        name="is_default"
                        value="1"
                        @checked(filter_var($isDefault, FILTER_VALIDATE_BOOLEAN))
                        class="size-4 rounded border admin-border accent-admin-brand admin-focus-ring"
                    >
                    <span class="text-sm admin-text">Default warehouse</span>
                </label>
                <p class="text-xs admin-muted">New product stock and product-form stock updates use the default warehouse.</p>
            </div>
        </x-admin.form-card>
    </div>
</div>
