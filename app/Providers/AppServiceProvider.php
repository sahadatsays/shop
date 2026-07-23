<?php

namespace App\Providers;

use App\Contracts\Repositories\AdminBrandRepositoryInterface;
use App\Contracts\Repositories\AdminCategoryRepositoryInterface;
use App\Contracts\Repositories\AdminCustomerRepositoryInterface;
use App\Contracts\Repositories\AdminInventoryRepositoryInterface;
use App\Contracts\Repositories\AdminProductRepositoryInterface;
use App\Contracts\Repositories\CategoryRepositoryInterface;
use App\Contracts\Repositories\CustomerRepositoryInterface;
use App\Contracts\Repositories\OrderRepositoryInterface;
use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Repositories\Eloquent\AdminBrandRepository;
use App\Repositories\Eloquent\AdminCategoryRepository;
use App\Repositories\Eloquent\AdminCustomerRepository;
use App\Repositories\Eloquent\AdminInventoryRepository;
use App\Repositories\Eloquent\AdminProductRepository;
use App\Repositories\Eloquent\CategoryRepository;
use App\Repositories\Eloquent\CustomerRepository;
use App\Repositories\Eloquent\OrderRepository;
use App\Repositories\Eloquent\ProductRepository;
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
        $this->app->bind(CustomerRepositoryInterface::class, CustomerRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(AdminCategoryRepositoryInterface::class, AdminCategoryRepository::class);
        $this->app->bind(AdminBrandRepositoryInterface::class, AdminBrandRepository::class);
        $this->app->bind(AdminProductRepositoryInterface::class, AdminProductRepository::class);
        $this->app->bind(AdminInventoryRepositoryInterface::class, AdminInventoryRepository::class);
        $this->app->bind(AdminCustomerRepositoryInterface::class, AdminCustomerRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
