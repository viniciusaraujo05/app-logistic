<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderListUpdated implements ShouldBroadcast
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Indica se a lista de pedidos foi atualizada.
     */
    public bool $updated;

    /**
     * Create a new event instance.
     */
    public function __construct(bool $updated)
    {
        $this->updated = $updated;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): Channel|array
    {
        return new Channel('order-list-updated');
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'updated' => $this->updated,
        ];
    }
}
