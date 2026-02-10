<?php

namespace App\Services;

use Pusher\Pusher;
use Illuminate\Support\Facades\Log;

/**
 * Service for sending real-time notifications via Pusher
 *
 * This service is used for order status updates and other non-payment events.
 * For payment events, use Laravel Reverb with the built-in event system.
 *
 * Usage:
 * $pusherService = app(PusherService::class);
 * $pusherService->notifyOrderStatus('ORD-XXX', 'shipped', 'Your order has been shipped', ['tracking' => 'ABC123']);
 */
class PusherService
{
    /**
     * Pusher instance
     */
    protected Pusher $pusher;

    /**
     * Create a new service instance
     */
    public function __construct()
    {
        $options = [
            'cluster' => env('PUSHER_APP_CLUSTER', 'ap1'),
            'useTLS' => true,
        ];

        $this->pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            $options
        );
    }

    /**
     * Notify order status update
     *
     * @param string $orderCode The order code
     * @param string $status The new status
     * @param string $message Human-readable message
     * @param array $data Additional data
     * @return bool
     */
    public function notifyOrderStatus(string $orderCode, string $status, string $message, array $data = []): bool
    {
        try {
            $this->pusher->trigger(
                'order.' . $orderCode,
                'order.status',
                [
                    'order_code' => $orderCode,
                    'status' => $status,
                    'message' => $message,
                    'data' => $data,
                    'timestamp' => now()->toIso8601String(),
                ]
            );

            Log::info('[PusherService] Order status notification sent', [
                'order_code' => $orderCode,
                'status' => $status,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('[PusherService] Failed to send order status notification', [
                'order_code' => $orderCode,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Notify user via private channel
     *
     * @param string $userId The user ID
     * @param string $event The event name
     * @param array $data The data to send
     * @return bool
     */
    public function notifyUser(string $userId, string $event, array $data): bool
    {
        try {
            $this->pusher->trigger(
                'private-user.' . $userId,
                $event,
                array_merge($data, ['timestamp' => now()->toIso8601String()])
            );

            Log::info('[PusherService] User notification sent', [
                'user_id' => $userId,
                'event' => $event,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('[PusherService] Failed to send user notification', [
                'user_id' => $userId,
                'event' => $event,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Broadcast custom event to a channel
     *
     * @param string $channel The channel name (without prefix)
     * @param string $event The event name
     * @param array $data The data to send
     * @return bool
     */
    public function broadcast(string $channel, string $event, array $data): bool
    {
        try {
            $this->pusher->trigger(
                $channel,
                $event,
                array_merge($data, ['timestamp' => now()->toIso8601String()])
            );

            Log::info('[PusherService] Custom broadcast sent', [
                'channel' => $channel,
                'event' => $event,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('[PusherService] Failed to send broadcast', [
                'channel' => $channel,
                'event' => $event,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get the Pusher instance for advanced usage
     *
     * @return Pusher
     */
    public function getPusher(): Pusher
    {
        return $this->pusher;
    }
}

