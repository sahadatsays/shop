<?php

namespace App\Enums;

enum OrderSource: string
{
    case Website = 'website';
    case Admin = 'admin';
    case Phone = 'phone';
    case Facebook = 'facebook';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Website => 'Website',
            self::Admin => 'Admin',
            self::Phone => 'Phone',
            self::Facebook => 'Facebook',
            self::Other => 'Other',
        };
    }
}
