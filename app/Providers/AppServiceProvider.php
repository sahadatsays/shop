<?php

namespace App\Providers;

use App\Contracts\Repositories\AdminBrandRepositoryInterface;
use App\Contracts\Repositories\AdminCategoryRepositoryInterface;
use App\Contracts\Repositories\AdminCustomerRepositoryInterface;
use App\Contracts\Repositories\AdminInventoryRepositoryInterface;
use App\Contracts\Repositories\AdminOrderRepositoryInterface;
use App\Contracts\Repositories\AdminProductRepositoryInterface;
use App\Contracts\Repositories\CartRepositoryInterface;
use App\Contracts\Repositories\CategoryRepositoryInterface;
use App\Contracts\Repositories\CustomerAuthRepositoryInterface;
use App\Contracts\Repositories\CustomerOrderRepositoryInterface;
use App\Contracts\Repositories\CustomerRepositoryInterface;
use App\Contracts\Repositories\OrderRepositoryInterface;
use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Contracts\Repositories\WishlistRepositoryInterface;
use App\Events\CustomerRegistered;
use App\Events\ProviderLinked;
use App\Listeners\AwardRegistrationRewardPoints;
use App\Listeners\SendCustomerWelcomeEmail;
use App\Listeners\TrackCustomerMarketingRegistration;
use App\Repositories\Eloquent\AdminBrandRepository;
use App\Repositories\Eloquent\AdminCategoryRepository;
use App\Repositories\Eloquent\AdminCustomerRepository;
use App\Repositories\Eloquent\AdminInventoryRepository;
use App\Repositories\Eloquent\AdminOrderRepository;
use App\Repositories\Eloquent\AdminProductRepository;
use App\Repositories\Eloquent\CartRepository;
use App\Repositories\Eloquent\CategoryRepository;
use App\Repositories\Eloquent\CustomerAuthRepository;
use App\Repositories\Eloquent\CustomerOrderRepository;
use App\Repositories\Eloquent\CustomerRepository;
use App\Repositories\Eloquent\OrderRepository;
use App\Repositories\Eloquent\ProductRepository;
use App\Repositories\Eloquent\WishlistRepository;
use App\Services\Notifications\OrderNotificationDispatcher;
use App\Support\StoreSettings;
use App\View\Composers\AdminNotificationComposer;
use App\View\Composers\CartComposer;
use App\View\Composers\CustomerAccountComposer;
use App\View\Composers\CustomerNotificationComposer;
use App\View\Composers\StoreSettingsComposer;
use App\View\Composers\WishlistComposer;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(CustomerOrderRepositoryInterface::class, CustomerOrderRepository::class);
        $this->app->bind(CustomerAuthRepositoryInterface::class, CustomerAuthRepository::class);
        $this->app->bind(CustomerRepositoryInterface::class, CustomerRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(AdminCategoryRepositoryInterface::class, AdminCategoryRepository::class);
        $this->app->bind(AdminBrandRepositoryInterface::class, AdminBrandRepository::class);
        $this->app->bind(AdminProductRepositoryInterface::class, AdminProductRepository::class);
        $this->app->bind(AdminInventoryRepositoryInterface::class, AdminInventoryRepository::class);
        $this->app->bind(AdminCustomerRepositoryInterface::class, AdminCustomerRepository::class);
        $this->app->bind(AdminOrderRepositoryInterface::class, AdminOrderRepository::class);
        $this->app->bind(CartRepositoryInterface::class, CartRepository::class);
        $this->app->bind(WishlistRepositoryInterface::class, WishlistRepository::class);
        $this->app->singleton(OrderNotificationDispatcher::class, fn (): OrderNotificationDispatcher => new OrderNotificationDispatcher(collect()));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();
        $this->registerCustomerAuthListeners();

        View::composer('components.layouts.app', CartComposer::class);
        View::composer('components.layouts.app', WishlistComposer::class);
        View::composer('components.layouts.app', StoreSettingsComposer::class);
        View::composer('components.account.sidebar', CustomerNotificationComposer::class);
        View::composer('components.account.sidebar', CustomerAccountComposer::class);
        View::composer('components.admin.notification-panel', AdminNotificationComposer::class);
        View::composer('errors.store-maintenance', StoreSettingsComposer::class);

        $this->applyStoreSettings();
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for('customer-login', fn (Request $request): Limit => Limit::perMinute(5)->by($request->ip()));

        RateLimiter::for('customer-register', fn (Request $request): Limit => Limit::perMinute(5)->by($request->ip()));

        RateLimiter::for('customer-password-reset', fn (Request $request): Limit => Limit::perMinute(3)->by($request->ip()));
    }

    private function registerCustomerAuthListeners(): void
    {
        Event::listen(CustomerRegistered::class, SendCustomerWelcomeEmail::class);
        Event::listen(CustomerRegistered::class, AwardRegistrationRewardPoints::class);
        Event::listen(CustomerRegistered::class, TrackCustomerMarketingRegistration::class);
        Event::listen(ProviderLinked::class, TrackCustomerMarketingRegistration::class);
    }

    private function applyStoreSettings(): void
    {
        try {
            $settings = StoreSettings::current();

            config([
                'app.name' => $settings->store_name,
                'app.timezone' => $settings->timezone,
            ]);

            if ($settings->mail_from_name) {
                config(['mail.from.name' => $settings->mail_from_name]);
            }

            if ($settings->mail_from_address) {
                config(['mail.from.address' => $settings->mail_from_address]);
            }
        } catch (\Throwable) {
            //
        }
    }
}
