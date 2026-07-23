@php
    use App\Support\MoneyFormatter;
@endphp

<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.adminToast?.push({ title: @json(session('success')), type: 'success' });
            });
        </script>
    @endif

    <x-admin.page-header :title="$customer->name" description="Customer profile, addresses, notes, and order history.">
        <x-slot:actions>
            <x-admin.button variant="secondary" size="sm" :href="route('admin.customers.index')">Back</x-admin.button>
            <x-admin.button size="sm" :href="route('admin.customers.edit', $customer)">Edit customer</x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="mb-6 grid gap-4 sm:grid-cols-3">
        <x-admin.stat-card label="Orders" :value="(string) $customer->orders_count" />
        <x-admin.stat-card label="Lifetime spent" :value="MoneyFormatter::format((int) ($customer->orders_sum_total_cents ?? 0))" />
        <x-admin.stat-card label="Status" :value="$customer->status->label()" />
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <x-admin.form-card title="Profile">
                <dl>
                    <x-admin.detail-row label="Name" :value="$customer->name" />
                    <x-admin.detail-row label="Email" :value="$customer->email" />
                    <x-admin.detail-row label="Phone" :value="$customer->phone" />
                    <x-admin.detail-row label="Status">
                        <x-admin.badge :variant="$customer->status->badgeVariant()" dot>{{ $customer->status->label() }}</x-admin.badge>
                    </x-admin.detail-row>
                    <x-admin.detail-row label="Joined" :value="$customer->created_at?->format('M j, Y g:i A')" />
                    <x-admin.detail-row label="Internal notes" :value="$customer->internal_notes" />
                </dl>
            </x-admin.form-card>

            <x-admin.form-card title="Addresses">
                @forelse ($customer->addresses as $address)
                    <div @class(['border-t admin-border pt-4 mt-4' => ! $loop->first])>
                        <div class="mb-2 flex flex-wrap items-center gap-2">
                            <p class="text-sm font-medium admin-text">{{ $address->label ?: 'Address' }}</p>
                            <x-admin.badge variant="muted">{{ $address->type->label() }}</x-admin.badge>
                            @if ($address->is_default)
                                <x-admin.badge variant="brand">Default</x-admin.badge>
                            @endif
                        </div>
                        <p class="text-sm admin-text-secondary">{{ $address->name }}</p>
                        <p class="text-sm admin-text-secondary">{{ $address->formatted() }}</p>
                        @if ($address->phone)
                            <p class="mt-1 text-xs admin-muted">{{ $address->phone }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-sm admin-muted">No addresses on file.</p>
                @endforelse
            </x-admin.form-card>

            <x-admin.form-card title="Order history" description="Recent purchases linked to this customer.">
                @if ($customer->orders->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead>
                                <tr class="border-b admin-border bg-admin-bg/40">
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider admin-muted">Order</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider admin-muted">Placed</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider admin-muted">Total</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider admin-muted">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-admin-border/60">
                                @foreach ($customer->orders as $order)
                                    <tr>
                                        <td class="px-4 py-3 font-medium admin-text">
                                            <a href="{{ route('admin.orders.show', $order) }}" class="hover:text-admin-brand">{{ $order->order_number }}</a>
                                        </td>
                                        <td class="px-4 py-3 admin-text-secondary">{{ $order->placed_at?->format('M j, Y') }}</td>
                                        <td class="px-4 py-3 tabular-nums admin-text-secondary">{{ MoneyFormatter::format($order->total_cents) }}</td>
                                        <td class="px-4 py-3">
                                            <x-admin.badge :variant="$order->status->badgeVariant()">{{ $order->status->label() }}</x-admin.badge>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm admin-muted">No orders yet.</p>
                @endif
            </x-admin.form-card>
        </div>

        <div class="space-y-6">
            <x-admin.form-card title="Add note">
                <form method="POST" action="{{ route('admin.customers.notes.store', $customer) }}" class="space-y-4">
                    @csrf
                    <x-admin.textarea
                        label="Note"
                        name="body"
                        rows="4"
                        placeholder="Add a customer note…"
                        required
                    >{{ old('body') }}</x-admin.textarea>
                    <x-admin.button type="submit" size="sm" class="w-full">Save note</x-admin.button>
                </form>
            </x-admin.form-card>

            <x-admin.form-card title="Notes timeline">
                <ul class="space-y-4">
                    @forelse ($customer->notes as $note)
                        <li class="border-b admin-border pb-4 last:border-0 last:pb-0">
                            <p class="text-sm admin-text">{{ $note->body }}</p>
                            <p class="mt-1 text-xs admin-muted">
                                {{ $note->author_name ?: 'Admin' }} · {{ $note->created_at?->diffForHumans() }}
                            </p>
                        </li>
                    @empty
                        <li class="text-sm admin-muted">No notes yet.</li>
                    @endforelse
                </ul>
            </x-admin.form-card>

            <form method="POST" action="{{ route('admin.customers.destroy', $customer) }}" onsubmit="return confirm('Move this customer to trash?')">
                @csrf
                @method('DELETE')
                <x-admin.button type="submit" variant="danger-ghost" class="w-full">Move to trash</x-admin.button>
            </form>
        </div>
    </div>
</x-layouts.admin>
