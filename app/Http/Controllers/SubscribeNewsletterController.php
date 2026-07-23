<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubscribeNewsletterRequest;
use App\Services\NewsletterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class SubscribeNewsletterController extends Controller
{
    public function __construct(private NewsletterService $newsletter) {}

    public function __invoke(SubscribeNewsletterRequest $request): JsonResponse|RedirectResponse
    {
        $result = $this->newsletter->subscribe($request->validated('email'));

        $message = $result['created']
            ? 'Thanks for subscribing to the Valor newsletter.'
            : 'You are already subscribed to our newsletter.';

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'created' => $result['created'],
            ]);
        }

        return back()->with('success', $message);
    }
}
