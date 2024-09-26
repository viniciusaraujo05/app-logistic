<?php

namespace App\Utils;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class LabelUtils
{
    /**
     * Converts a ZPL string to a PDF using the Labelary API.
     *
     * @param  string  $zpl  The ZPL string to convert.
     * @return string The converted PDF content.
     *
     * @throws Exception If the conversion fails.
     * @throws GuzzleException
     */
    public static function convertZplToPdf(string $zpl): string
    {
        $client = new Client();
        $url = 'http://api.labelary.com/v1/printers/8dpmm/labels/4x6/0/';
        try {
            $response = $client->request('POST', $url, [
                'headers' => [
                    'Accept' => 'application/pdf',
                ],
                'body' => $zpl,
            ]);

            return $response->getBody()->getContents();
        } catch (RequestException $e) {
            return false;
        }
    }
}
