<?php

namespace App\Enums;

enum AuditCategory: string
{
    case Auth = 'auth';
    case Product = 'product';
    case Inventory = 'inventory';
    case Order = 'order';
    case Customer = 'customer';
    case User = 'user';
    case System = 'system';

    public function label(): string
    {
        return match ($this) {
            self::Auth => 'Login history',
            self::Product => 'Product changes',
            self::Inventory => 'Stock changes',
            self::Order => 'Order changes',
            self::Customer => 'Customer changes',
            self::User => 'User activity',
            self::System => 'System',
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::Auth => 'info',
            self::Product => 'brand',
            self::Inventory => 'warning',
            self::Order => 'success',
            self::Customer => 'muted',
            self::User => 'info',
            self::System => 'danger',
        };
    }
}
