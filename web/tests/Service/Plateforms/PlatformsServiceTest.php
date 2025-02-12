<?php
use App\Entity\User;
use App\Entity\JiraInfo;
use App\Service\Global\RequestApi;
use App\Service\Global\Cryptage;
use App\Service\Platforms\JiraInfoService;
use App\Service\Platforms\PlatformsService;
use PHPUnit\Framework\TestCase;
use Doctrine\Common\Collections\ArrayCollection;

class PlatformsServiceTest extends TestCase
{
    public function testGetPlatforms()
    {
        $requestApiMock = $this->createMock(RequestApi::class);

        $requestApiMock->method('send')
            ->willReturn([
                'values' => [
                    [
                        'id' => 1,
                        'name' => 'Project 1',
                        'location' => [
                            'projectId' => 123
                        ]
                    ]
                ]
            ]);

        $cryptageMock = $this->createMock(Cryptage::class);
        $cryptageMock->method('decrypt')->willReturn('mockDecryptedString');

        $jiraInfoMock = $this->createMock(JiraInfo::class);
        $jiraInfoMock->method('getUrl')->willReturn('example.atlassian.net');
        $jiraInfoMock->method('getId')->willReturn(123);
        $jiraInfoMock->method('getApiToken')->willReturn('mockApiToken');

        $userMock = $this->createMock(User::class);
        $userMock->method('getJiraAccounts')->willReturn(new ArrayCollection([$jiraInfoMock]));

        $jiraInfoServiceMock = $this->createMock(JiraInfoService::class);


        $platformsService = new PlatformsService(
            $requestApiMock,
            $cryptageMock,
            $jiraInfoServiceMock
        );
        $result = $platformsService->getPlatforms($userMock);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
    }
}