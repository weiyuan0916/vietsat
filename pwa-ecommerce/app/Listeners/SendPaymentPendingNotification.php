<?php

namespace App\Listeners;

use App\Events\PaymentPending;
use Illuminate\Support\Facades\Log;

class SendPaymentPendingNotification
{
    public function handle(PaymentPending $event): void
    {
        $order = $event->order;

        Log::info('[Listener] Payment pending notification sent', [
            'order_code' => $order->order_code,
        ]);
    }
}

