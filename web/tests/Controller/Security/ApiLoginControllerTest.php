<?php

namespace App\Tests\Controller\Security;

use App\Controller\Security\ApiLoginController;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Global\JsonValidator;
use App\Service\Security\ApiLogin;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ApiLoginControllerTest extends TestCase
{
    public function testSuccessfulLogin()
    {
        $jsonValidatorMock = $this->createMock(JsonValidator::class);
        $jsonValidatorMock->method('validateJson')->willReturn(null);

        $apiLoginMock = $this->createMock(ApiLogin::class);
        $apiLoginMock->method('createToken')->willReturn(new JsonResponse([
            'token' => 'fake_jwt_token'
        ], JsonResponse::HTTP_OK));

        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $jwtTokenManagerMock = $this->createMock(JWTTokenManagerInterface::class);
        $userRepositoryMock = $this->createMock(UserRepository::class);

        $controller = new ApiLoginController();

        $request = new Request([], [], [], [], [], [], json_encode([
            'email' => 'user@example.com',
            'password' => 'securepassword'
        ]));

        $response = $controller->index($jsonValidatorMock, $entityManagerMock, $request, $jwtTokenManagerMock, $apiLoginMock, $userRepositoryMock);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['token' => 'fake_jwt_token']),
            $response->getContent()
        );
    }

    public function testLoginWithInvalidJson()
    {
        $jsonValidatorMock = $this->createMock(JsonValidator::class);
        $jsonValidatorMock->method('validateJson')->willReturn('Invalid JSON format');

        $apiLoginMock = $this->createMock(ApiLogin::class);

        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $jwtTokenManagerMock = $this->createMock(JWTTokenManagerInterface::class);
        $userRepositoryMock = $this->createMock(UserRepository::class);

        $controller = new ApiLoginController();

        $request = new Request([], [], [], [], [], [], json_encode([
            'password' => 'securepassword'
        ]));

        $response = $controller->index($jsonValidatorMock, $entityManagerMock, $request, $jwtTokenManagerMock, $apiLoginMock, $userRepositoryMock);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['code' => 400, 'message' => 'Invalid JSON format']),
            $response->getContent()
        );
    }

    public function testGetUserInfo()
    {
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setFirstname('John');
        $user->setName('Doe');

        $controllerMock = $this->getMockBuilder(ApiLoginController::class)
            ->onlyMethods(['getUser'])
            ->getMock();

        $controllerMock->method('getUser')->willReturn($user);

        $response = $controllerMock->user();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode([
                'email' => 'user@example.com',
                'firstName' => 'John',
                'nom' => 'Doe'
            ]),
            $response->getContent()
        );
    }
}