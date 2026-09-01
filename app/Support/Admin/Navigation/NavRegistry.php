<?php

namespace App\Support\Admin\Navigation;

use App\Models\User;

class NavRegistry
{
    /**
     * @return array<int, NavItem>
     */
    public static function sidebar(): array
    {
        return [
            new NavItem(
                label: 'Dashboard',
                route: 'admin.dashboard',
                icon: 'M3 11 12 3l9 8M6 10v10h12V10',
                permission: 'dashboard.view',
            ),
            new NavItem(
                label: 'Catalog',
                icon: 'M16 3l5 3-2 5-2-1v10a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1V10l-2 1-2-5 5-3a4 4 0 0 0 8 0Z',
                children: [
                    new NavItem(label: 'Products', route: 'admin.products.index', routePrefix: 'admin.products.', permission: 'products.view'),
                    new NavItem(label: 'Categories', route: 'admin.categories.index', routePrefix: 'admin.categories.', permission: 'categories.view'),
                    new NavItem(label: 'Brands', route: 'admin.brands.index', routePrefix: 'admin.brands.', permission: 'brands.view'),
                    new NavItem(label: 'Collections', route: 'admin.collections.index', routePrefix: 'admin.collections.', permission: 'collections.view'),
                    new NavItem(label: 'Inventory', route: 'admin.inventory.index', routePrefix: 'admin.inventory.', permission: 'inventory.view'),
                    new NavItem(label: 'Warehouses', route: 'admin.warehouses.index', routePrefix: 'admin.warehouses.', permission: 'warehouses.view'),
                ],
            ),
            new NavItem(
                label: 'Commerce',
                icon: 'M6 7h12l1.2 12.2a1.5 1.5 0 0 1-1.5 1.8H6.3a1.5 1.5 0 0 1-1.5-1.8L6 7ZM9 10V6a3 3 0 0 1 6 0v4',
                children: [
                    new NavItem(label: 'Orders', route: 'admin.orders.index', routePrefix: 'admin.orders.', permission: 'orders.view'),
                    new NavItem(label: 'Refunds', route: 'admin.refunds.index', routePrefix: 'admin.refunds.', permission: 'refunds.view'),
                    new NavItem(label: 'Coupons', disabled: true),
                    new NavItem(label: 'Shipping', disabled: true),
                ],
            ),
            new NavItem(
                label: 'Customers',
                icon: 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 7a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm11 4a3 3 0 1 0 0-6M22 21v-2a4 4 0 0 0-3-3.87',
                children: [
                    new NavItem(label: 'Customers', route: 'admin.customers.index', routePrefix: 'admin.customers.', permission: 'customers.view'),
                    new NavItem(label: 'Reviews', disabled: true),
                ],
            ),
            new NavItem(
                label: 'Veterans & Impact',
                icon: 'M12 2 4 5v6c0 5.25 3.4 9.74 8 11 4.6-1.26 8-5.75 8-11V5l-8-3Z',
                disabled: true,
                children: [
                    new NavItem(label: 'Discount verification', disabled: true),
                    new NavItem(label: 'Giving reports', disabled: true),
                ],
            ),
            new NavItem(
                label: 'Content',
                icon: 'M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2Z',
                children: [
                    new NavItem(label: 'Homepage settings', route: 'admin.homepage.settings.edit', routePrefix: 'admin.homepage.settings.', permission: 'homepage.view'),
                    new NavItem(label: 'Hero banners', route: 'admin.homepage.hero-banners.index', routePrefix: 'admin.homepage.hero-banners.', permission: 'homepage.view'),
                    new NavItem(label: 'Promo banners', route: 'admin.homepage.promo-banners.index', routePrefix: 'admin.homepage.promo-banners.', permission: 'homepage.view'),
                    new NavItem(label: 'Why shop features', route: 'admin.homepage.features.index', routePrefix: 'admin.homepage.features.', permission: 'homepage.view'),
                    new NavItem(label: 'Menus', route: 'admin.homepage.menus.index', routePrefix: 'admin.homepage.menus.', permission: 'homepage.view'),
                    new NavItem(label: 'Homepage reviews', route: 'admin.homepage.reviews.index', routePrefix: 'admin.homepage.reviews.', permission: 'homepage.view'),
                    new NavItem(label: 'Newsletter subscribers', route: 'admin.homepage.newsletter-subscribers.index', routePrefix: 'admin.homepage.newsletter-subscribers.', permission: 'homepage.view'),
                    new NavItem(label: 'Media library', route: 'admin.media.index', routePrefix: 'admin.media.', permission: 'media.view'),
                ],
            ),
            new NavItem(
                label: 'Engagement',
                icon: 'M6 9a6 6 0 0 1 12 0c0 5 2 6 2 6H4s2-1 2-6M10 19a2 2 0 0 0 4 0',
                children: [
                    new NavItem(label: 'Offers', route: 'admin.offers.index', routePrefix: 'admin.offers.', permission: 'offers.view'),
                    new NavItem(label: 'Discounts', route: 'admin.discounts.index', routePrefix: 'admin.discounts.', permission: 'discounts.view'),
                    new NavItem(label: 'Sale products', route: 'admin.sale-products.index', routePrefix: 'admin.sale-products.', permission: 'sale-products.view'),
                    new NavItem(label: 'Collections', route: 'admin.collections.index', routePrefix: 'admin.collections.', permission: 'collections.view'),
                    new NavItem(label: 'Banner promotions', route: 'admin.banner-promotions.index', routePrefix: 'admin.banner-promotions.', permission: 'promotions.view'),
                    new NavItem(label: 'Countdown promotions', route: 'admin.countdown-promotions.index', routePrefix: 'admin.countdown-promotions.', permission: 'promotions.view'),
                ],
            ),
            new NavItem(
                label: 'Insights',
                icon: 'M3 3v18h18M7 16l4-4 4 4 5-6',
                disabled: true,
            ),
            new NavItem(
                label: 'System',
                icon: 'M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm7.5-3a7.5 7.5 0 0 0-.1-1.2l2-1.6-2-3.4-2.4 1a7.6 7.6 0 0 0-2-1.2L14.6 3h-4l-.4 2.6a7.6 7.6 0 0 0-2 1.2l-2.4-1-2 3.4 2 1.6a7.7 7.7 0 0 0 0 2.4l-2 1.6 2 3.4 2.4-1a7.6 7.6 0 0 0 2 1.2l.4 2.6h4l.4-2.6a7.6 7.6 0 0 0 2-1.2l2.4 1 2-3.4-2-1.6c.06-.4.1-.8.1-1.2Z',
                children: [
                    new NavItem(label: 'Store settings', route: 'admin.settings.edit', routePrefix: 'admin.settings.', permission: 'settings.view'),
                    new NavItem(label: 'Dashboard widgets', route: 'admin.dashboard-widgets.index', routePrefix: 'admin.dashboard-widgets.', permission: 'dashboard.widgets.view'),
                    new NavItem(label: 'Notifications', route: 'admin.notifications.index', routePrefix: 'admin.notifications.', permission: 'notifications.view'),
                    new NavItem(label: 'Audit logs', route: 'admin.audit-logs.index', routePrefix: 'admin.audit-logs.', permission: 'audit.view'),
                    new NavItem(label: 'Admin users', route: 'admin.users.index', routePrefix: 'admin.users.', permission: 'users.view'),
                    new NavItem(label: 'Roles', route: 'admin.roles.index', routePrefix: 'admin.roles.', permission: 'roles.view'),
                    new NavItem(label: 'Permissions', route: 'admin.permissions.index', routePrefix: 'admin.permissions.', permission: 'permissions.view'),
                    new NavItem(label: 'Permission matrix', route: 'admin.roles.matrix', permission: 'access-matrix.manage'),
                ],
            ),
        ];
    }

