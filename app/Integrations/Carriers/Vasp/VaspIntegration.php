<?php

namespace App\Integrations\Carriers\Vasp;

use App\Integrations\Integration;
use GuzzleHttp\Exception\GuzzleException;

class VaspIntegration extends Integration
{
    public function setHeaders($apiKey): array
    {
        $headers = parent::setHeaders($apiKey);
        $headers['Authorization'] = 'Bearer '.$apiKey['access_token'];

        return $headers;
    }

    public function genereteToken(array $apiKey, string $endpoint)
    {
        try {
            $data = [
                'grant_type' => 'password',
                'username' => $apiKey['username'],
                'password' => $apiKey['password'],
            ];

            $response = $this->client->post(
                parent::buildUrl($endpoint, []),
                [
                    'headers' => [
                        'Authorization' => 'Basic '.$apiKey['username'].':'.$apiKey['password'],
                        'Content-Type' => 'application/x-www-form-urlencoded',
                        'Accept' => 'application/json',
                    ],
                    'form_params' => $data,
                ]
            );
        } catch (GuzzleException $e) {
            return $e->getMessage();
        }

        return json_decode($response->getBody()->getContents(), true);
    }
}
