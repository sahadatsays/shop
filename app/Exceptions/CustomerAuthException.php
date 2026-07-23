<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CustomerAuthException extends Exception
{
    public function __construct(
        string $message,
        private readonly string $field = 'email',
    ) {
        parent::__construct($message);
    }

    public static function invalidCredentials(): self
    {
        return new self('These credentials do not match our records.');
    }

    public static function accountBlocked(string $message): self
    {
        return new self($message);
    }

    public static function currentPasswordIncorrect(): self
    {
        return new self('The current password is incorrect.', 'current_password');
    }

    public static function passwordResetFailed(string $message): self
    {
        return new self($message);
    }

    public static function providerNotEnabled(): self
    {
        return new self('This sign-in provider is not available.');
    }

    public function field(): string
    {
        return $this->field;
    }

    public function render(Request $request): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $this->getMessage(),
                'errors' => [
                    $this->field() => [$this->getMessage()],
                ],
            ], 422);
        }

        return back()
            ->withInput($request->except('password', 'password_confirmation', 'current_password', 'new_password'))
            ->withErrors([$this->field() => $this->getMessage()]);
    }
}
