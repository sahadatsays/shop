<?php

namespace App\Support;

use Illuminate\Support\Str;

class SlugGenerator
{
    public static function from(string $value): string
    {
        return Str::slug($value);
    }

    public static function unique(string $base, callable $exists): string
    {
        $slug = self::from($base);
        $original = $slug;
        $counter = 1;

        while ($exists($slug)) {
            $slug = $original.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
