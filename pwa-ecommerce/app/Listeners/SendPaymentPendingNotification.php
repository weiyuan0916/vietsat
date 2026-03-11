<?php

namespace App\Listeners;

use App\Events\PaymentPending;
use App\Services\PusherService;
use Illuminate\Support\Facades\Log;

class SendPaymentPendingNotification
{
    protected PusherService $pusherService;

    public function __construct(PusherService $pusherService)
    {
        $this->pusherService = $pusherService;
    }

    /**
     * Handle the event.
     */
    public function handle(PaymentPending $event): void
    {
        $order = $event->order;

        // Send real-time notification via Pusher
        $this->pusherService->notifyOrderStatus(
            $order->order_code,
            'pending',
            'Đơn hàng đang chờ thanh toán',
            [
                'order_code' => $order->order_code,
                'amount' => $order->amount,
                'expires_at' => $order->expires_at?->toIso8601String(),
            ]
        );

        Log::info('[Listener] Payment pending notification sent', [
            'order_code' => $order->order_code,
        ]);
    }
}

