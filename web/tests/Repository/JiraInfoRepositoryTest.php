<?php

namespace App\Tests\Repository;

use App\Entity\JiraInfo;
use App\Entity\User;
use App\Repository\JiraInfoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

class JiraInfoRepositoryTest extends TestCase
{
    private $entityManagerMock;
    private $managerRegistryMock;
    private $jiraInfoRepository;

    protected function setUp(): void
    {
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $this->managerRegistryMock = $this->createMock(ManagerRegistry::class);

        $this->jiraInfoRepository = new JiraInfoRepository(
            $this->managerRegistryMock,
            $this->entityManagerMock
        );
    }

    public function testCreateJiraInfo(): void
    {
        $user = new User();
        $url = 'https://example.atlassian.net';
        $token = 'fake-token';

        $this->entityManagerMock
            ->expects($this->once())
            ->method('persist')
            ->with($this->callback(function (JiraInfo $jiraInfo) use ($user, $url, $token) {
                return $jiraInfo->getUser() === $user &&
                       $jiraInfo->getUrl() === $url &&
                       $jiraInfo->getApiToken() === $token;
            }));

        $this->entityManagerMock
            ->expects($this->once())
            ->method('flush');

        $this->jiraInfoRepository->createJirainfo($user, $url, $token);
    }

    public function testDeleteJiraInfo(): void
    {
        $jiraInfo = new JiraInfo();

        $this->entityManagerMock
            ->expects($this->once())
            ->method('remove')
            ->with($this->equalTo($jiraInfo));

        $this->entityManagerMock
            ->expects($this->once())
            ->method('flush');

        $this->jiraInfoRepository->deleteJiraInfo($jiraInfo);
    }
}
