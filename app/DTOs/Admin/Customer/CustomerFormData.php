<?php

namespace App\DTOs\Admin\Customer;

use App\Models\Customer;

readonly class CustomerFormData
{
    public function __construct(
        public ?Customer $customer,
    ) {}
}
