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
use App\Contracts\Repositories\CompareRepositoryInterface;
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
use App\Repositories\Eloquent\CompareRepository;
use App\Repositories\Eloquent\CustomerAuthRepository;
use App\Repositories\Eloquent\CustomerOrderRepository;
use App\Repositories\Eloquent\CustomerRepository;
use App\Repositories\Eloquent\OrderRepository;
use App\Repositories\Eloquent\ProductRepository;
use App\Repositories\Eloquent\WishlistRepository;
use App\Services\Notifications\OrderNotificationDispatcher;
use App\Support\Dashboard\WidgetRegistry;
use App\Support\Dashboard\Widgets\ActivityTimelineWidget;
use App\Support\Dashboard\Widgets\AnnouncementsWidget;
use App\Support\Dashboard\Widgets\BestSellingProductsWidget;
use App\Support\Dashboard\Widgets\CatalogStatsWidget;
use App\Support\Dashboard\Widgets\CustomerGrowthChartWidget;
use App\Support\Dashboard\Widgets\CustomerStatsWidget;
use App\Support\Dashboard\Widgets\InventoryStatusChartWidget;
use App\Support\Dashboard\Widgets\LatestCustomersWidget;
use App\Support\Dashboard\Widgets\LatestReviewsWidget;
use App\Support\Dashboard\Widgets\LowStockWidget;
use App\Support\Dashboard\Widgets\MarketingCalendarWidget;
use App\Support\Dashboard\Widgets\NotificationsWidget;
use App\Support\Dashboard\Widgets\OrdersByRegionWidget;
use App\Support\Dashboard\Widgets\OrderStatsWidget;
use App\Support\Dashboard\Widgets\OrderStatusChartWidget;
use App\Support\Dashboard\Widgets\OrdersTrendChartWidget;
use App\Support\Dashboard\Widgets\QuickActionsWidget;
use App\Support\Dashboard\Widgets\RecentOrdersWidget;
use App\Support\Dashboard\Widgets\SalesStatsWidget;
use App\Support\Dashboard\Widgets\SalesTrendChartWidget;
use App\Support\Dashboard\Widgets\TopCategoriesChartWidget;
use App\Support\Dashboard\Widgets\TopCustomersWidget;
use App\Support\Dashboard\Widgets\WeatherWidget;
use App\Support\StoreSettings;
use App\View\Composers\AdminNotificationComposer;
use App\View\Composers\CartComposer;
use App\View\Composers\CompareComposer;
use App\View\Composers\CustomerAccountComposer;
use App\View\Composers\CustomerNotificationComposer;
use App\View\Composers\NavigationComposer;
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
        $this->app->bind(CompareRepositoryInterface::class, CompareRepository::class);
        $this->app->bind(CartRepositoryInterface::class, CartRepository::class);
        $this->app->bind(WishlistRepositoryInterface::class, WishlistRepository::class);
        $this->app->singleton(OrderNotificationDispatcher::class, fn (): OrderNotificationDispatcher => new OrderNotificationDispatcher(collect()));

        $this->registerDashboardWidgets();
    }

    /**
     * Register the dashboard widget registry and map every widget key to its
     * provider. Adding a new widget is a one-line entry here plus a config row.
     */
    private function registerDashboardWidgets(): void
    {
        $this->app->singleton(WidgetRegistry::class, function ($app): WidgetRegistry {
            $registry = new WidgetRegistry($app);

            $providers = [
                'sales-stats' => SalesStatsWidget::class,
                'order-stats' => OrderStatsWidget::class,
                'catalog-stats' => CatalogStatsWidget::class,
                'customer-stats' => CustomerStatsWidget::class,
                'sales-trend' => SalesTrendChartWidget::class,
                'orders-trend' => OrdersTrendChartWidget::class,
                'order-status-breakdown' => OrderStatusChartWidget::class,
                'top-categories' => TopCategoriesChartWidget::class,
                'inventory-status' => InventoryStatusChartWidget::class,
                'customer-growth' => CustomerGrowthChartWidget::class,
                'recent-orders' => RecentOrdersWidget::class,
                'low-stock' => LowStockWidget::class,
                'best-selling-products' => BestSellingProductsWidget::class,
                'latest-customers' => LatestCustomersWidget::class,
                'top-customers' => TopCustomersWidget::class,
                'latest-reviews' => LatestReviewsWidget::class,
                'activity-timeline' => ActivityTimelineWidget::class,
                'notifications' => NotificationsWidget::class,
                'quick-actions' => QuickActionsWidget::class,
                'marketing-calendar' => MarketingCalendarWidget::class,
                'orders-by-region' => OrdersByRegionWidget::class,
                'announcements' => AnnouncementsWidget::class,
                'weather' => WeatherWidget::class,
            ];

            foreach ($providers as $key => $providerClass) {
                $registry->register($key, $providerClass);
            }

            return $registry;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();
        $this->registerCustomerAuthListeners();

        View::composer('components.layouts.app', CompareComposer::class);
        View::composer('components.layouts.app', CartComposer::class);
        View::composer('components.layouts.app', WishlistComposer::class);
        View::composer('components.layouts.app', StoreSettingsComposer::class);
        View::composer('components.layouts.app', NavigationComposer::class);
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
