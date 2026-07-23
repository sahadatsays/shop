<?php

namespace App\Http\Controllers;

use App\Services\Storefront\HomeService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(private HomeService $home) {}

    public function __invoke(): View
    {
        return view('home', $this->home->data());
    }
}
