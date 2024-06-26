<?php

namespace App\Services;

use GuzzleHttp\Client;

class RapidAPIService
{
    protected $client;
    protected $apiHost;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiHost = env('RAPIDAPI_HOST');
        $this->apiKey = env('RAPIDAPI_KEY');
    }

    public function getData($endpoint, $params = [])
    {
        try {
            $response = $this->client->request('GET', $endpoint, [
                'headers' => [
                    'x-rapidapi-host' => $this->apiHost,
                    'x-rapidapi-key' => $this->apiKey,
                ],
                'query' => $params,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            // Handle exception or log error
            return ['error' => $e->getMessage()];
        }
    }
}
