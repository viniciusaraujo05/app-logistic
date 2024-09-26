<?php

namespace App\Utils;

use App\Repositories\TokenRepository;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;

class CheckTokenUtils
{
    /**
     * @throws TenantCouldNotBeIdentifiedById
     */
    public function execute($tenantId, string $integration): bool
    {
        tenancy()->initialize($tenantId);

        $integrationToken = (new TokenRepository())->get($integration);
        tenancy()->end();

        return !$integrationToken->isEmpty();
    }
}
