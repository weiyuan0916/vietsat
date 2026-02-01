<?php

namespace App\Events;

use App\Models\ServiceOrder;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentSuccess
{
    use Dispatchable, SerializesModels;

    public function __construct(public ServiceOrder $order) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('order.' . $this->order->order_code),
        ];
    }
}
