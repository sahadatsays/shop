<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\NewsletterSubscriberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NewsletterSubscriberController extends Controller
{
    public function __construct(private NewsletterSubscriberService $subscribers) {}

    public function index(Request $request): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('homepage.view'), 403);

        return view('admin.homepage.newsletter-subscribers.index', [
            'title' => 'Newsletter Subscribers',
            'breadcrumbs' => [
                ['label' => 'Content'],
                ['label' => 'Newsletter Subscribers'],
            ],
            'subscribers' => $this->subscribers->list([
                'search' => $request->string('search')->toString() ?: null,
                'status' => $request->string('status')->toString() ?: null,
            ]),
            'filters' => $request->only(['search', 'status']),
        ]);
    }
}
