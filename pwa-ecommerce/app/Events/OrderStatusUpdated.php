<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event for order status updates broadcast via Pusher
 *
 * This event is used for order status live updates (not payment events).
 * Payment events use Reverb with private channels.
 * Order status updates use Pusher with public channels for broader accessibility.
 *
 * Channel: order.{orderCode}
 * Event: order.status
 */
class OrderStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The order code
     */
    public string $orderCode;

    /**
     * The new status
     */
    public string $status;

    /**
     * Human-readable message
     */
    public string $message;

    /**
     * Additional data
     */
    public array $data;

    /**
     * Create a new event instance
     *
     * @param string $orderCode The order code (e.g., 'ORD-XXXXXXXXXX')
     * @param string $status The new status (e.g., 'processing', 'shipped', 'delivered')
     * @param string $message Human-readable message
     * @param array $data Additional data to broadcast
     */
    public function __construct(string $orderCode, string $status, string $message, array $data = [])
    {
        $this->orderCode = $orderCode;
        $this->status = $status;
        $this->message = $message;
        $this->data = $data;
    }

    /**
     * Get the channels the event should broadcast on
     *
     * Uses public channel (order.{orderCode}) for order status updates
     * Unlike payment events which use private channels
     *
     * @return Channel[]
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('order.' . $this->orderCode),
        ];
    }

    /**
     * Get the data to broadcast
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'order_code' => $this->orderCode,
            'status' => $this->status,
            'message' => $this->message,
            'data' => $this->data,
            'timestamp' => now()->toIso8601String(),
        ];
    }

    /**
     * The event name
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'order.status';
    }

    /**
     * Determine if the event should broadcast
     *
     * @return bool
     */
    public function shouldBroadcast(): bool
    {
        return !empty($this->orderCode) && !empty($this->status);
    }
}

