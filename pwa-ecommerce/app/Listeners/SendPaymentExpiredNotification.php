<?php

namespace App\Listeners;

use App\Events\PaymentExpired;
use App\Services\PusherService;
use Illuminate\Support\Facades\Log;

class SendPaymentExpiredNotification
{
    protected PusherService $pusherService;

    public function __construct(PusherService $pusherService)
    {
        $this->pusherService = $pusherService;
    }

    /**
     * Handle the event.
     */
    public function handle(PaymentExpired $event): void
    {
        $order = $event->order;

        // Send real-time notification via Pusher
        $this->pusherService->notifyOrderStatus(
            $order->order_code,
            'expired',
            'Đơn hàng đã hết hạn',
            [
                'order_code' => $order->order_code,
                'message' => 'Mã thanh toán đã hết hạn. Vui lòng tạo mã mới.',
            ]
        );

        Log::info('[Listener] Payment expired notification sent', [
            'order_code' => $order->order_code,
        ]);
    }
}

