<?php

namespace App\Jobs;

use App\Enums\IntegrationEnum;
use App\Services\Ecommerce\WooCommerce\OrderService;
use App\Utils\CheckTokenUtils;
use Bugsnag\BugsnagLaravel\Facades\Bugsnag;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SyncWooCommerceOrders implements ShouldQueue
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
     *
     * @return void
     * @throws Throwable
     */
    public function handle(): void
    {
        foreach ($this->tenantIds as $tenantId) {
            try {
                if (!(new CheckTokenUtils())->execute($tenantId, IntegrationEnum::WOOCOMERCE)) {
                    continue;
                }

                tenancy()->initialize($tenantId);

                $orderService = new OrderService();
                $orders = $orderService->getOrders();
                $orderService->syncOrders($orders);

                tenancy()->end();
            } catch (Exception $e) {
                Bugsnag::notifyException($e, function ($report) use ($tenantId) {
                    $report->setMetaData(['tenant_id' => $tenantId]);
                });
                Log::error('Error during WooCommerce orders synchronization for tenant ' . $tenantId . ': ' . $e->getMessage());
            }
        }
    }
}
