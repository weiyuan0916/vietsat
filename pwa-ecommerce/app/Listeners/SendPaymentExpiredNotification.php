<?php

namespace App\Listeners;

use App\Events\PaymentExpired;
use Illuminate\Support\Facades\Log;

class SendPaymentExpiredNotification
{
    public function handle(PaymentExpired $event): void
    {
        $order = $event->order;

        Log::info('[Listener] Payment expired notification sent', [
            'order_code' => $order->order_code,
        ]);
    }
}

