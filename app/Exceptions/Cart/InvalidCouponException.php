<?php

namespace App\Exceptions\Cart;

use Exception;

class InvalidCouponException extends Exception
{
    public function __construct(string $message = 'That coupon code is not valid.')
    {
        parent::__construct($message);
    }
}
