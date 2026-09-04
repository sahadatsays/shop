<?php

namespace App\Enums;

enum PurchaseStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Approved = 'approved';
    case PartiallyReceived = 'partially_received';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Submitted => 'Submitted',
            self::Approved => 'Approved',
            self::PartiallyReceived => 'Partially received',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::Draft => 'muted',
            self::Submitted => 'info',
            self::Approved => 'brand',
            self::PartiallyReceived => 'warning',
            self::Completed => 'success',
            self::Cancelled => 'danger',
        };
    }

    public function isEditable(): bool
    {
        return $this === self::Draft;
    }

    public function canSubmit(): bool
    {
        return $this === self::Draft;
    }

    public function canApprove(): bool
    {
        return in_array($this, [self::Draft, self::Submitted], true);
    }

    public function canReceive(): bool
    {
        return in_array($this, [self::Approved, self::PartiallyReceived], true);
    }

    public function canCancel(): bool
    {
        return in_array($this, [self::Draft, self::Submitted, self::Approved], true);
    }

    public function affectsInventory(): bool
    {
        return in_array($this, [self::PartiallyReceived, self::Completed], true);
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
