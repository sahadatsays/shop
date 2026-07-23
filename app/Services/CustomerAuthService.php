<?php

namespace App\Services;

use App\Contracts\Repositories\CustomerAuthRepositoryInterface;
use App\Enums\AuthProvider;
use App\Enums\CustomerStatus;
use App\Events\CustomerLoggedIn;
use App\Events\CustomerLoggedOut;
use App\Events\CustomerRegistered;
use App\Events\PasswordChanged;
use App\Events\ProviderLinked;
use App\Exceptions\CustomerAuthException;
use App\Models\Customer;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class CustomerAuthService
{
    public function __construct(
        private CustomerAuthRepositoryInterface $customers,
        private CartService $cart,
        private WishlistService $wishlist,
        private AuditService $audit,
    ) {}

    /**
     * @param  array{name: string, email: string, phone: string, password: string, newsletter?: bool|null}  $data
     */
    public function register(array $data, Request $request): Customer
    {
        return DB::transaction(function () use ($data, $request): Customer {
            $customer = $this->customers->create([
                'name' => $data['name'],
                'email' => Str::lower($data['email']),
                'phone' => $data['phone'],
                'password' => $data['password'],
                'status' => CustomerStatus::Active,
                'newsletter_subscribed' => (bool) ($data['newsletter'] ?? false),
            ]);

            $this->customers->createProfile($customer);
            $this->customers->ensureWishlist($customer);

            CustomerRegistered::dispatch($customer);
            $this->establishSession($customer, false, $request);

            return $customer;
        });
    }

    public function attemptLogin(string $email, string $password, bool $remember, Request $request): Customer
    {
        $customer = $this->customers->findByEmail($email);

        if (! $customer || ! $customer->usesPasswordAuthentication() || ! Hash::check($password, $customer->password)) {
            throw CustomerAuthException::invalidCredentials();
        }

        $this->assertCanLogin($customer);
        $this->establishSession($customer, $remember, $request);

        return $customer;
    }

    public function logout(Request $request): void
    {
        $customer = $this->currentCustomer();

        Auth::guard('customer')->logout();

        $request->session()->forget('customer_id');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($customer) {
            CustomerLoggedOut::dispatch($customer);
            $this->audit->logCustomerLogout($customer, $request);
        }
    }

    public function resolveSocialUser(AuthProvider $provider, SocialiteUser $socialUser, Request $request): Customer
    {
        return DB::transaction(function () use ($provider, $socialUser, $request): Customer {
            $providerId = (string) $socialUser->getId();
            $email = Str::lower((string) ($socialUser->getEmail() ?? ''));
            $avatar = $socialUser->getAvatar();
            $name = trim((string) ($socialUser->getName() ?: ($socialUser->getNickname() ?? '')));

            $customer = $this->customers->findBySocialAccount($provider, $providerId);

            if ($customer) {
                $this->assertCanLogin($customer);
                $this->establishSession($customer, true, $request);

                return $customer;
            }

            $existingByEmail = $email !== '' ? $this->customers->findByEmail($email) : null;

            if ($existingByEmail) {
                $this->assertCanLogin($existingByEmail);
                $this->customers->linkSocialAccount($existingByEmail, $provider, $providerId, $avatar);
                ProviderLinked::dispatch($existingByEmail, $provider);
                $this->establishSession($existingByEmail, true, $request);

                return $existingByEmail;
            }

            if ($name === '') {
                $name = $email !== '' ? Str::headline(Str::before($email, '@')) : 'Valor Customer';
            }

            $customer = $this->customers->create([
                'name' => $name,
                'email' => $email !== '' ? $email : "{$provider->value}_{$providerId}@oauth.local",
                'phone' => null,
                'password' => null,
                'avatar' => $avatar,
                'provider' => $provider->value,
                'provider_id' => $providerId,
                'status' => CustomerStatus::Active,
            ]);

            $this->customers->linkSocialAccount($customer, $provider, $providerId, $avatar);
            $this->customers->createProfile($customer);
            $this->customers->ensureWishlist($customer);

            CustomerRegistered::dispatch($customer);
            $this->establishSession($customer, true, $request);

            return $customer;
        });
    }

    public function sendPasswordResetLink(string $email): void
    {
        Password::broker('customers')->sendResetLink([
            'email' => Str::lower($email),
        ]);
    }

    /**
     * @param  array{email: string, password: string, password_confirmation: string, token: string}  $credentials
     */
    public function resetPassword(array $credentials): void
    {
        $status = Password::broker('customers')->reset(
            $credentials,
            function (Customer $customer, string $password): void {
                $customer->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                PasswordChanged::dispatch($customer);
                event(new PasswordReset($customer));
            },
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw CustomerAuthException::passwordResetFailed(__($status));
        }
    }

    /**
     * @param  array{name?: string, phone?: string, password?: string, current_password?: string, remove_avatar?: bool}  $data
     */
    public function updateProfile(Customer $customer, array $data, Request $request): Customer
    {
        $attributes = [];

        if (array_key_exists('name', $data)) {
            $attributes['name'] = $data['name'];
        }

        if (array_key_exists('phone', $data)) {
            $attributes['phone'] = $data['phone'];
        }

        if ($request->hasFile('avatar')) {
            $attributes['avatar'] = $request->file('avatar')->store('customer-avatars', 'public');
        } elseif (($data['remove_avatar'] ?? false) === true) {
            $attributes['avatar'] = null;
        }

        if ($attributes !== []) {
            $customer = $this->customers->update($customer, $attributes);
        }

        if (! empty($data['password'])) {
            if (! $customer->usesPasswordAuthentication() || ! Hash::check((string) ($data['current_password'] ?? ''), $customer->password)) {
                throw CustomerAuthException::currentPasswordIncorrect();
            }

            $customer = $this->customers->update($customer, [
                'password' => $data['password'],
            ]);

            PasswordChanged::dispatch($customer);
        }

        return $customer;
    }

    public function currentCustomer(): ?Customer
    {
        /** @var Customer|null $customer */
        $customer = Auth::guard('customer')->user();

        if ($customer instanceof Customer) {
            return $customer;
        }

        $customerId = session('customer_id');

        return $customerId ? Customer::query()->find($customerId) : null;
    }

    private function establishSession(Customer $customer, bool $remember, Request $request): void
    {
        $this->assertCanLogin($customer);

        Auth::guard('customer')->login($customer, $remember);

        $request->session()->regenerate();
        session(['customer_id' => $customer->id]);

        $customer->update(['last_login_at' => now()]);

        $this->cart->mergeGuestIntoCustomer($customer);
        $this->wishlist->mergeGuestIntoCustomer($customer);
        $this->audit->logCustomerLogin($customer, $request);

        CustomerLoggedIn::dispatch($customer);
    }

    private function assertCanLogin(Customer $customer): void
    {
        if ($customer->status->canLogin()) {
            return;
        }

        throw CustomerAuthException::accountBlocked($customer->status->loginBlockedMessage());
    }
}
