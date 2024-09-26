<?php

namespace App\Integrations;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Integration
{
    protected string $baseUrl;

    protected Client $client;

    public function __construct($baseUrl)
    {
        $this->baseUrl = $baseUrl;
        $this->client = new Client(['base_uri' => $this->baseUrl]);
    }

    public function get(array $apiKey, string $endpoint, array $params = [])
    {
        try {
            $response = $this->client->get(
                $this->buildUrl($endpoint, $params),
                [
                    'headers' => $this->setHeaders($apiKey),
                ]
            );

        } catch (GuzzleException $e) {
            return $e->getMessage();
        }

        return json_decode($response->getBody()->getContents(), true);
    }

    public function buildUrl($endpoint, $params): string
    {
        $url = $this->baseUrl . $endpoint;
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        return $url;
    }

    public function setHeaders($apiKey): array
    {
        return [
            'Authorization' => $apiKey,
            'Accept' => 'application/json',
        ];
    }

    public function patch(array $apiKey, string $endpoint, array $params = [], array $data = [])
    {
        try {
            $response = $this->client->patch(
                $this->buildUrl($endpoint, $params),
                [
                    'headers' => $this->setHeaders($apiKey),
                    'json' => $data,
                ]
            );
        } catch (GuzzleException $e) {
            return $e->getMessage();
        }

        return json_decode($response->getBody()->getContents(), true);
    }

    public function post(array $apiKey, string $endpoint, array $params = [], array $data = [])
    {
        try {
            $response = $this->client->post(
                $this->buildUrl($endpoint, $params),
                [
                    'headers' => $this->setHeaders($apiKey),
                    'json' => $data,
                ]
            );
        } catch (GuzzleException $e) {
            return $e->getMessage();
        }

        return json_decode($response->getBody()->getContents(), true);
    }
}
