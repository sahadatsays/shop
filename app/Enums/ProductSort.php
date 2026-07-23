<?php

namespace App\Enums;

enum ProductSort: string
{
    case Newest = 'newest';
    case Oldest = 'oldest';
    case PriceLow = 'price_low';
    case PriceHigh = 'price_high';
    case Featured = 'featured';
    case NameAsc = 'name_asc';
    case NameDesc = 'name_desc';
    case BestSelling = 'best_selling';
    case MostViewed = 'most_viewed';

    public function label(): string
    {
        return match ($this) {
            self::Newest => 'Newest',
            self::Oldest => 'Oldest',
            self::PriceLow => 'Price: low to high',
            self::PriceHigh => 'Price: high to low',
            self::Featured => 'Featured',
            self::NameAsc => 'Name A–Z',
            self::NameDesc => 'Name Z–A',
            self::BestSelling => 'Best selling',
            self::MostViewed => 'Most viewed',
        };
    }

    public function isFutureReady(): bool
    {
        return match ($this) {
            self::BestSelling, self::MostViewed => true,
            default => false,
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
