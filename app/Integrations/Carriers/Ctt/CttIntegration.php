<?php

namespace App\Integrations\Carriers\Ctt;

use App\Integrations\Integration;
use GuzzleHttp\Exception\GuzzleException;

class CttIntegration extends Integration
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
        $headers['Content-Type'] = $apiKey['Content-Type'];
        $headers['SOAPAction'] = $apiKey['SOAPAction'];

        return $headers;
    }

    public function post(array $headers, string $endpoint, array $params = [], $data = '')
    {
        try {
            $response = $this->client->post(
                $this->buildUrl($endpoint, $params),
                [
                    'headers' => $headers,
                    'body' => $data,
                ]
            );

            return $response->getBody()->getContents();
        } catch (GuzzleException $e) {
            return $e->getMessage();
        }
    }
}
