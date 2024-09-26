<?php

namespace App\Listeners;

use App\Events\OrderListUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Broadcast;

class ProcessOrderListUpdate implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(OrderListUpdated $event): void
    {
        $updated = $event->updated;

        Broadcast::channel(
            'order-list-updated',
            function () use ($updated) {
                return [
                    'updated' => $updated,
                ];
            }
        );
    }
}
