@props([
    'customers',
])

<x-admin.card title="Latest Customers" {{ $attributes }}>
    <ul class="divide-y admin-border">
        @forelse ($customers as $customer)
            <li class="flex items-center justify-between gap-3 py-3 transition-colors duration-150 first:pt-0 last:pb-0 hover:bg-admin-accent-muted/30">
                <div class="min-w-0">
                    <a href="{{ route('admin.customers.show', $customer->customerId) }}" class="truncate text-sm font-medium admin-text hover:text-admin-brand">{{ $customer->name }}</a>
                    <p class="truncate text-xs admin-muted">{{ $customer->email }}</p>
                </div>
                <div class="shrink-0 text-right">
                    <p class="text-xs font-medium admin-text-secondary">{{ $customer->orderCount }} orders</p>
                    <p class="text-[11px] admin-muted">{{ $customer->joinedAt }}</p>
                </div>
            </li>
        @empty
            <li class="py-6">
                <x-admin.empty-state title="No customers yet" description="New customer signups will show here." />
            </li>
        @endforelse
    </ul>
</x-admin.card>
