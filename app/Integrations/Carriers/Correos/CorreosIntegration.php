<?php

namespace App\Integrations\Carriers\Correos;

use App\Integrations\Integration;
use GuzzleHttp\Client;

class CorreosIntegration extends Integration
{
    protected string $baseUrl;

    protected Client $client;

    public function __construct($baseUrl)
    {
        $this->baseUrl = $baseUrl;
        $this->client = new Client(['base_uri' => $this->baseUrl, 'verify' => false]);
    }

    public function setHeaders($apiKey): array
    {
        $headers = parent::setHeaders($apiKey);
        $headers['Authorization'] = 'Basic '.base64_encode($apiKey['user'].':'.$apiKey['password']);

        return $headers;
    }
}
