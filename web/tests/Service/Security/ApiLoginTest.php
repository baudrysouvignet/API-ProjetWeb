<?php

namespace App\Tests\Service\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Security\ApiLogin;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class ApiLoginTest extends TestCase
{
    private $JWTTokenManagerMock;
    private $userRepositoryMock;
    private $apiLoginService;

    protected function setUp(): void
    {
        $this->JWTTokenManagerMock = $this->createMock(JWTTokenManagerInterface::class);

        $this->userRepositoryMock = $this->createMock(UserRepository::class);

        $this->apiLoginService = new ApiLogin(
            $this->JWTTokenManagerMock,
            $this->userRepositoryMock
        );
    }

    public function testCreateTokenWithInvalidCredentials(): void
    {
        $data = ['email' => 'user@example.com', 'password' => 'wrongpassword'];

        $this->userRepositoryMock
            ->method('findOneBy')
            ->willReturn(null);

        $response = $this->apiLoginService->createToken($data);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(404, $responseData['code']);
        $this->assertEquals('Invalid credentials', $responseData['message']);
    }

    public function testCreateTokenWithValidCredentials(): void
    {
        $data = ['email' => 'user@example.com', 'password' => 'validpassword'];

        $user = new User();
        $user->setEmail('user@example.com');
        $user->setPassword(password_hash('validpassword', PASSWORD_DEFAULT));

        $this->userRepositoryMock
            ->method('findOneBy')
            ->willReturn($user);

        $this->userRepositoryMock
            ->method('setLastConnexion')
            ->willReturn(null);

        $this->JWTTokenManagerMock
            ->method('create')
            ->willReturn('fake-jwt-token');

        $response = $this->apiLoginService->createToken($data);

        $this->assertInstanceOf(JsonResponse::class, $response);

        $responseData = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('token', $responseData);
        $this->assertEquals('fake-jwt-token', $responseData['token']);
        $this->assertArrayHasKey('firstConnexion', $responseData);
        $this->assertTrue($responseData['firstConnexion']);
    }

    public function testCreateTokenWithIncorrectPassword(): void
    {
        $data = ['email' => 'user@example.com', 'password' => 'incorrectpassword'];

        $user = new User();
        $user->setEmail('user@example.com');
        $user->setPassword(password_hash('validpassword', PASSWORD_DEFAULT));

        $this->userRepositoryMock
            ->method('findOneBy')
            ->willReturn($user);

        $response = $this->apiLoginService->createToken($data);

        $this->assertInstanceOf(JsonResponse::class, $response);

        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals(404, $responseData['code']);
        $this->assertEquals('Invalid credentials', $responseData['message']);
    }
}
