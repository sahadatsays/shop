<?php

namespace App\Events;

use App\Enums\AuthProvider;
use App\Models\Customer;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProviderLinked
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Customer $customer,
        public AuthProvider $provider,
    ) {}
}
