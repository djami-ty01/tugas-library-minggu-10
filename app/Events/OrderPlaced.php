<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderPlaced implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly int $orderId,
        public readonly string $receiptNumber,
        public readonly float $total,
        public readonly string $cashierName,
    ) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('orders');
    }

    public function broadcastAs(): string
    {
        return 'order.placed';
    }

    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->orderId,
            'receipt_number' => $this->receiptNumber,
            'total' => $this->total,
            'cashier_name' => $this->cashierName,
        ];
    }
}
