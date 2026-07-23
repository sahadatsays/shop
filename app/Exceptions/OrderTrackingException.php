<?php

namespace App\Exceptions;

use Exception;

class OrderTrackingException extends Exception
{
    public static function invalidCredentials(): self
    {
        return new self('We could not find an order matching that order number and email address.');
    }

    public static function unauthorized(): self
    {
        return new self('You do not have permission to view this order.');
    }
}
