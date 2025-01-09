<?php

namespace App\Service\Global;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RequestApi
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function send(
        string $method,
        string $apiUrl,
        array $headers,
    ): array
    {
        $response = $this->client->request($method, $apiUrl, [
            'headers' => $headers,
        ]);

        return $response->toArray();
    }
}