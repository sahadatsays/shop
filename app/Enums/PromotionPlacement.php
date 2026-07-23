<?php

namespace App\Enums;

enum PromotionPlacement: string
{
    case HomeHero = 'home_hero';
    case ShopTop = 'shop_top';
    case UtilityBar = 'utility_bar';

    public function label(): string
    {
        return match ($this) {
            self::HomeHero => 'Home hero',
            self::ShopTop => 'Shop top',
            self::UtilityBar => 'Utility bar',
        };
    }
}
