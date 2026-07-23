<?php

namespace App\Support\Dashboard\Widgets;

use App\Support\Dashboard\AbstractWidgetProvider;
use App\Support\Dashboard\WidgetContext;

class QuickActionsWidget extends AbstractWidgetProvider
{
    /**
     * Catalogue of quick actions, each gated by an optional permission so a
     * user only sees the shortcuts they are actually allowed to perform.
     *
     * @return array<int, array{label: string, route: ?string, permission: ?string, icon: string, description: string}>
     */
    private function catalogue(): array
    {
        return [
            ['label' => 'Add Product', 'route' => 'admin.products.create', 'permission' => 'products.manage', 'icon' => 'M12 5v14M5 12h14', 'description' => 'Create a new catalog item'],
            ['label' => 'Add Category', 'route' => 'admin.categories.create', 'permission' => 'categories.manage', 'icon' => 'M4 4h16v4H4zM4 10h10v10H4z', 'description' => 'Organize your catalog'],
            ['label' => 'Update Inventory', 'route' => 'admin.inventory.index', 'permission' => 'inventory.view', 'icon' => 'M20 7 12 3 4 7m16 0-8 4m8-4v10l-8 4m0-10L4 7', 'description' => 'Adjust stock levels'],
            ['label' => 'Create Offer', 'route' => 'admin.offers.create', 'permission' => 'offers.manage', 'icon' => 'M20 12v6a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-6M2 7h20v5H2zM12 22V7M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7Z', 'description' => 'Launch a promotion'],
            ['label' => 'Create Banner', 'route' => 'admin.homepage.hero-banners.create', 'permission' => 'homepage.manage', 'icon' => 'M3 5h18v14H3zM3 9h18M8 13h8', 'description' => 'Add a hero banner'],
            ['label' => 'Manage Orders', 'route' => 'admin.orders.index', 'permission' => 'orders.view', 'icon' => 'M6 7h12l1.2 12.2a1.5 1.5 0 0 1-1.5 1.8H6.3a1.5 1.5 0 0 1-1.5-1.8L6 7Z', 'description' => 'Fulfill and track orders'],
            ['label' => 'Manage Customers', 'route' => 'admin.customers.index', 'permission' => 'customers.view', 'icon' => 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 7a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z', 'description' => 'View customer records'],
            ['label' => 'View Reports', 'route' => null, 'permission' => null, 'icon' => 'M3 3v18h18M7 16l4-4 4 4 5-6', 'description' => 'Open analytics and exports'],
        ];
    }

    public function data(WidgetContext $context): array
    {
        $user = $context->user;

        $actions = collect($this->catalogue())
            ->filter(fn (array $action): bool => $action['permission'] === null || ($user?->hasPermission($action['permission']) ?? false))
            ->map(fn (array $action): array => [
                'label' => $action['label'],
                'href' => $action['route'] ? route($action['route']) : null,
                'icon' => $action['icon'],
                'description' => $action['description'],
            ])
            ->values()
            ->all();

        return ['actions' => $actions];
    }

    public function view(): string
    {
        return 'admin.dashboard.widgets.quick-actions';
    }

    public function cacheTtl(): int
    {
        return 0;
    }
}
