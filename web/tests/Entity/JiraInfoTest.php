<?php

namespace App\Tests\Entity;

use App\Entity\JiraInfo;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class JiraInfoTest extends TestCase
{
    public function testGetAndSetId(): void
    {
        $jiraInfo = new JiraInfo();
        $this->assertNull($jiraInfo->getId(), 'ID should initially be null.');
    }

    public function testGetAndSetUser(): void
    {
        $jiraInfo = new JiraInfo();
        $user = new User();

        $jiraInfo->setUser($user);

        $this->assertSame($user, $jiraInfo->getUser(), 'The setUser method should correctly assign the User object.');
    }

    public function testGetAndSetUrl(): void
    {
        $jiraInfo = new JiraInfo();
        $url = 'https://example.atlassian.net';

        $jiraInfo->setUrl($url);

        $this->assertEquals($url, $jiraInfo->getUrl(), 'The setUrl method should correctly assign the URL.');
    }

    public function testGetAndSetApiToken(): void
    {
        $jiraInfo = new JiraInfo();
        $apiToken = 'exampleapitoken123';

        $jiraInfo->setApiToken($apiToken);

        $this->assertEquals($apiToken, $jiraInfo->getApiToken(), 'The setApiToken method should correctly assign the API token.');
    }
}
