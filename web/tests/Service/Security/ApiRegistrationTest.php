<?php

namespace App\Tests\Service\Security;

use App\Entity\User;
use App\Service\Security\ApiRegistration;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use PHPUnit\Framework\TestCase;

class ApiRegistrationTest extends TestCase
{
    private ApiRegistration $apiRegistration;
    private EntityManagerInterface $entityManager;
    private ObjectRepository $userRepository;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->userRepository = $this->createMock(EntityRepository::class);

        $this->entityManager
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($this->userRepository);

        $this->apiRegistration = new ApiRegistration($this->entityManager);
    }

    public function testValidateAndCreateUserWithInvalidEmail(): void
    {
        $response = $this->apiRegistration->validateAndCreateUser('invalid-email', 'password');

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['code' => 400, 'message' => 'Email is invalid']),
            $response->getContent()
        );
    }

    public function testValidateAndCreateUserWhenUserExists(): void
    {

        $this->userRepository
            ->method('findOneBy')
            ->willReturn(new User());

        $response = $this->apiRegistration->validateAndCreateUser('test@test.com', 'password');

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(409, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['code' => 409, 'message' => 'User with this email already exists']),
            $response->getContent()
        );
    }

    public function testValidateAndCreateUserSuccessfully(): void
    {
        $this->userRepository
            ->method('findOneBy')
            ->willReturn(null);


        $this->entityManager
            ->expects($this->once())
            ->method('persist');

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $response = $this->apiRegistration->validateAndCreateUser('test@test.com', 'password');

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['code' => 201, 'message' => 'User created successfully']),
            $response->getContent()
        );
    }

    public function testIsValidEmail(): void
    {
        $reflection = new \ReflectionClass(ApiRegistration::class);
        $method = $reflection->getMethod('isValidEmail');
        $method->setAccessible(true);

        $this->assertTrue($method->invoke($this->apiRegistration, 'test@test.com'));
        $this->assertFalse($method->invoke($this->apiRegistration, 'invalid-email'));
    }
}
