<?php

namespace App\View\Composers;

use App\Services\CustomerAuthService;
use Illuminate\View\View;

class CustomerAccountComposer
{
    public function __construct(private CustomerAuthService $auth) {}

    public function compose(View $view): void
    {
        $view->with('accountCustomer', $this->auth->currentCustomer());
    }
}
