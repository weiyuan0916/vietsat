<?php

namespace App\Listeners;

use App\Events\PaymentSuccess;
use App\Services\ExtensionTriggerService;
use Illuminate\Support\Facades\Log;

class SendPaymentSuccessNotification
{
    protected ExtensionTriggerService $extensionService;

    public function __construct(ExtensionTriggerService $extensionService)
    {
        $this->extensionService = $extensionService;
    }

    /**
     * Handle the event.
     */
    public function handle(PaymentSuccess $event): void
    {
        $order = $event->order;
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

