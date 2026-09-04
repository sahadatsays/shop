@php
    use App\Enums\SupplierStatus;

    $supplier = $supplier ?? null;
    $selectedStatus = old('status', $supplier?->status?->value ?? SupplierStatus::Active->value);
@endphp

@if ($errors->any())
    <div class="mb-6 rounded-[var(--radius-admin-lg)] border border-admin-danger/30 bg-red-50 px-4 py-3 dark:bg-red-950/20" role="alert">
        <p class="text-sm font-medium text-admin-danger">Please fix the errors below and try again.</p>
    </div>
@endif

<div class="grid gap-6 xl:grid-cols-3">
    <div class="space-y-6 xl:col-span-2">
        <x-admin.form-card title="Supplier details" description="Primary identity and contact information.">
            <div class="grid gap-5 sm:grid-cols-2">
                <x-admin.input
                    label="Supplier name"
                    name="name"
                    :value="old('name', $supplier->name ?? '')"
                    placeholder="Dhaka Agro Supplies"
                    required
                    class="sm:col-span-2"
                />

                <x-admin.input
                    label="Company name"
                    name="company_name"
                    :value="old('company_name', $supplier->company_name ?? '')"
                    placeholder="Dhaka Agro Supplies Ltd"
                    class="sm:col-span-2"
                />

                <x-admin.input
                    label="Contact person"
                    name="contact_person"
                    :value="old('contact_person', $supplier->contact_person ?? '')"
                    placeholder="Rahim Ahmed"
                />

                <x-admin.input
                    label="Phone"
                    name="phone"
                    :value="old('phone', $supplier->phone ?? '')"
                    placeholder="01700000000"
                />

                <x-admin.input
                    label="Email"
                    name="email"
                    type="email"
                    :value="old('email', $supplier->email ?? '')"
                    placeholder="orders@supplier.example"
                    class="sm:col-span-2"
                />
            </div>
        </x-admin.form-card>

        <x-admin.form-card title="Address" description="Location details for procurement and delivery coordination.">
            <div class="grid gap-5 sm:grid-cols-2">
                <x-admin.input
                    label="Address"
                    name="address"
                    :value="old('address', $supplier->address ?? '')"
                    placeholder="12 Warehouse Road"
                    class="sm:col-span-2"
                />

                <x-admin.input
                    label="City"
                    name="city"
                    :value="old('city', $supplier->city ?? '')"
                    placeholder="Dhaka"
                />

                <x-admin.input
                    label="District"
                    name="district"
                    :value="old('district', $supplier->district ?? '')"
                    placeholder="Dhaka"
                />

                <x-admin.input
                    label="Country"
                    name="country"
                    :value="old('country', $supplier->country ?? 'Bangladesh')"
                    placeholder="Bangladesh"
                    class="sm:col-span-2"
                />

                <x-admin.input
                    label="Tax / Business ID"
                    name="tax_id"
                    :value="old('tax_id', $supplier->tax_id ?? '')"
                    placeholder="TIN or business registration number"
                    class="sm:col-span-2"
                />
            </div>
        </x-admin.form-card>
    </div>

    <div class="space-y-6">
        <x-admin.form-card title="Status" description="Inactive suppliers cannot be selected for new purchases.">
            <x-admin.select label="Status" name="status" required>
                @foreach (SupplierStatus::cases() as $option)
                    <option value="{{ $option->value }}" @selected($selectedStatus === $option->value)>{{ $option->label() }}</option>
                @endforeach
            </x-admin.select>
        </x-admin.form-card>

        <x-admin.form-card title="Notes" description="Internal remarks for purchasing and accounts payable.">
            <x-admin.textarea
                label="Notes"
                name="notes"
                :value="old('notes', $supplier->notes ?? '')"
                placeholder="Payment terms, preferred contact hours, etc."
                rows="6"
            />
        </x-admin.form-card>
    </div>
</div>
