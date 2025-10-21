<?php

namespace App\Filament\Notifications;

use App\Models\Vendor;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

/**
 * New Vendor Notification
 * 
 * Send notification when a new vendor registers
 */
class NewVendorNotification
{
    public static function send(Vendor $vendor): void
    {
        Notification::make()
            ->title('New Vendor Registered')
            ->icon('heroicon-o-building-storefront')
            ->iconColor('success')
            ->body("**{$vendor->shop_name}** has registered as a new vendor.")
            ->actions([
                Action::make('view')
                    ->label('View Vendor')
                    ->url(route('filament.admin.resources.vendors.view', $vendor)),
            ])
            ->success()
            ->sendToDatabase(\App\Models\User::where('role', 'admin')->get());
    }
}

