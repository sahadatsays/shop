<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\CustomerAuthRepositoryInterface;
use App\Enums\AuthProvider;
use App\Models\Customer;
use App\Models\CustomerProfile;
use App\Models\CustomerSocialAccount;
use App\Models\Wishlist;
use Illuminate\Support\Str;

class CustomerAuthRepository implements CustomerAuthRepositoryInterface
{
    public function findByEmail(string $email): ?Customer
    {
        return Customer::query()->where('email', Str::lower($email))->first();
    }

    public function findByPhone(string $phone): ?Customer
    {
        return Customer::query()->where('phone', $phone)->first();
    }

    public function findBySocialAccount(AuthProvider $provider, string $providerId): ?Customer
    {
        return CustomerSocialAccount::query()
            ->where('provider', $provider)
            ->where('provider_id', $providerId)
            ->with('customer')
            ->first()
            ?->customer;
    }

    public function create(array $attributes): Customer
    {
        return Customer::query()->create($attributes);
    }

    public function update(Customer $customer, array $attributes): Customer
    {
        $customer->update($attributes);

        return $customer->fresh();
    }

    public function createProfile(Customer $customer): CustomerProfile
    {
        return $customer->profile()->create([
            'preferences' => [],
        ]);
    }

    public function ensureWishlist(Customer $customer): Wishlist
    {
        return Wishlist::query()->firstOrCreate(
            ['customer_id' => $customer->id],
            ['expires_at' => null],
        );
    }

    public function linkSocialAccount(
        Customer $customer,
        AuthProvider $provider,
        string $providerId,
        ?string $avatarUrl = null,
    ): CustomerSocialAccount {
        $account = $customer->socialAccounts()->updateOrCreate(
            ['provider' => $provider],
            [
                'provider_id' => $providerId,
                'avatar_url' => $avatarUrl,
            ],
        );

        $customer->update([
            'provider' => $provider->value,
            'provider_id' => $providerId,
            'avatar' => $avatarUrl ?? $customer->avatar,
        ]);

        return $account;
    }
}
