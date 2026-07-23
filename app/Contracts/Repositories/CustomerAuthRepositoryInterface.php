<?php

namespace App\Contracts\Repositories;

use App\Enums\AuthProvider;
use App\Models\Customer;
use App\Models\CustomerProfile;
use App\Models\CustomerSocialAccount;
use App\Models\Wishlist;

interface CustomerAuthRepositoryInterface
{
    public function findByEmail(string $email): ?Customer;

    public function findByPhone(string $phone): ?Customer;

    public function findBySocialAccount(AuthProvider $provider, string $providerId): ?Customer;

    public function create(array $attributes): Customer;

    public function update(Customer $customer, array $attributes): Customer;

    public function createProfile(Customer $customer): CustomerProfile;

    public function ensureWishlist(Customer $customer): Wishlist;

    public function linkSocialAccount(
        Customer $customer,
        AuthProvider $provider,
        string $providerId,
        ?string $avatarUrl = null,
    ): CustomerSocialAccount;
}
