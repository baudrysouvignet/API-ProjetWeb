<?php

namespace App\Service\Platforms;

use App\Entity\JiraInfo;
use App\Entity\User;
use App\Service\Global\Cryptage;
use App\Service\Global\RequestApi;
use Symfony\Component\HttpFoundation\JsonResponse;

class JiraInfoService
{
    private RequestApi $requestApi;
    private Cryptage $cryptage;
    public function __construct(
        RequestApi $requestApi,
        Cryptage $cryptage
    )
    {
        $this->requestApi = $requestApi;
        $this->cryptage = $cryptage;
    }

    public function getJiraProjectsInfo(
        string $urlJira,
        string $token,
        int $id
    ): array
    {
        $url = "https://$urlJira/rest/api/3/project/$id";
        $headers = [
            'Authorization' => "Basic $token",
            'Content-Type' => 'application/json',
        ];

        $value = $this->requestApi->send('GET', $url, $headers);

        $result = [];
        foreach ($value["issueTypes"] as $issues) {
            $result[] = [
                "name" => $issues["name"],
                "id" => $issues["id"]
            ];
        }
        return $result;
    }
}