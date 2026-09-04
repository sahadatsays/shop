@php
    $customer = $form->customer;
    $addresses = old('addresses', $customer?->addresses->map(fn ($address) => [
        'label' => $address->label,
        'type' => $address->type->value,
        'name' => $address->name,
        'phone' => $address->phone,
        'line1' => $address->line1,
        'line2' => $address->line2,
        'city' => $address->city,
        'state' => $address->state,
        'postal_code' => $address->postal_code,
        'country' => $address->country,
        'is_default' => $address->is_default,
    ])->values()->all() ?? []);
@endphp

@if ($errors->any())
    <div class="mb-6 rounded-[var(--radius-admin-lg)] border border-admin-danger/30 bg-red-50 px-4 py-3 dark:bg-red-950/20" role="alert">
        <p class="text-sm font-medium text-admin-danger">Please fix the errors below and try again.</p>
    </div>
@endif

<form
    method="POST"
    action="{{ $action }}"
    x-data="customerForm(@js(['addresses' => $addresses]))"
    class="space-y-6"
>
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <x-admin.form-card title="Profile" description="Contact details and account status.">
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-admin.input
                        label="Full name"
                        name="name"
                        :value="old('name', $customer?->name)"
                        placeholder="Jordan Hale"
                        help="Customer display name."
                        required
                        class="sm:col-span-2"
                    />

                    <x-admin.input
                        label="Email"
                        name="email"
                        type="email"
                        :value="old('email', $customer?->email)"
                        placeholder="jordan@example.com"
                        help="Must be unique across customers."
                        required
                    />

                    <x-admin.input
                        label="Phone"
                        name="phone"
                        :value="old('phone', $customer?->phone)"
                        placeholder="+1 (555) 010-2000"
                        help="Optional contact number."
                    />

                    <x-admin.select label="Status" name="status" help="Suspended customers can be blocked from purchasing." required class="sm:col-span-2">
                        @foreach (\App\Enums\CustomerStatus::cases() as $status)
                            <option value="{{ $status->value }}" @selected(old('status', $customer?->status?->value ?? 'active') === $status->value)>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </x-admin.select>

                    <x-admin.textarea
                        label="Internal notes"
                        name="internal_notes"
                        rows="3"
                        placeholder="Standing preferences, VIP flags, account context…"
                        help="Private staff notes shown on the customer profile."
                        class="sm:col-span-2"
                    >{{ old('internal_notes', $customer?->internal_notes) }}</x-admin.textarea>
                </div>
            </x-admin.form-card>

            <x-admin.form-card title="Addresses" description="Shipping locations for this customer.">
                <div class="space-y-4">
                    <template x-for="(address, index) in addresses" :key="index">
                        <div class="rounded-[var(--radius-admin-lg)] border admin-border bg-admin-bg/40 p-4">
                            <div class="mb-4 flex items-center justify-between gap-3">
                                <p class="text-sm font-medium admin-text" x-text="`Address ${index + 1}`"></p>
                                <div class="flex items-center gap-2">
                                    <label class="inline-flex items-center gap-2 text-xs admin-muted">
                                        <input type="checkbox" :checked="address.is_default" @change="setDefault(index)" class="rounded border-admin-border text-admin-brand admin-focus-ring">
                                        Default
                                    </label>
                                    <input type="hidden" :name="`addresses[${index}][is_default]`" :value="address.is_default ? 1 : 0">
                                    <button type="button" @click="removeAddress(index)" class="rounded-[var(--radius-admin)] border admin-border px-2.5 py-1.5 text-xs admin-muted hover:admin-text admin-focus-ring">
                                        Remove
                                    </button>
                                </div>
                            </div>

                            <div class="grid gap-3 sm:grid-cols-2">
                                <input type="text" :name="`addresses[${index}][label]`" x-model="address.label" placeholder="Label (Home, Work)" class="block w-full rounded-[var(--radius-admin)] border admin-border bg-admin-surface px-3.5 py-2.5 text-sm admin-text placeholder:admin-muted admin-focus-ring">
                                <select :name="`addresses[${index}][type]`" x-model="address.type" class="block w-full rounded-[var(--radius-admin)] border admin-border bg-admin-surface px-3.5 py-2.5 text-sm admin-text admin-focus-ring">
                                    @foreach (\App\Enums\AddressType::cases() as $type)
                                        <option value="{{ $type->value }}">{{ $type->label() }}</option>
                                    @endforeach
                                </select>
                                <input type="text" :name="`addresses[${index}][name]`" x-model="address.name" placeholder="Recipient name" class="block w-full rounded-[var(--radius-admin)] border admin-border bg-admin-surface px-3.5 py-2.5 text-sm admin-text placeholder:admin-muted admin-focus-ring sm:col-span-2">
                                <input type="text" :name="`addresses[${index}][line1]`" x-model="address.line1" placeholder="Address line 1" class="block w-full rounded-[var(--radius-admin)] border admin-border bg-admin-surface px-3.5 py-2.5 text-sm admin-text placeholder:admin-muted admin-focus-ring sm:col-span-2">
                                <input type="text" :name="`addresses[${index}][line2]`" x-model="address.line2" placeholder="Address line 2" class="block w-full rounded-[var(--radius-admin)] border admin-border bg-admin-surface px-3.5 py-2.5 text-sm admin-text placeholder:admin-muted admin-focus-ring sm:col-span-2">
                                <input type="text" :name="`addresses[${index}][city]`" x-model="address.city" placeholder="City" class="block w-full rounded-[var(--radius-admin)] border admin-border bg-admin-surface px-3.5 py-2.5 text-sm admin-text placeholder:admin-muted admin-focus-ring">
                                <input type="text" :name="`addresses[${index}][state]`" x-model="address.state" placeholder="State" class="block w-full rounded-[var(--radius-admin)] border admin-border bg-admin-surface px-3.5 py-2.5 text-sm admin-text placeholder:admin-muted admin-focus-ring">
                                <input type="text" :name="`addresses[${index}][postal_code]`" x-model="address.postal_code" placeholder="Postal code" class="block w-full rounded-[var(--radius-admin)] border admin-border bg-admin-surface px-3.5 py-2.5 text-sm admin-text placeholder:admin-muted admin-focus-ring">
                                <input type="text" :name="`addresses[${index}][country]`" x-model="address.country" placeholder="Country" maxlength="2" class="block w-full rounded-[var(--radius-admin)] border admin-border bg-admin-surface px-3.5 py-2.5 text-sm admin-text placeholder:admin-muted admin-focus-ring">
                                <input type="text" :name="`addresses[${index}][phone]`" x-model="address.phone" placeholder="Phone" class="block w-full rounded-[var(--radius-admin)] border admin-border bg-admin-surface px-3.5 py-2.5 text-sm admin-text placeholder:admin-muted admin-focus-ring sm:col-span-2">
                            </div>
                        </div>
                    </template>

                    <x-admin.button type="button" variant="secondary" size="sm" @click="addAddress">Add address</x-admin.button>
                </div>
            </x-admin.form-card>
        </div>

        <div class="space-y-6">
            <x-admin.form-card title="Add note" description="Optional timeline note saved with this customer.">
                <x-admin.textarea
                    label="Note"
                    name="note"
                    rows="5"
                    placeholder="Called about shipping preference…"
                    help="Appears in the customer notes timeline."
                >{{ old('note') }}</x-admin.textarea>
            </x-admin.form-card>

            <div class="flex flex-col gap-2 sm:flex-row">
                <x-admin.button type="submit" class="flex-1">{{ $submitLabel }}</x-admin.button>
                <x-admin.button variant="secondary" :href="route('admin.customers.index')" class="flex-1">Cancel</x-admin.button>
            </div>
        </div>
    </div>
</form>
