<?php

namespace App\Providers;

use App\Events\OrderCreated;
use App\Events\ProductCreated;
use App\Events\VendorCreated;
use App\Events\PaymentSuccess;
use App\Events\PaymentPending;
use App\Events\PaymentExpired;
use App\Listeners\SendOrderCreatedNotification;
use App\Listeners\SendProductCreatedNotification;
use App\Listeners\SendVendorCreatedNotification;
use App\Listeners\SendPaymentSuccessNotification;
use App\Listeners\SendPaymentPendingNotification;
use App\Listeners\SendPaymentExpiredNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Event Service Provider
 * 
 * Register event listeners for the application
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        VendorCreated::class => [
            SendVendorCreatedNotification::class,
        ],
        ProductCreated::class => [
            SendProductCreatedNotification::class,
        ],
        OrderCreated::class => [
            SendOrderCreatedNotification::class,
        ],
        PaymentSuccess::class => [
            SendPaymentSuccessNotification::class,
        ],
        PaymentPending::class => [
            SendPaymentPendingNotification::class,
        ],
        PaymentExpired::class => [
            SendPaymentExpiredNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}

