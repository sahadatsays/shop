<?php

use App\Enums\AuthProvider;
use App\Enums\CustomerStatus;
use App\Events\CustomerLoggedIn;
use App\Events\CustomerRegistered;
use App\Events\PasswordChanged;
use App\Models\Customer;
use App\Models\CustomerProfile;
use App\Models\CustomerSocialAccount;
use App\Models\Wishlist;
use App\Notifications\CustomerResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

uses()->group('customer-auth');
uses(RefreshDatabase::class);

beforeEach(function (): void {
    config([
        'services.google.client_id' => 'google-client-id',
        'services.google.client_secret' => 'google-client-secret',
        'services.facebook.client_id' => 'facebook-client-id',
        'services.facebook.client_secret' => 'facebook-client-secret',
    ]);
});

test('login page uses the local login banner on desktop and mobile', function (): void {
    $response = $this->get(route('login'));

    $response->assertSuccessful();

    expect(substr_count($response->getContent(), asset('storage/login/login-banner.png')))->toBe(2);
});

test('customer can register with email and password', function (): void {
    Event::fake([CustomerRegistered::class]);

    $response = $this->postJson(route('register.store'), [
        'name' => 'James Mitchell',
        'email' => 'james@example.com',
        'phone' => '+15550001111',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'newsletter' => true,
    ]);

    $response->assertCreated()
        ->assertJsonPath('customer.email', 'james@example.com');

    $customer = Customer::query()->where('email', 'james@example.com')->first();

    expect($customer)->not->toBeNull()
        ->and($customer->status)->toBe(CustomerStatus::Active)
        ->and($customer->hasVerifiedEmail())->toBeFalse()
        ->and($customer->newsletter_subscribed)->toBeTrue()
        ->and(Hash::check('Password123!', $customer->password))->toBeTrue()
        ->and(CustomerProfile::query()->where('customer_id', $customer->id)->exists())->toBeTrue()
        ->and(Wishlist::query()->where('customer_id', $customer->id)->exists())->toBeTrue();

    Event::assertDispatched(CustomerRegistered::class);
});

test('customer can login with valid credentials', function (): void {
    Event::fake([CustomerLoggedIn::class]);

    $customer = Customer::factory()->create([
        'email' => 'login@example.com',
        'password' => 'Password123!',
    ]);

    $response = $this->postJson(route('login.store'), [
        'email' => 'login@example.com',
        'password' => 'Password123!',
        'remember' => true,
    ]);

    $response->assertSuccessful()
        ->assertJsonPath('customer.id', $customer->id);

    $this->assertAuthenticatedAs($customer, 'customer');
    expect(session('customer_id'))->toBe($customer->id);

    Event::assertDispatched(CustomerLoggedIn::class);
});

