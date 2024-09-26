<?php

namespace App\Actions\Orders\Magento;

use App\Jobs\SyncMagentoOrders;
use App\Models\Tenant;
use Exception;
use Illuminate\Support\Facades\Log;

class MagentoUpdate
{
    /**
     * Executes the synchronization of Magento orders for all tenants.
     *
     * @return bool Returns true if the synchronization is successful for all tenants,
     *              false otherwise.
     */
    public function execute(): bool
    {
        try {
            $tenantIds = Tenant::query()->pluck('id')->toArray();

            SyncMagentoOrders::dispatch($tenantIds);

            return true;
        } catch (Exception $e) {
            Log::error('Error during Magento orders synchronization: ' . $e->getMessage());

            return false;
        }
    }
}
