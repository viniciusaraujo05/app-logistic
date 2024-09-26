<?php

namespace App\Actions\Orders\WooCommerce;

use App\Jobs\SyncWooCommerceOrders;
use App\Models\Tenant;
use Illuminate\Support\Facades\Log;

class WooCommerceUpdate
{
    /**
     * Executes the synchronization of WooCommerce orders for all tenants.
     *
     * @return bool Returns true if the synchronization is successful for all tenants,
     *              false otherwise.
     */
    public function execute(): bool
    {
        try {
            $tenantIds = Tenant::query()->pluck('id')->toArray();

            SyncWooCommerceOrders::dispatch($tenantIds);

            return true;
        } catch (\Exception $e) {
            Log::error('Error during WooCommerce orders synchronization: ' . $e->getMessage());

            return false;
        }
    }
}