test('login rejects invalid credentials with a generic message', function (): void {
    Customer::factory()->create([
        'email' => 'login@example.com',
        'password' => 'Password123!',
    ]);

    $response = $this->postJson(route('login.store'), [
        'email' => 'login@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertUnprocessable()
        ->assertJsonPath('message', 'These credentials do not match our records.');

    $this->assertGuest('customer');
});

test('suspended customers receive a friendly login error', function (): void {
    Customer::factory()->suspended()->create([
        'email' => 'suspended@example.com',
        'password' => 'Password123!',
    ]);

    $response = $this->postJson(route('login.store'), [
        'email' => 'suspended@example.com',
        'password' => 'Password123!',
    ]);

    $response->assertUnprocessable()
        ->assertJsonPath('message', 'Your account has been suspended. Please contact support for assistance.');
});

test('google login creates a new customer account', function (): void {
    Event::fake([CustomerRegistered::class]);

    $socialUser = mockSocialUser('google-user-1', 'Google User', 'google@example.com');

    Socialite::shouldReceive('driver')
        ->with('google')
        ->andReturnSelf();
    Socialite::shouldReceive('scopes')->andReturnSelf();
    Socialite::shouldReceive('user')->andReturn($socialUser);

    $response = $this->get(route('auth.social.callback', 'google'));

    $response->assertRedirect(route('account'));

    $customer = Customer::query()->where('email', 'google@example.com')->first();

    expect($customer)->not->toBeNull()
        ->and(CustomerSocialAccount::query()->where('customer_id', $customer->id)->where('provider', AuthProvider::Google)->exists())->toBeTrue();

    $this->assertAuthenticatedAs($customer, 'customer');
    Event::assertDispatched(CustomerRegistered::class);
});

test('google login requires password sign-in when email already exists', function (): void {
    Customer::factory()->create([
        'email' => 'existing@example.com',
        'password' => 'Password123!',
    ]);

    $socialUser = mockSocialUser('google-user-2', 'Existing User', 'existing@example.com');

    Socialite::shouldReceive('driver')
        ->with('google')
        ->andReturnSelf();
    Socialite::shouldReceive('scopes')->andReturnSelf();
    Socialite::shouldReceive('user')->andReturn($socialUser);

    $this->get(route('auth.social.callback', 'google'))
        ->assertRedirect(route('login'))
        ->assertSessionHas('error');

    expect(CustomerSocialAccount::query()->where('provider', AuthProvider::Google)->exists())->toBeFalse();
    $this->assertGuest('customer');
});

test('facebook login requires password sign-in when email already exists', function (): void {
    Customer::factory()->create([
        'email' => 'facebook@example.com',
        'password' => 'Password123!',
    ]);

    $socialUser = mockSocialUser('facebook-user-1', 'Facebook User', 'facebook@example.com');

    Socialite::shouldReceive('driver')
        ->with('facebook')
        ->andReturnSelf();
    Socialite::shouldReceive('scopes')->andReturnSelf();
    Socialite::shouldReceive('user')->andReturn($socialUser);

    $this->get(route('auth.social.callback', 'facebook'))
        ->assertRedirect(route('login'))
        ->assertSessionHas('error');

    expect(CustomerSocialAccount::query()->where('provider', AuthProvider::Facebook)->exists())->toBeFalse();
});

test('customer can request a password reset link', function (): void {
    Notification::fake();

    Customer::factory()->create(['email' => 'reset@example.com']);

    $response = $this->postJson(route('password.email'), [
        'email' => 'reset@example.com',
    ]);

    $response->assertSuccessful();

    Notification::assertSentTo(
        Customer::query()->where('email', 'reset@example.com')->first(),
        CustomerResetPasswordNotification::class,
    );
});

test('customer can reset password with a valid token', function (): void {
    Event::fake([PasswordChanged::class]);

    $customer = Customer::factory()->create(['email' => 'reset@example.com']);

    $token = Password::broker('customers')->createToken($customer);

    $response = $this->postJson(route('password.update'), [
        'token' => $token,
        'email' => 'reset@example.com',
        'password' => 'NewPassword123!',
        'password_confirmation' => 'NewPassword123!',
    ]);

    $response->assertSuccessful();

    expect(Hash::check('NewPassword123!', $customer->fresh()->password))->toBeTrue();
    Event::assertDispatched(PasswordChanged::class);
});

test('authenticated customer can update profile details', function (): void {
    Event::fake([PasswordChanged::class]);

    $customer = actingAsCustomer(Customer::factory()->create([
        'name' => 'James Mitchell',
        'phone' => '+15550001111',
        'password' => 'Password123!',
    ]));

    $response = $this->putJson(route('profile.update'), [
        'first_name' => 'Jamie',
        'last_name' => 'Mitchell',
        'phone' => '+15550002222',
        'current_password' => 'Password123!',
        'password' => 'AnotherPassword123!',
        'password_confirmation' => 'AnotherPassword123!',
    ]);

    $response->assertSuccessful()
        ->assertJsonPath('customer.name', 'Jamie Mitchell')
        ->assertJsonPath('customer.phone', '+15550002222');

    expect(Hash::check('AnotherPassword123!', $customer->fresh()->password))->toBeTrue();
    Event::assertDispatched(PasswordChanged::class);
});

test('customer can logout securely', function (): void {
    $customer = actingAsCustomer();

    $response = $this->post(route('logout'));

    $response->assertRedirect(route('home'));
    $this->assertGuest('customer');
    expect(session()->has('customer_id'))->toBeFalse();
});

test('guest account pages redirect to login', function (): void {
    $this->get(route('account.settings'))->assertRedirect(route('login'));
    $this->get(route('profile'))->assertRedirect(route('login'));
});

function mockSocialUser(string $id, string $name, string $email, bool $emailVerified = true): SocialiteUser
{
    $user = Mockery::mock(SocialiteUser::class);
    $user->shouldReceive('getId')->andReturn($id);
    $user->shouldReceive('getName')->andReturn($name);
    $user->shouldReceive('getNickname')->andReturn(null);
    $user->shouldReceive('getEmail')->andReturn($email);
    $user->shouldReceive('getAvatar')->andReturn('https://example.com/avatar.jpg');
    $user->shouldReceive('getRaw')->andReturn([
        'email_verified' => $emailVerified,
        'verified_email' => $emailVerified,
    ]);

    return $user;
}
