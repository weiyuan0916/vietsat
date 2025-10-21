<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Filament\Notifications\NewOrderNotification;

/**
 * Send Order Created Notification Listener
 * 
 * Listen for OrderCreated event and send Filament notification
 */
class SendOrderCreatedNotification
{
    /**
     * Handle the event.
     */
    public function handle(OrderCreated $event): void
    {
        NewOrderNotification::send($event->order);
    }
}

