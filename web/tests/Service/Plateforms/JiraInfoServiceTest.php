<?php

namespace App\Tests\Service\Platforms;

use App\Service\Platforms\JiraInfoService;
use App\Service\Global\RequestApi;
use App\Service\Global\Cryptage;
use PHPUnit\Framework\TestCase;

class JiraInfoServiceTest extends TestCase
{
    private $jiraInfoService;
    private $requestApiMock;
    private $cryptageMock;

    protected function setUp(): void
    {
        $this->requestApiMock = $this->createMock(RequestApi::class);
        $this->cryptageMock = $this->createMock(Cryptage::class);
        
        $this->jiraInfoService = new JiraInfoService($this->requestApiMock, $this->cryptageMock);
    }

    public function testGetJiraProjectsInfo(): void
    {
        $mockResponse = [
            "issueTypes" => [
                ["name" => "Bug", "id" => 1],
                ["name" => "Feature", "id" => 2],
            ]
        ];

        $this->requestApiMock->expects($this->once())
            ->method('send')
            ->with(
                $this->equalTo('GET'),
                $this->stringContains('https://example.atlassian.net/rest/api/3/project/123'),
                $this->arrayHasKey('Authorization')
            )
            ->willReturn($mockResponse);

        $result = $this->jiraInfoService->getJiraProjectsInfo('example.atlassian.net', 'mockToken', 123);

        $this->assertCount(2, $result);
        $this->assertEquals([
            ["name" => "Bug", "id" => 1],
            ["name" => "Feature", "id" => 2],
        ], $result);
    }
}
