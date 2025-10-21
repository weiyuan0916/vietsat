<?php

namespace App\Filament\Notifications;

use App\Models\Order;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

/**
 * New Order Notification
 * 
 * Send notification when a new order is created
 */
class NewOrderNotification
{
    public static function send(Order $order): void
    {
        Notification::make()
            ->title('New Order Received')
            ->icon('heroicon-o-shopping-cart')
            ->iconColor('warning')
            ->body("Order **{$order->order_number}** for ₫" . number_format($order->total, 0, '.', ',') . " has been placed.")
            ->actions([
                Action::make('view')
                    ->label('View Order')
                    ->url(route('filament.admin.resources.orders.view', $order)),
            ])
            ->warning()
            ->sendToDatabase(\App\Models\User::where('role', 'admin')->get());
    }
}

