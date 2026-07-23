<?php

namespace App\Exceptions\Cart;

use Exception;

class CartValidationException extends Exception
{
    /**
     * @param  array<int, string>  $errors
     */
    public function __construct(
        string $message = 'Your cart contains invalid items.',
        public readonly array $errors = [],
    ) {
        parent::__construct($message);
    }
}
