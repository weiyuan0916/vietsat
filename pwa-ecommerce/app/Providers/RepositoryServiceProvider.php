<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Interfaces
use App\Repositories\Interfaces\VendorRepositoryInterface;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Repositories\Interfaces\CartRepositoryInterface;
use App\Repositories\Interfaces\LicenseRepositoryInterface;
use App\Repositories\Interfaces\LicenseActivationRepositoryInterface;
use App\Repositories\Interfaces\ServiceOrderRepositoryInterface;

// Implementations
use App\Repositories\Eloquent\VendorRepository;
use App\Repositories\Eloquent\ProductRepository;
use App\Repositories\Eloquent\CategoryRepository;
use App\Repositories\Eloquent\OrderRepository;
use App\Repositories\Eloquent\CartRepository;
use App\Repositories\Eloquent\LicenseRepository;
use App\Repositories\Eloquent\LicenseActivationRepository;
use App\Repositories\Eloquent\ServiceOrderRepository;

/**
 * Repository Service Provider
 * 
 * Binds repository interfaces to their implementations
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(VendorRepositoryInterface::class, VendorRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(CartRepositoryInterface::class, CartRepository::class);
        $this->app->bind(LicenseRepositoryInterface::class, LicenseRepository::class);
        $this->app->bind(LicenseActivationRepositoryInterface::class, LicenseActivationRepository::class);
        $this->app->bind(ServiceOrderRepositoryInterface::class, ServiceOrderRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
