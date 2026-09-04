<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case BankTransfer = 'bank_transfer';
    case Card = 'card';
    case MobileBanking = 'mobile_banking';
    case Cod = 'cod';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Cash => 'Cash',
            self::BankTransfer => 'Bank transfer',
            self::Card => 'Card',
            self::MobileBanking => 'Mobile banking',
            self::Cod => 'Cash on delivery',
            self::Other => 'Other',
        };
    }
}
