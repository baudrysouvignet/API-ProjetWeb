<?php

namespace App\Service\Platforms;

use App\Entity\User;
use App\Service\Global\Cryptage;
use App\Service\Global\RequestApi;

class PlatfomrsServcie
{
    private RequestApi $requestApi;
    private Cryptage $cryptage;
    private JiraInfoService $jiraInfoService;

    public function __construct(
        RequestApi $requestApi,
        Cryptage $cryptage,
        JiraInfoService $jiraInfoService
    )
    {
        $this->requestApi = $requestApi;
        $this->cryptage = $cryptage;
        $this->jiraInfoService = $jiraInfoService;
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
                "id" => $jiraAccount->getId(),
                "projects" => $this->getJiraProjects(
                    $jiraAccount->getUrl(),
                    $jiraAccount->getApiToken()
                ),

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
        $apiUrl = "https://$url/rest/agile/1.0/board?projectKey=CCS";

        $headers = [
            'Authorization' => "Basic $auth_string"
        ];

        try {
            $return = [];
            $data = $this->requestApi->send('GET', $apiUrl, $headers);

            foreach ($data['values'] as $project) {
                $return[] = [

                    "boardId" => $project['id'],
                    "projectId" => $project['location']['projectId'],
                    "name" => $project['name'],
                    'issuesType' => $this->jiraInfoService->getJiraProjectsInfo(
                        $url,
                        $auth_string,
                        $project['location']['projectId']
                    )
                ];
            }
            return $return;
        } catch (\Exception $e) {
            return [];
        }
    }
}