<?php

namespace App\Services;

use App\Models\ServiceOrder;
use App\Notifications\PaymentReceivedTestNotification;

class WebPushTestService
{
    public function saveSubscription(ServiceOrder $order, string $endpoint, ?string $p256dh, ?string $auth, ?string $contentEncoding): void
    {
        $order->updatePushSubscription($endpoint, $p256dh, $auth, $contentEncoding);
    }

    public function notifyPaid(ServiceOrder $order): void
    {
        $order->notify(new PaymentReceivedTestNotification($order));
    }
}
