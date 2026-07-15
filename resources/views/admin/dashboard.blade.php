<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header title="Dashboard" description="Overview of store performance, orders, and support activity.">
        <x-slot:actions>
            <x-admin.button variant="secondary" size="sm" data-palette-open>Browse actions</x-admin.button>
            <x-admin.button size="sm" onclick="window.adminToast?.push({ title: 'Report exported', message: 'Dashboard summary downloaded.', type: 'success' })">
                Export summary
            </x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    {{-- KPI row --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-admin.stat-card class="admin-fade-up admin-stagger-1" label="Revenue (30d)" value="$48,290" change="+12.4% vs prior period" trend="up" icon="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
        <x-admin.stat-card class="admin-fade-up admin-stagger-2" label="Orders" value="1,284" change="+86 this week" trend="up" icon="M6 7h12l1.2 12.2a1.5 1.5 0 0 1-1.5 1.8H6.3a1.5 1.5 0 0 1-1.5-1.8L6 7Z"/>
        <x-admin.stat-card class="admin-fade-up admin-stagger-3" label="Avg. order value" value="$142.80" change="-2.1% vs prior period" trend="down" icon="M3 3v18h18M7 16l4-4 4 4 5-6"/>
        <x-admin.stat-card class="admin-fade-up admin-stagger-4" label="Open tickets" value="14" change="3 urgent" trend="neutral" icon="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.4-4 8-9 8a9.8 9.8 0 0 1-4-.8L3 21l1.8-4.2A8.8 8.8 0 0 1 3 12c0-4.4 4-8 9-8s9 3.6 9 8Z"/>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        {{-- Chart --}}
        <x-admin.card title="Revenue trend" class="xl:col-span-2 admin-fade-up admin-stagger-5">
            <div class="flex h-56 items-end gap-2 sm:gap-3" aria-hidden="true">
                @foreach ([38, 52, 44, 61, 57, 72, 68, 80, 74, 88, 92, 85] as $index => $height)
                    <div class="admin-bar-grow flex flex-1 flex-col justify-end rounded-t bg-admin-accent-muted" style="height: 100%; animation-delay: {{ 0.3 + $index * 0.05 }}s">
                        <div class="rounded-t bg-admin-brand transition-all duration-300 hover:opacity-80" style="height: {{ $height }}%"></div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 flex items-center justify-between">
                <p class="text-xs admin-muted">Demo chart — connect analytics module for live data.</p>
                <div class="flex items-center gap-3 text-xs admin-muted">
                    <span class="flex items-center gap-1.5"><span class="inline-block size-2 rounded-sm bg-admin-brand"></span>Revenue</span>
                </div>
            </div>
        </x-admin.card>

        {{-- Quick stats --}}
        <x-admin.card title="Today" class="admin-fade-up admin-stagger-6">
            <dl class="space-y-4">
                @foreach ([
                    ['label' => 'New orders', 'value' => '23'],
                    ['label' => 'Pending fulfillment', 'value' => '8'],
                    ['label' => 'Veteran discounts applied', 'value' => '11'],
                    ['label' => 'Impact fund contribution', 'value' => '$412'],
                ] as $row)
                    <div class="flex items-center justify-between gap-3 rounded-[var(--radius-admin)] px-2 py-1.5 transition-colors duration-150 hover:bg-admin-accent-muted/40">
                        <dt class="text-sm admin-muted">{{ $row['label'] }}</dt>
                        <dd class="text-sm font-semibold admin-text">{{ $row['value'] }}</dd>
                    </div>
                @endforeach
            </dl>
        </x-admin.card>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
        {{-- Recent orders --}}
        <div class="admin-fade-up" style="animation-delay: 0.35s">
            <x-admin.data-table>
                <x-slot:header>
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-sm font-semibold admin-text">Recent orders</h2>
                        <x-admin.badge variant="muted">Demo</x-admin.badge>
                    </div>
                </x-slot:header>
                <thead class="border-b admin-border bg-admin-bg/60 text-xs uppercase tracking-wide admin-muted">
                    <tr>
                        <th scope="col" class="px-4 py-3 font-medium">Order</th>
                        <th scope="col" class="px-4 py-3 font-medium">Customer</th>
                        <th scope="col" class="px-4 py-3 font-medium">Total</th>
                        <th scope="col" class="px-4 py-3 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y admin-border">
                    @foreach ([
                        ['id' => '#4821', 'customer' => 'Marcus T.', 'total' => '$189.00', 'status' => 'Processing', 'variant' => 'warning'],
                        ['id' => '#4819', 'customer' => 'Elena R.', 'total' => '$246.50', 'status' => 'Shipped', 'variant' => 'success'],
                        ['id' => '#4815', 'customer' => 'Chris W.', 'total' => '$92.00', 'status' => 'Delivered', 'variant' => 'success'],
                        ['id' => '#4812', 'customer' => 'Sam K.', 'total' => '$318.00', 'status' => 'Pending', 'variant' => 'default'],
                    ] as $order)
                        <tr class="admin-table-row hover:bg-admin-accent-muted/30">
                            <td class="sticky left-0 bg-inherit px-4 py-3 font-medium admin-text">{{ $order['id'] }}</td>
                            <td class="px-4 py-3 admin-text-secondary">{{ $order['customer'] }}</td>
                            <td class="px-4 py-3 admin-text-secondary">{{ $order['total'] }}</td>
                            <td class="px-4 py-3"><x-admin.badge :variant="$order['variant']">{{ $order['status'] }}</x-admin.badge></td>
                        </tr>
                    @endforeach
                </tbody>
            </x-admin.data-table>
        </div>

        {{-- Support queue --}}
        <div class="admin-fade-up" style="animation-delay: 0.4s">
            <x-admin.card title="Support queue">
                <ul class="divide-y admin-border">
                    @foreach ([
                        ['subject' => 'Order delivery delay', 'priority' => 'High', 'time' => '18m ago'],
                        ['subject' => 'Veteran ID verification', 'priority' => 'Medium', 'time' => '42m ago'],
                        ['subject' => 'Return request — Field Jacket', 'priority' => 'Low', 'time' => '2h ago'],
                    ] as $ticket)
                        <li class="flex items-start justify-between gap-3 rounded-[var(--radius-admin)] px-1 py-3 transition-colors duration-150 first:pt-0 last:pb-0 hover:bg-admin-accent-muted/30">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium admin-text">{{ $ticket['subject'] }}</p>
                                <p class="text-xs admin-muted">{{ $ticket['time'] }}</p>
                            </div>
                            <x-admin.badge :variant="$ticket['priority'] === 'High' ? 'danger' : ($ticket['priority'] === 'Medium' ? 'warning' : 'muted')">{{ $ticket['priority'] }}</x-admin.badge>
                        </li>
                    @endforeach
                </ul>
                <div class="mt-4">
                    <x-admin.button variant="ghost" size="sm" onclick="window.adminModal?.open({ title: 'Open support module?', body: 'The support queue module is not connected yet.', confirmLabel: 'Got it' })">
                        View all tickets
                    </x-admin.button>
                </div>
            </x-admin.card>
        </div>
    </div>

    {{-- State primitives demo --}}
    <div class="mt-8 grid gap-4 lg:grid-cols-3">
        <div class="admin-fade-up" style="animation-delay: 0.45s">
            <x-admin.card title="Skeleton" :padding="true">
                <x-admin.skeleton :lines="4" />
            </x-admin.card>
        </div>
        <div class="admin-fade-up" style="animation-delay: 0.5s">
            <x-admin.card title="Empty state" :padding="true">
                <x-admin.empty-state title="No products yet" description="Create your first catalog item when the products module ships." action-label="Create product" />
            </x-admin.card>
        </div>
        <div class="admin-fade-up" style="animation-delay: 0.55s">
            <x-admin.card title="Error state" :padding="true">
                <x-admin.error-state>
                    <x-slot:actions>
                        <x-admin.button size="sm" variant="secondary">Retry</x-admin.button>
                    </x-slot:actions>
                </x-admin.error-state>
            </x-admin.card>
        </div>
    </div>
</x-layouts.admin>
