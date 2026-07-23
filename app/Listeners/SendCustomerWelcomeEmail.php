<?php

namespace App\Listeners;

use App\Events\CustomerRegistered;

class SendCustomerWelcomeEmail
{
    public function handle(CustomerRegistered $event): void
    {
        //
    }
}
