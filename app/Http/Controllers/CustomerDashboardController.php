<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\CustomerAuthService;
use App\Services\CustomerDashboardService;
use Illuminate\View\View;

class CustomerDashboardController extends Controller
{
    public function __construct(
        private CustomerAuthService $auth,
        private CustomerDashboardService $dashboard,
    ) {}

    public function __invoke(): View
    {
        $customer = $this->customer();

        return view('account', [
            'title' => $customer->name."'s Account",
            'dashboard' => $this->dashboard->data($customer),
        ]);
    }

    private function customer(): Customer
    {
        /** @var Customer $customer */
        $customer = $this->auth->currentCustomer();

        return $customer;
    }
}
