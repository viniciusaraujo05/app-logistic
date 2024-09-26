<?php

namespace App\Jobs;

use App\Models\TrackingTransit;
use App\Utils\ProcessOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use Throwable;

class ProcessOrdersCompleted implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public array $tenantIds;

    /**
     * Create a new job instance.
     *
     * @param array $tenantIds
     * @return void
     */
    public function __construct(array $tenantIds)
    {
        $this->tenantIds = $tenantIds;
    }

    /**
     * Execute the job.
     * @throws TenantCouldNotBeIdentifiedById|Throwable
     */
    public function handle(): void
    {
        foreach ($this->tenantIds as $tenantId) {
            tenancy()->initialize($tenantId);

            if (!TrackingTransit::query()->exists()) {
                tenancy()->end();
                continue;
            }

            $trackingInfos = $this->getTracking();

            foreach ($trackingInfos as $trackingInfo) {
                (new ProcessOrder())->execute($trackingInfo);
            }

            tenancy()->end();
        }
    }

    private function getTracking(): array
    {
        return TrackingTransit::query()
            ->select(
                'tracking_transit.order_id',
                'tracking.tracking',
                'tracking.integration_id',
                'tracking.carrier_name'
            )
            ->join('tracking', 'tracking_transit.tracking_id', '=', 'tracking.id')
            ->get()
            ->all();
    }
}
