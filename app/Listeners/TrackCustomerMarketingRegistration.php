<?php

namespace App\Listeners;

use App\Events\CustomerRegistered;
use App\Events\ProviderLinked;

class TrackCustomerMarketingRegistration
{
    public function handle(CustomerRegistered|ProviderLinked $event): void
    {
        //
    }
}
