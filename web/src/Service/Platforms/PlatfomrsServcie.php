<?php

namespace App\Service\Platforms;

use App\Entity\User;
use App\Service\Global\Cryptage;
use App\Service\Global\RequestApi;

class PlatfomrsServcie
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

    public function getPlatforms(
        User $user
    ): array
    {
        return [...$this->getJiraAccount($user)];

    }

    public function getJiraAccount(
        User $user
    ):array
    {
        $data = [];
        foreach ($user->getJiraAccounts() as $jiraAccount) {
            $data[] = [
                "type" => "jira",
                "url" => $jiraAccount->getUrl(),
                "projects" => $this->getJiraProjects(
                    $jiraAccount->getUrl(),
                    $jiraAccount->getApiToken()
                )
            ];
        }
        return $data;
    }

    public function getJiraProjects(
        $url,
        $auth_string
    ): array
    {
        $auth_string = $this->cryptage->decrypt($auth_string);
        $apiUrl = "https://$url/rest/api/3/project";

        $headers = [
            'Authorization' => "Basic $auth_string"
        ];

        try {
            $return = [];
            $data = $this->requestApi->send('GET', $apiUrl, $headers);
            foreach ($data as $project) {
                $return[] = [
                    "id" => $project['id'],
                    "name" => $project['name']
                ];
            }
            return $return;
        } catch (\Exception $e) {
            return [];
        }
    }
}