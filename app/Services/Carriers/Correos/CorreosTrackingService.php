<?php

namespace App\Services\Carriers\Correos;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

class CorreosTrackingService
{
    private Client $client;
    private string $baseUri;
    private string $username;
    private string $password;

    public function __construct()
    {
        $this->client = new Client();
        $this->baseUri = 'https://localizador.correos.es/canonico/eventos_envio_servicio_auth';
        $this->username = 'dcarvalho@crazy4pets.pt';
        $this->password = 'Vvon64?7';
    }

    public function getTrackingInfo($trackingCode, $language = 'ES', $lastEvent = 'N')
    {
        $auth = base64_encode("{$this->username}:{$this->password}");

        $url = "{$this->baseUri}/{$trackingCode}?codIdioma={$language}&indUltEvento={$lastEvent}";
        try {
            $response = $this->client->request('GET', $url, [
                'headers' => [
                    'Authorization' => "Basic {$auth}",
                ],
            ]);


            return json_decode($response->getBody()->getContents(), true);

        } catch (RequestException $e) {
            return [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        } catch (GuzzleException $e) {
            return [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
    }
}
