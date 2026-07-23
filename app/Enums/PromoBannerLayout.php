<?php

namespace App\Enums;

enum PromoBannerLayout: string
{
    case Single = 'single';
    case Double = 'double';
    case ThreeColumn = 'three_column';

    public function label(): string
    {
        return match ($this) {
            self::Single => 'Single banner',
            self::Double => 'Double banner',
            self::ThreeColumn => 'Three column',
        };
    }

    public function columnCount(): int
    {
        return match ($this) {
            self::Single => 1,
            self::Double => 2,
            self::ThreeColumn => 3,
        };
    }
}
