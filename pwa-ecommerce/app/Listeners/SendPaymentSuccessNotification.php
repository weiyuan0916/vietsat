<?php

namespace App\Listeners;

use App\Events\PaymentSuccess;
use App\Services\PusherService;
use App\Services\ExtensionTriggerService;
use Illuminate\Support\Facades\Log;

class SendPaymentSuccessNotification
{
    protected PusherService $pusherService;
    protected ExtensionTriggerService $extensionService;

    public function __construct(
        PusherService $pusherService,
        ExtensionTriggerService $extensionService
    ) {
        $this->pusherService = $pusherService;
        $this->extensionService = $extensionService;
    }

    /**
     * Handle the event.
     */
    public function handle(PaymentSuccess $event): void
    {
        $order = $event->order;

        // Send real-time notification via Pusher
        $this->pusherService->notifyOrderStatus(
            $order->order_code,
            'paid',
            'Thanh toán thành công!',
            [
                'order_code' => $order->order_code,
                'amount' => $order->amount,
                'facebook_url' => $order->facebook_profile_link,
                'paid_at' => $order->paid_at?->toIso8601String(),
            ]
        );

        // Send notification to specific user if logged in
        if ($order->user_id) {
            $this->pusherService->notifyUser(
                (string) $order->user_id,
                'payment.success',
                [
                    'order_code' => $order->order_code,
                    'amount' => $order->amount,
                    'message' => 'Thanh toán đơn hàng ' . $order->order_code . ' thành công!',
                ]
            );
        }

        // Trigger extension to process Facebook approval
        // This runs in background to not block the response
        try {
            $this->extensionService->triggerExtension($order);
        } catch (\Exception $e) {
            Log::error('[Listener] Failed to trigger extension', [
                'order_code' => $order->order_code,
                'error' => $e->getMessage(),
            ]);
        }

        Log::info('[Listener] Payment success notification sent', [
            'order_code' => $order->order_code,
            'user_id' => $order->user_id,
        ]);
    }
}

