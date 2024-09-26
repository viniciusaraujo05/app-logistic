<?php

namespace App\Integrations\Magento;

use App\Integrations\Integration;

class MagentoIntegration extends Integration
{
    /**
     * Set the headers for the API request using the provided API key.
     *
     * @param  mixed  $apiKey  The API key to be used for authorization.
     * @return array The headers for the API request.
     */
    public function setHeaders($apiKey): array
    {
        $headers = parent::setHeaders($apiKey);
        $headers['Authorization'] = 'Bearer '.$apiKey['api_token'];

        return $headers;
    }
}
