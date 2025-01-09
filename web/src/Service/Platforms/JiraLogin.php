<?php

namespace App\Service\Platforms;

use App\Repository\JiraInfoRepository;
use App\Service\Global\Cryptage;
use App\Service\Global\RequestApi;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class JiraLogin
{
    private $requestApi;
    private $tokenStorage;
    private $jiraInfoRepository;

    private $cryptage;

    public function __construct(
        RequestApi $requestApi,
        TokenStorageInterface $tokenStorage,
        JiraInfoRepository $jiraInfoRepository,
        Cryptage $cryptage
    )
    {
        $this->requestApi = $requestApi;
        $this->tokenStorage = $tokenStorage;
        $this->jiraInfoRepository = $jiraInfoRepository;
        $this->cryptage = $cryptage;
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

        $jiraInfo = $this->jiraInfoRepository->findOneBy([
            'url' => $url,
            'user' => $this->tokenStorage->getToken()->getUser()
        ]);
        if ($jiraInfo) {
            return new JsonResponse([
                'code' => 400,
                'message' => 'You already have a Jira account with this url'
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

        $token_crypted= $this->cryptage->encrypt($auth_string);
        $user = $this->tokenStorage->getToken()->getUser();
        $this->jiraInfoRepository->createJirainfo($user, $url, $token_crypted);


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
            $this->requestApi->send('GET', $apiUrl, $headers);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}