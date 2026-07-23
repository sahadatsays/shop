<?php

namespace App\Exceptions\Compare;

use Exception;

class CompareValidationException extends Exception
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
