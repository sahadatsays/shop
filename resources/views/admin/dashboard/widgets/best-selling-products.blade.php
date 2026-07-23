@php
    $products = $data['products'] ?? collect();
@endphp

@if ($products->isEmpty())
    <x-admin.empty-state title="No sales yet" description="No completed orders in this range to rank products." />
@else
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="border-b admin-border text-xs uppercase tracking-wide admin-muted">
                <tr>
                    <th scope="col" class="py-2 pr-4 font-medium">Product</th>
                    <th scope="col" class="py-2 pr-4 font-medium">Units</th>
                    <th scope="col" class="py-2 font-medium">Revenue</th>
                </tr>
            </thead>
            <tbody class="divide-y admin-border">
                @foreach ($products as $product)
                    <tr class="admin-table-row hover:bg-admin-accent-muted/30">
                        <td class="py-2.5 pr-4">
                            <span class="block font-medium admin-text">{{ $product['name'] }}</span>
                            <span class="text-xs admin-muted">SKU {{ $product['sku'] }}</span>
                        </td>
                        <td class="py-2.5 pr-4 admin-text-secondary">{{ $product['units'] }}</td>
                        <td class="py-2.5 font-medium admin-text">{{ $product['revenue'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
