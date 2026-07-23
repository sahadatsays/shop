<?php

namespace App\Enums;

enum AuthProvider: string
{
    case Google = 'google';
    case Facebook = 'facebook';
    case Apple = 'apple';
    case Github = 'github';
    case Microsoft = 'microsoft';

    public function label(): string
    {
        return match ($this) {
            self::Google => 'Google',
            self::Facebook => 'Facebook',
            self::Apple => 'Apple',
            self::Github => 'GitHub',
            self::Microsoft => 'Microsoft',
        };
    }

    public function isEnabled(): bool
    {
        return match ($this) {
            self::Google, self::Facebook => filled(config("services.{$this->value}.client_id")),
            default => false,
        };
    }
}
