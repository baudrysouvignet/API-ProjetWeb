<?php

namespace App\Tests\Service\Platforms;

use App\Entity\User;
use App\Repository\JiraInfoRepository;
use App\Service\Global\Cryptage;
use App\Service\Global\RequestApi;
use App\Service\Platforms\JiraLogin;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class JiraLoginTest extends TestCase
{
    private $requestApiMock;
    private $tokenStorageMock;
    private $jiraInfoRepositoryMock;
    private $cryptageMock;
    private $jiraLogin;

    protected function setUp(): void
    {
        $this->requestApiMock = $this->createMock(RequestApi::class);
        $this->tokenStorageMock = $this->createMock(TokenStorageInterface::class);
        $this->jiraInfoRepositoryMock = $this->createMock(JiraInfoRepository::class);
        $this->cryptageMock = $this->createMock(Cryptage::class);

        $this->jiraLogin = new JiraLogin(
            $this->requestApiMock,
            $this->tokenStorageMock,
            $this->jiraInfoRepositoryMock,
            $this->cryptageMock
        );
    }

    private function createUserMock(): User
    {
        return $this->createMock(User::class);
    }


    public function testConnectInvalidUrl()
    {
        $response = $this->jiraLogin->connect('test@example.com', 'invalid-url', 'token');
        $this->assertEquals(JsonResponse::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals([
            'code' => 400,
            'message' => 'Invalid url format for Jira',
        ], json_decode($response->getContent(), true));
    }

    public function testConnectExistingJiraInfo()
    {
        $userMock = $this->createUserMock();
        $tokenMock = $this->createMock(TokenInterface::class);

        $tokenMock
            ->method('getUser')
            ->willReturn($userMock);

        $this->tokenStorageMock
            ->method('getToken')
            ->willReturn($tokenMock);

        $this->jiraInfoRepositoryMock
            ->method('findOneBy')
            ->willReturn(new \stdClass());

        $response = $this->jiraLogin->connect('test@example.com', 'example.atlassian.net', 'token');
        $this->assertEquals(JsonResponse::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals([
            'code' => 400,
            'message' => 'You already have a Jira account with this url',
        ], json_decode($response->getContent(), true));
    }

    public function testConnectInvalidCredentials()
    {
        $userMock = $this->createUserMock();
        $tokenMock = $this->createMock(TokenInterface::class);

        $tokenMock
            ->method('getUser')
            ->willReturn($userMock);

        $this->tokenStorageMock
            ->method('getToken')
            ->willReturn($tokenMock);

        $this->jiraInfoRepositoryMock
            ->method('findOneBy')
            ->willReturn(null);

        $this->requestApiMock
            ->method('send')
            ->will($this->throwException(new \Exception()));

        $response = $this->jiraLogin->connect('test@example.com', 'example.atlassian.net', 'token');
        $this->assertEquals(JsonResponse::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals([
            'code' => 400,
            'message' => 'Invalid credentials',
        ], json_decode($response->getContent(), true));
    }

    public function testConnectSuccess()
    {
        $userMock = $this->createUserMock(); // Mock a User entity
        $tokenMock = $this->createMock(TokenInterface::class);

        $tokenMock
            ->method('getUser')
            ->willReturn($userMock);

        $this->tokenStorageMock
            ->method('getToken')
            ->willReturn($tokenMock);

        $this->jiraInfoRepositoryMock
            ->method('findOneBy')
            ->willReturn(null);

        $this->cryptageMock
            ->method('encrypt')
            ->willReturn('encrypted_token');

        // Mock 'send' method to return an array instead of a boolean
        $this->requestApiMock
            ->method('send')
            ->willReturn(['status' => 'success']); // Return an array as expected by the method

        // Expect the method to be called with a User object and the encrypted token
        $this->jiraInfoRepositoryMock
            ->expects($this->once())
            ->method('createJirainfo')
            ->with($this->isInstanceOf(User::class), 'example.atlassian.net', 'encrypted_token');

        $response = $this->jiraLogin->connect('test@example.com', 'example.atlassian.net', 'token');
        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertEquals([
            'code' => 200,
            'message' => 'Connection successful',
        ], json_decode($response->getContent(), true));
    }
}
