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

    <x-admin.page-header :title="$order->order_number" description="Order details, lifecycle status, timeline, and admin notes.">
        <x-slot:actions>
            <x-admin.button variant="secondary" size="sm" :href="route('admin.orders.index')">Back</x-admin.button>
            <x-admin.button variant="secondary" size="sm" :href="route('admin.customers.show', $order->customer)">View customer</x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="mb-6 grid gap-4 sm:grid-cols-3">
        <x-admin.stat-card label="Items" :value="(string) $order->items->sum('quantity')" />
        <x-admin.stat-card label="Order total" :value="MoneyFormatter::format($order->total_cents)" />
        <x-admin.stat-card label="Status" :value="$order->status->label()" />
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <x-admin.form-card title="Order summary">
                <dl>
                    <x-admin.detail-row label="Order number">
                        <code class="rounded bg-admin-bg px-1.5 py-0.5 font-mono text-xs">{{ $order->order_number }}</code>
                    </x-admin.detail-row>
                    <x-admin.detail-row label="Customer">
                        <a href="{{ route('admin.customers.show', $order->customer) }}" class="text-sm font-medium admin-text hover:text-admin-brand">{{ $order->customer->name }}</a>
                    </x-admin.detail-row>
                    <x-admin.detail-row label="Email" :value="$order->customer->email" />
                    <x-admin.detail-row label="Placed" :value="$order->placed_at?->format('M j, Y g:i A')" />
                    <x-admin.detail-row label="Subtotal" :value="MoneyFormatter::format($order->subtotal_cents)" />
                    <x-admin.detail-row label="Total" :value="MoneyFormatter::format($order->total_cents)" />
                    <x-admin.detail-row label="Status">
                        <x-admin.badge :variant="$order->status->badgeVariant()" dot>{{ $order->status->label() }}</x-admin.badge>
                    </x-admin.detail-row>
                </dl>
            </x-admin.form-card>

            <x-admin.form-card title="Line items">
                @if ($order->items->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead>
                                <tr class="border-b admin-border bg-admin-bg/40">
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider admin-muted">Product</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider admin-muted">SKU</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider admin-muted">Qty</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider admin-muted">Unit</th>
                                    <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wider admin-muted">Line total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-admin-border/60">
                                @foreach ($order->items as $item)
                                    <tr>
                                        <td class="px-4 py-3 font-medium admin-text">{{ $item->product?->name ?? 'Product removed' }}</td>
                                        <td class="px-4 py-3 font-mono text-xs admin-text-secondary">{{ $item->product?->sku }}</td>
                                        <td class="px-4 py-3 tabular-nums admin-text-secondary">{{ $item->quantity }}</td>
                                        <td class="px-4 py-3 tabular-nums admin-text-secondary">{{ MoneyFormatter::format($item->unit_price_cents) }}</td>
                                        <td class="px-4 py-3 tabular-nums font-medium admin-text">{{ MoneyFormatter::format($item->line_total_cents) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm admin-muted">No line items recorded.</p>
                @endif
            </x-admin.form-card>

            <x-admin.form-card title="Order timeline" description="Status changes and fulfillment milestones.">
                <ul class="space-y-0">
                    @forelse ($order->timelineEvents as $event)
                        <li class="relative border-l-2 admin-border pb-6 pl-6 last:pb-0">
                            <span class="absolute -left-[9px] top-1 flex size-4 items-center justify-center rounded-full border-2 border-admin-brand bg-admin-surface"></span>
                            <div class="flex flex-wrap items-center gap-2">
                                <x-admin.badge :variant="$event->status->badgeVariant()" dot>{{ $event->status->label() }}</x-admin.badge>
                                <span class="text-xs admin-muted">{{ $event->created_at?->format('M j, Y g:i A') }}</span>
                            </div>
                            @if ($event->message)
                                <p class="mt-2 text-sm admin-text">{{ $event->message }}</p>
                            @endif
                            <p class="mt-1 text-xs admin-muted">{{ $event->author_name ?: 'Admin' }}</p>
                        </li>
                    @empty
                        <li class="text-sm admin-muted">No timeline events yet.</li>
                    @endforelse
                </ul>
            </x-admin.form-card>
        </div>

        <div class="space-y-6">
            <x-admin.form-card title="Update status">
                <form method="POST" action="{{ route('admin.orders.status.update', $order) }}" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <x-admin.select label="Status" name="status" required>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @selected(old('status', $order->status->value) === $status->value)>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </x-admin.select>
                    <x-admin.textarea
                        label="Timeline note"
                        name="message"
                        rows="3"
                        placeholder="Optional note for the timeline…"
                    >{{ old('message') }}</x-admin.textarea>
                    <x-admin.button type="submit" size="sm" class="w-full">Update status</x-admin.button>
                </form>
            </x-admin.form-card>

            <x-admin.form-card title="Add admin note">
                <form method="POST" action="{{ route('admin.orders.notes.store', $order) }}" class="space-y-4">
                    @csrf
                    <x-admin.textarea
                        label="Note"
                        name="body"
                        rows="4"
                        placeholder="Internal note for this order…"
                        required
                    >{{ old('body') }}</x-admin.textarea>
                    <x-admin.button type="submit" size="sm" class="w-full">Save note</x-admin.button>
                </form>
            </x-admin.form-card>

            <x-admin.form-card title="Admin notes">
                <ul class="space-y-4">
                    @forelse ($order->notes as $note)
                        <li class="border-b admin-border pb-4 last:border-0 last:pb-0">
                            <p class="text-sm admin-text">{{ $note->body }}</p>
                            <p class="mt-1 text-xs admin-muted">
                                {{ $note->author_name ?: 'Admin' }} · {{ $note->created_at?->diffForHumans() }}
                            </p>
                        </li>
                    @empty
                        <li class="text-sm admin-muted">No admin notes yet.</li>
                    @endforelse
                </ul>
            </x-admin.form-card>
        </div>
    </div>
</x-layouts.admin>
