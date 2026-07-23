<?php

namespace App\Exceptions\Wishlist;

use Exception;

class WishlistValidationException extends Exception
{
    /**
     * @param  array<int, string>  $errors
     */
    public function __construct(
        string $message,
        public array $errors = [],
    ) {
        parent::__construct($message);
    }
}
