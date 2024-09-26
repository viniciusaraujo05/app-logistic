<?php

namespace App\Actions\Orders\Worten;

use App\Jobs\SyncWortenOrders;
use App\Models\Tenant;
use Exception;
use Illuminate\Support\Facades\Log;

class WortenUpdate
{
    /**
     * Executes the Worten order synchronization process for all tenants.
     *
     * @return void
     */
    public function execute(): void
    {
        try {
            $tenantIds = Tenant::query()->pluck('id')->toArray();

            SyncWortenOrders::dispatch($tenantIds);
        } catch (Exception $e) {
            Log::error('Error during Worten orders synchronization: ' . $e->getMessage());
        }
    }
}
