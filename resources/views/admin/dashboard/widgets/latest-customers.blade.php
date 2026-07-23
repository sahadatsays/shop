@php
    $customers = $data['customers'] ?? collect();
@endphp

@if ($customers->isEmpty())
    <x-admin.empty-state title="No customers" description="New customers will appear here." />
@else
    <ul class="divide-y admin-border">
        @foreach ($customers as $customer)
            <li class="flex items-center justify-between gap-3 py-2.5">
                <div class="min-w-0">
                    <a href="{{ $customer['url'] }}" class="block truncate text-sm font-medium admin-text hover:text-admin-brand">{{ $customer['name'] }}</a>
                    <p class="truncate text-xs admin-muted">{{ $customer['email'] }}</p>
                </div>
                <span class="shrink-0 text-xs admin-muted">{{ $customer['joined'] }}</span>
            </li>
        @endforeach
    </ul>
@endif
