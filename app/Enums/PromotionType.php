<?php

namespace App\Enums;

enum PromotionType: string
{
    case Banner = 'banner';
    case Countdown = 'countdown';

    public function label(): string
    {
        return match ($this) {
            self::Banner => 'Banner',
            self::Countdown => 'Countdown',
        };
    }
}
