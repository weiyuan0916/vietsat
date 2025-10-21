<?php

namespace App\Listeners;

use App\Events\ProductCreated;
use App\Filament\Notifications\NewProductNotification;

/**
 * Send Product Created Notification Listener
 * 
 * Listen for ProductCreated event and send Filament notification
 */
class SendProductCreatedNotification
{
    /**
     * Handle the event.
     */
    public function handle(ProductCreated $event): void
    {
        NewProductNotification::send($event->product);
    }
}

