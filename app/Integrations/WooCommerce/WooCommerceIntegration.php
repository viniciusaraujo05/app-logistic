<?php

namespace App\Integrations\WooCommerce;

use App\Integrations\Integration;

class WooCommerceIntegration extends Integration
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
        $headers['Authorization'] = 'Basic '.base64_encode($apiKey['consumer_key'].':'.$apiKey['secret_key']);

        return $headers;
    }
}
