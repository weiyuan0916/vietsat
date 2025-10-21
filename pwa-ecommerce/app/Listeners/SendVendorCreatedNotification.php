<?php

namespace App\Listeners;

use App\Events\VendorCreated;
use App\Filament\Notifications\NewVendorNotification;

/**
 * Send Vendor Created Notification Listener
 * 
 * Listen for VendorCreated event and send Filament notification
 */
class SendVendorCreatedNotification
{
    /**
     * Handle the event.
     */
    public function handle(VendorCreated $event): void
    {
        NewVendorNotification::send($event->vendor);
    }
}

