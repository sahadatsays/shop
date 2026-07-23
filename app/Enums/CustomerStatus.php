<?php

namespace App\Enums;

enum CustomerStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Suspended = 'suspended';
    case Blocked = 'blocked';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Inactive => 'Inactive',
            self::Suspended => 'Suspended',
            self::Blocked => 'Blocked',
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Inactive => 'muted',
            self::Suspended, self::Blocked => 'danger',
        };
    }

    public function canLogin(): bool
    {
        return $this === self::Active;
    }

    public function loginBlockedMessage(): string
    {
        return match ($this) {
            self::Suspended => 'Your account has been suspended. Please contact support for assistance.',
            self::Blocked => 'Your account has been blocked. Please contact support for assistance.',
            self::Inactive => 'Your account is inactive. Please contact support for assistance.',
            default => 'Your account is not active.',
        };
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
