@php
    $orders = $data['orders'] ?? collect();
@endphp

@if ($orders->isEmpty())
    <x-admin.empty-state title="No orders" description="No orders were placed in the selected range." />
@else
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="border-b admin-border text-xs uppercase tracking-wide admin-muted">
                <tr>
                    <th scope="col" class="py-2 pr-4 font-medium">Order</th>
                    <th scope="col" class="hidden py-2 pr-4 font-medium sm:table-cell">Customer</th>
                    <th scope="col" class="py-2 pr-4 font-medium">Total</th>
                    <th scope="col" class="py-2 pr-4 font-medium">Status</th>
                    <th scope="col" class="hidden py-2 font-medium md:table-cell">When</th>
                </tr>
            </thead>
            <tbody class="divide-y admin-border">
                @foreach ($orders as $order)
                    <tr class="admin-table-row hover:bg-admin-accent-muted/30">
                        <td class="py-2.5 pr-4 font-medium admin-text">
                            <a href="{{ $order['url'] }}" class="hover:text-admin-brand">{{ $order['number'] }}</a>
                        </td>
                        <td class="hidden py-2.5 pr-4 admin-text-secondary sm:table-cell">{{ $order['customer'] }}</td>
                        <td class="py-2.5 pr-4 admin-text-secondary">{{ $order['total'] }}</td>
                        <td class="py-2.5 pr-4"><x-admin.badge :variant="$order['status_variant']">{{ $order['status'] }}</x-admin.badge></td>
                        <td class="hidden py-2.5 text-xs admin-muted md:table-cell">{{ $order['placed_at'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
