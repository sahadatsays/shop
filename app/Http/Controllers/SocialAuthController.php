<?php

namespace App\Http\Controllers;

use App\Enums\AuthProvider;
use App\Exceptions\CustomerAuthException;
use App\Services\CustomerAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\Response;

class SocialAuthController extends Controller
{
    public function __construct(private CustomerAuthService $auth) {}

    public function redirect(string $provider): RedirectResponse|Response
    {
        $authProvider = $this->resolveProvider($provider);

        if (! $authProvider->isEnabled()) {
            return redirect()
                ->route('login')
                ->with('error', 'This sign-in provider is not available yet.');
        }

        return Socialite::driver($authProvider->value)
            ->scopes($authProvider === AuthProvider::Facebook ? ['email'] : [])
            ->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        $authProvider = $this->resolveProvider($provider);

        if (! $authProvider->isEnabled()) {
            return redirect()
                ->route('login')
                ->with('error', 'This sign-in provider is not available yet.');
        }

        try {
            $socialUser = Socialite::driver($authProvider->value)->user();
            $customer = $this->auth->resolveSocialUser($authProvider, $socialUser, request());
        } catch (CustomerAuthException $exception) {
            return redirect()
                ->route('login')
                ->with('error', $exception->getMessage());
        } catch (\Throwable $exception) {
            Log::warning('Social authentication failed.', [
                'provider' => $authProvider->value,
                'message' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('login')
                ->with('error', 'Unable to sign in with '.$authProvider->label().'. Please try again.');
        }

        return redirect()
            ->intended(route('account'))
            ->with('success', 'Welcome back, '.$customer->name.'.');
    }

    private function resolveProvider(string $provider): AuthProvider
    {
        $authProvider = AuthProvider::tryFrom($provider);

        if (! $authProvider) {
            abort(404);
        }

        return $authProvider;
    }
}