    /**
     * @return array<int, NavItem>
     */
    public static function sidebarFor(?User $user): array
    {
        return self::filterItems(self::sidebar(), $user);
    }

    /**
     * @param  array<int, NavItem>  $items
     * @return array<int, NavItem>
     */
    private static function filterItems(array $items, ?User $user): array
    {
        $filtered = [];

        foreach ($items as $item) {
            if ($item->children !== null) {
                $children = self::filterItems($item->children, $user);

                if ($children === []) {
                    continue;
                }

                $filtered[] = new NavItem(
                    label: $item->label,
                    route: $item->route,
                    routePrefix: $item->routePrefix,
                    icon: $item->icon,
                    badge: $item->badge,
                    disabled: $item->disabled,
                    children: $children,
                    paletteGroup: $item->paletteGroup,
                    permission: $item->permission,
                );

                continue;
            }

            if ($item->disabled) {
                $filtered[] = $item;

                continue;
            }

            if ($item->permission !== null && ($user === null || ! $user->hasPermission($item->permission))) {
                continue;
            }

            $filtered[] = $item;
        }

        return $filtered;
    }

    /**
     * @return array<int, array{group: string, items: array<int, array{label: string, href: ?string, keywords: string}>}>
     */
    public static function commandPalette(): array
    {
        return [
            [
                'group' => 'Navigate',
                'items' => [
                    ['label' => 'Dashboard', 'href' => route('admin.dashboard'), 'keywords' => 'home overview'],
                    ['label' => 'Products', 'href' => route('admin.products.index'), 'keywords' => 'catalog inventory sku'],
                    ['label' => 'Inventory', 'href' => route('admin.inventory.index'), 'keywords' => 'stock warehouse movement'],
                    ['label' => 'Warehouses', 'href' => route('admin.warehouses.index'), 'keywords' => 'stock fulfillment location distribution'],
                    ['label' => 'Stock history', 'href' => route('admin.inventory.movements'), 'keywords' => 'inventory log movement audit'],
                    ['label' => 'Customers', 'href' => route('admin.customers.index'), 'keywords' => 'users accounts profiles'],
                    ['label' => 'Orders', 'href' => route('admin.orders.index'), 'keywords' => 'commerce sales fulfillment'],
                    ['label' => 'Refunds', 'href' => route('admin.refunds.index'), 'keywords' => 'commerce returns payments'],
                    ['label' => 'Categories', 'href' => route('admin.categories.index'), 'keywords' => 'catalog organize'],
                    ['label' => 'Brands', 'href' => route('admin.brands.index'), 'keywords' => 'catalog brand logo'],
                    ['label' => 'Store settings', 'href' => route('admin.settings.edit'), 'keywords' => 'store configuration logo seo maintenance theme'],
                    ['label' => 'Media library', 'href' => route('admin.media.index'), 'keywords' => 'upload images files assets media'],
                    ['label' => 'Admin users', 'href' => route('admin.users.index'), 'keywords' => 'system staff team access'],
                    ['label' => 'Roles', 'href' => route('admin.roles.index'), 'keywords' => 'system access security'],
                    ['label' => 'Permissions', 'href' => route('admin.permissions.index'), 'keywords' => 'system access security'],
                    ['label' => 'Permission matrix', 'href' => route('admin.roles.matrix'), 'keywords' => 'system access security matrix'],
                ],
            ],
            [
                'group' => 'Actions',
                'items' => [
                    ['label' => 'Create product', 'href' => route('admin.products.create'), 'keywords' => 'new catalog add'],
                    ['label' => 'Create category', 'href' => route('admin.categories.create'), 'keywords' => 'new catalog category'],
                    ['label' => 'Create brand', 'href' => route('admin.brands.create'), 'keywords' => 'new catalog brand'],
                    ['label' => 'Create customer', 'href' => route('admin.customers.create'), 'keywords' => 'new user account'],
                    ['label' => 'View orders', 'href' => route('admin.orders.index'), 'keywords' => 'commerce sales'],
                    ['label' => 'Export report', 'href' => null, 'keywords' => 'download csv'],
                ],
            ],
        ];
    }

    /**
     * @return array<int, array{label: string, href: ?string, icon: string}>
     */
    public static function quickActions(): array
    {
        return [
            ['label' => 'Add Product', 'href' => route('admin.products.create'), 'icon' => 'M12 5v14M5 12h14'],
            ['label' => 'Manage Brands', 'href' => route('admin.brands.index'), 'icon' => 'M12 2 4 5v6c0 5.25 3.4 9.74 8 11 4.6-1.26 8-5.75 8-11V5l-8-3Z'],
            ['label' => 'Manage Customers', 'href' => route('admin.customers.index'), 'icon' => 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 7a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z'],
            ['label' => 'View Reports', 'href' => null, 'icon' => 'M3 3v18h18M7 16l4-4 4 4 5-6'],
        ];
    }
}
