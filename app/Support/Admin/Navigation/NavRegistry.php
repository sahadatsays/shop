<?php

namespace App\Support\Admin\Navigation;

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
            ),
            new NavItem(
                label: 'Catalog',
                icon: 'M16 3l5 3-2 5-2-1v10a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1V10l-2 1-2-5 5-3a4 4 0 0 0 8 0Z',
                disabled: true,
                children: [
                    new NavItem(label: 'Products', disabled: true),
                    new NavItem(label: 'Categories', disabled: true),
                    new NavItem(label: 'Collections', disabled: true),
                    new NavItem(label: 'Inventory', disabled: true, badge: 'Soon'),
                ],
            ),
            new NavItem(
                label: 'Commerce',
                icon: 'M6 7h12l1.2 12.2a1.5 1.5 0 0 1-1.5 1.8H6.3a1.5 1.5 0 0 1-1.5-1.8L6 7ZM9 10V6a3 3 0 0 1 6 0v4',
                disabled: true,
                children: [
                    new NavItem(label: 'Orders', disabled: true),
                    new NavItem(label: 'Refunds', disabled: true),
                    new NavItem(label: 'Coupons', disabled: true),
                    new NavItem(label: 'Shipping', disabled: true),
                ],
            ),
            new NavItem(
                label: 'Customers',
                icon: 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 7a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm11 4a3 3 0 1 0 0-6M22 21v-2a4 4 0 0 0-3-3.87',
                disabled: true,
                children: [
                    new NavItem(label: 'Users', disabled: true),
                    new NavItem(label: 'Addresses', disabled: true),
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
                disabled: true,
            ),
            new NavItem(
                label: 'Engagement',
                icon: 'M6 9a6 6 0 0 1 12 0c0 5 2 6 2 6H4s2-1 2-6M10 19a2 2 0 0 0 4 0',
                disabled: true,
            ),
            new NavItem(
                label: 'Insights',
                icon: 'M3 3v18h18M7 16l4-4 4 4 5-6',
                disabled: true,
            ),
            new NavItem(
                label: 'System',
                icon: 'M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm7.5-3a7.5 7.5 0 0 0-.1-1.2l2-1.6-2-3.4-2.4 1a7.6 7.6 0 0 0-2-1.2L14.6 3h-4l-.4 2.6a7.6 7.6 0 0 0-2 1.2l-2.4-1-2 3.4 2 1.6a7.7 7.7 0 0 0 0 2.4l-2 1.6 2 3.4 2.4-1a7.6 7.6 0 0 0 2 1.2l.4 2.6h4l.4-2.6a7.6 7.6 0 0 0 2-1.2l2.4 1 2-3.4-2-1.6c.06-.4.1-.8.1-1.2Z',
                disabled: true,
            ),
        ];
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
                ],
            ],
            [
                'group' => 'Actions',
                'items' => [
                    ['label' => 'Create product', 'href' => null, 'keywords' => 'new catalog add'],
                    ['label' => 'View orders', 'href' => null, 'keywords' => 'commerce sales'],
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
            ['label' => 'Create product', 'href' => null, 'icon' => 'M12 5v14M5 12h14'],
            ['label' => 'View orders', 'href' => null, 'icon' => 'M6 7h12l1.2 12.2a1.5 1.5 0 0 1-1.5 1.8H6.3a1.5 1.5 0 0 1-1.5-1.8L6 7Z'],
            ['label' => 'Support queue', 'href' => null, 'icon' => 'M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.4-4 8-9 8a9.8 9.8 0 0 1-4-.8L3 21l1.8-4.2A8.8 8.8 0 0 1 3 12c0-4.4 4-8 9-8s9 3.6 9 8Z'],
            ['label' => 'Export data', 'href' => null, 'icon' => 'M12 3v12m0 0 4-4m-4 4-4-4M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2'],
        ];
    }
}
