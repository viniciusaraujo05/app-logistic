<?php

namespace App\Jobs;

use App\Events\OrderListUpdated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class OrderUpdated implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Indica se a lista de pedidos foi atualizada.
     */
    protected bool $updated;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(bool $updated)
    {
        $this->updated = $updated;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $event = new OrderListUpdated($this->updated);

        event($event);
    }
}
