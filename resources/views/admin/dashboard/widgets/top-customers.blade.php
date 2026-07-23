@php
    $customers = $data['customers'] ?? collect();
@endphp

@if ($customers->isEmpty())
    <x-admin.empty-state title="No spend yet" description="No completed orders in this range to rank customers." />
@else
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="border-b admin-border text-xs uppercase tracking-wide admin-muted">
                <tr>
                    <th scope="col" class="py-2 pr-4 font-medium">Customer</th>
                    <th scope="col" class="py-2 pr-4 font-medium">Orders</th>
                    <th scope="col" class="py-2 font-medium">Spent</th>
                </tr>
            </thead>
            <tbody class="divide-y admin-border">
                @foreach ($customers as $customer)
                    <tr class="admin-table-row hover:bg-admin-accent-muted/30">
                        <td class="py-2.5 pr-4">
                            <span class="block font-medium admin-text">{{ $customer['name'] }}</span>
                            <span class="truncate text-xs admin-muted">{{ $customer['email'] }}</span>
                        </td>
                        <td class="py-2.5 pr-4 admin-text-secondary">{{ $customer['orders'] }}</td>
                        <td class="py-2.5 font-medium admin-text">{{ $customer['spent'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
