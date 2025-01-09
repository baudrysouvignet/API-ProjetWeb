<?php

namespace App\Service\Platforms;

use App\Service\Global\RequestApi;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class JiraLogin
{
    private $requestApi;

    public function __construct(RequestApi $requestApi)
    {
        $this->requestApi = $requestApi;
    }

    public function connect(
        string $email,
        string $url,
        string $token
    ):JsonResponse
    {
        if (!preg_match('/^[a-zA-Z0-9-]+\\.atlassian\\.net$/', $url)) {
            return new JsonResponse([
                'code' => 400,
                'message' => 'Invalid url format for Jira'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $auth_string = base64_encode("$email:$token");

        $testConnexion = $this->testConnexion($url, $auth_string);
        if (!$testConnexion) {
            return new JsonResponse([
                'code' => 400,
                'message' => 'Invalid credentials'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse([
            'code' => 200,
            'message' => 'Connection successful'
        ], JsonResponse::HTTP_OK);
    }

    public function testConnexion(
        string $url,
        string $auth_string
    ): bool
    {
        $apiUrl = "https://$url/rest/api/3/myself";

        $headers = [
            'Authorization' => "Basic $auth_string",
            'Content-Type' => 'application/json',
        ];

        try {
            $data = $this->requestApi->send('GET', $apiUrl, $headers);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}