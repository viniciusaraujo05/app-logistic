<?php

namespace App\Integrations\KuantoKusta;

use App\Integrations\Integration;

class KuantoKustaIntegration extends Integration
{
    public function setHeaders($apiKey): array
    {
        $headers = parent::setHeaders($apiKey);
        $headers['x-api-key'] = $apiKey['api_token'];

        return $headers;
    }
}
