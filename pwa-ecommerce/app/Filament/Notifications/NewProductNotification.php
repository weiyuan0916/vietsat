<?php

namespace App\Filament\Notifications;

use App\Models\Product;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

/**
 * New Product Notification
 * 
 * Send notification when a new product is added
 */
class NewProductNotification
{
    public static function send(Product $product): void
    {
        Notification::make()
            ->title('New Product Added')
            ->icon('heroicon-o-shopping-bag')
            ->iconColor('info')
            ->body("**{$product->name}** has been added by {$product->vendor->shop_name}.")
            ->actions([
                Action::make('view')
                    ->label('View Product')
                    ->url(route('filament.admin.resources.products.view', $product)),
            ])
            ->info()
            ->sendToDatabase(\App\Models\User::where('role', 'admin')->get());
    }
}

