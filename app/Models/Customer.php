<?php

namespace App\Models;

use App\Enums\CustomerStatus;
use App\Models\Concerns\HasAppNotifications;
use App\Notifications\CustomerResetPasswordNotification;
use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class Customer extends Authenticatable
{
    /** @use HasFactory<CustomerFactory> */
    use HasAppNotifications, HasFactory, Notifiable, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'avatar',
        'provider',
        'provider_id',
        'email_verified_at',
        'status',
        'internal_notes',
        'last_login_at',
        'newsletter_subscribed',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'provider_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => CustomerStatus::class,
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'newsletter_subscribed' => 'boolean',
            'password' => 'hashed',
        ];
    }

    /**
     * @return HasMany<Order, $this>
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * @return HasMany<Review, $this>
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * @return HasOne<Wishlist, $this>
     */
    public function wishlist(): HasOne
    {
        return $this->hasOne(Wishlist::class);
    }

    /**
     * @return HasMany<CustomerAddress, $this>
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(CustomerAddress::class);
    }

    /**
     * @return HasMany<CustomerNote, $this>
     */
    public function notes(): HasMany
    {
        return $this->hasMany(CustomerNote::class)->latest();
    }

    /**
     * @return HasOne<CustomerProfile, $this>
     */
    public function profile(): HasOne
    {
        return $this->hasOne(CustomerProfile::class);
    }

    /**
     * @return HasMany<CustomerSocialAccount, $this>
     */
    public function socialAccounts(): HasMany
    {
        return $this->hasMany(CustomerSocialAccount::class);
    }

    /**
     * @param  Builder<Customer>  $query
     * @return Builder<Customer>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', CustomerStatus::Active);
    }

    /**
     * @param  Builder<Customer>  $query
     * @return Builder<Customer>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('name');
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim($this->name)) ?: [];

        $initials = collect($parts)
            ->filter()
            ->take(2)
            ->map(fn (string $part): string => strtoupper(substr($part, 0, 1)))
            ->implode('');

        return $initials !== '' ? $initials : 'CU';
    }

    public function avatarUrl(): ?string
    {
        if (! $this->avatar) {
            return null;
        }

        if (str_starts_with($this->avatar, 'http')) {
            return $this->avatar;
        }

        return asset('storage/'.$this->avatar);
    }

    public function usesPasswordAuthentication(): bool
    {
        return filled($this->password);
    }

    public function hasNotifiableEmail(): bool
    {
        $email = Str::lower(trim((string) $this->email));

        if ($email === '' || str_ends_with($email, '@oauth.local')) {
            return false;
        }

        return Validator::make(
            ['email' => $email],
            ['email' => ['required', 'email']],
        )->passes();
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new CustomerResetPasswordNotification($token));
    }
}
