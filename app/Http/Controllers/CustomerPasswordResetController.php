<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomerAuthException;
use App\Http\Requests\ForgotCustomerPasswordRequest;
use App\Http\Requests\ResetCustomerPasswordRequest;
use App\Services\CustomerAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerPasswordResetController extends Controller
{
    public function __construct(private CustomerAuthService $auth) {}

    public function create(Request $request): View
    {
        return view('reset-password', [
            'email' => $request->query('email'),
            'token' => $request->route('token'),
        ]);
    }

    public function sendLink(ForgotCustomerPasswordRequest $request): JsonResponse|RedirectResponse
    {
        $this->auth->sendPasswordResetLink($request->validated('email'));

        $message = 'If an account exists for that email, a password reset link has been sent.';

        if ($request->expectsJson()) {
            return response()->json(['message' => $message]);
        }

        return back()->with('success', $message);
    }

    public function reset(ResetCustomerPasswordRequest $request): JsonResponse|RedirectResponse
    {
        try {
            $this->auth->resetPassword($request->validated());
        } catch (CustomerAuthException $exception) {
            return $exception->render($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Your password has been reset successfully.',
            ]);
        }

        return redirect()
            ->route('login')
            ->with('success', 'Your password has been reset. You can sign in now.');
    }
}
