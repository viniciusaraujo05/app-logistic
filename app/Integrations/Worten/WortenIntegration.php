<?php

namespace App\Integrations\Worten;

use App\Integrations\Integration;

class WortenIntegration extends Integration
{
    /**
     * Set the headers for the API request using the provided API key.
     *
     * @param  array  $apiKey  The API key to be used for authorization.
     * @return array The headers for the API request.
     */
    public function setHeaders($apiKey): array
    {
        $headers = parent::setHeaders($apiKey);
        $headers['Authorization'] = $apiKey['api_token'];

        return $headers;
    }
}
