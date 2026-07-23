<?php

namespace App\Enums;

enum NotificationCategory: string
{
    case OrderUpdate = 'order_update';
    case SystemAlert = 'system_alert';
    case Promotion = 'promotion';
    case Inventory = 'inventory';
    case Account = 'account';

    public function label(): string
    {
        return match ($this) {
            self::OrderUpdate => 'Order update',
            self::SystemAlert => 'System alert',
            self::Promotion => 'Promotion',
            self::Inventory => 'Inventory',
            self::Account => 'Account',
        };
    }

    public function customerFilterLabel(): string
    {
        return match ($this) {
            self::OrderUpdate => 'Orders',
            self::Promotion => 'Promotions',
            self::Account => 'Account',
            self::SystemAlert, self::Inventory => 'Account',
        };
    }

    public function customerType(): string
    {
        return match ($this) {
            self::OrderUpdate => 'orders',
            self::Promotion => 'promotions',
            default => 'account',
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::OrderUpdate => 'navy',
            self::Promotion => 'bronze',
            self::Inventory => 'warning',
            self::SystemAlert, self::Account => 'neutral',
        };
    }
}
