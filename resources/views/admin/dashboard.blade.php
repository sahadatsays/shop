<x-layouts.admin :title="$title" :breadcrumbs="$breadcrumbs">
    <x-admin.page-header
        title="Dashboard"
        description="Business performance overview — sales, orders, inventory, and customer activity."
    >
        <x-slot:actions>
            <x-admin.button variant="secondary" size="sm" data-palette-open>Browse actions</x-admin.button>
            <x-admin.button
                size="sm"
                onclick="window.adminToast?.push({ title: 'Report exported', message: 'Dashboard summary downloaded.', type: 'success' })"
            >
                Export summary
            </x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.dashboard.stat-grid :stats="$dashboard->stats" class="admin-fade-up" />

    <div class="mt-6">
        <x-admin.dashboard.charts-grid :charts="$dashboard->charts" />
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <div class="admin-fade-up xl:col-span-2" style="animation-delay: 0.35s">
            <x-admin.dashboard.recent-orders :orders="$dashboard->recentOrders" />
        </div>
        <div class="admin-fade-up" style="animation-delay: 0.4s">
            <x-admin.dashboard.low-stock :products="$dashboard->lowStockProducts" />
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <div class="admin-fade-up" style="animation-delay: 0.45s">
            <x-admin.dashboard.latest-customers :customers="$dashboard->latestCustomers" />
        </div>
        <div class="admin-fade-up" style="animation-delay: 0.5s">
            <x-admin.dashboard.top-products :products="$dashboard->topProducts" />
        </div>
        <div class="admin-fade-up" style="animation-delay: 0.55s">
            <x-admin.dashboard.featured-brands :brands="$dashboard->featuredBrands" />
        </div>
    </div>

    <div class="mt-8">
        <x-admin.section title="Quick Actions" description="Common tasks to manage your store.">
            <x-admin.dashboard.quick-actions :actions="$dashboard->quickActions" />
        </x-admin.section>
    </div>
</x-layouts.admin>
