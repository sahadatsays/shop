@props([
    'orders',
])

<x-admin.data-table {{ $attributes }}>
    <x-slot:header>
        <h2 class="text-sm font-semibold admin-text">Recent Orders</h2>
    </x-slot:header>
    <thead class="border-b admin-border bg-admin-bg/60 text-xs uppercase tracking-wide admin-muted">
        <tr>
            <th scope="col" class="px-4 py-3 font-medium">Order</th>
            <th scope="col" class="hidden px-4 py-3 font-medium sm:table-cell">Customer</th>
            <th scope="col" class="px-4 py-3 font-medium">Total</th>
            <th scope="col" class="px-4 py-3 font-medium">Status</th>
        </tr>
    </thead>
    <tbody class="divide-y admin-border">
        @forelse ($orders as $order)
            <tr class="admin-table-row hover:bg-admin-accent-muted/30">
                <td class="px-4 py-3 font-medium admin-text">{{ $order->orderNumber }}</td>
                <td class="hidden px-4 py-3 admin-text-secondary sm:table-cell">{{ $order->customerName }}</td>
                <td class="px-4 py-3 admin-text-secondary">{{ $order->totalFormatted }}</td>
                <td class="px-4 py-3">
                    <x-admin.badge :variant="$order->statusVariant">{{ $order->status }}</x-admin.badge>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="px-4 py-8">
                    <x-admin.empty-state title="No orders yet" description="Orders will appear here once customers start purchasing." />
                </td>
            </tr>
        @endforelse
    </tbody>
</x-admin.data-table>
