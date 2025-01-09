<?php

namespace App\Service\Platforms;

use App\Entity\User;

class PlatfomrsServcie
{
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
            ];
        }
        return $data;
    }
}