<?php

namespace App\Events;

use App\Models\ServiceOrder;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentExpired implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public ServiceOrder $order) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('order.' . $this->order->order_code),
        ];
    }

    public function broadcastAs(): string
    {
        return 'payment.expired';
    }

    public function broadcastWith(): array
    {
        return [
            'order_code' => $this->order->order_code,
            'status' => $this->order->status,
            'expires_at' => $this->order->expires_at?->toIso8601String(),
            'message' => 'Đơn hàng đã hết hạn',
        ];
    }
}
