<?php

namespace App\Tests\Service\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Security\ApiRegistration;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiRegistrationTest extends TestCase
{
    private ApiRegistration $apiRegistration;
    private $entityManager;
    private $userRepository;

    protected function setUp(): void
    {
        parent::setUp();

        // Mocks pour les dépendances
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->userRepository = $this->createMock(UserRepository::class);

        // Initialisation de la classe à tester
        $this->apiRegistration = new ApiRegistration($this->entityManager, $this->userRepository);
    }

    public function testValidateAndCreateUserWithInvalidEmail(): void
    {
        $data = ['email' => 'invalid-email', 'password' => 'password123'];

        $response = $this->apiRegistration->validateAndCreateUser($data);

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['code' => ApiRegistration::CODE_BAD_REQUEST, 'message' => ApiRegistration::MESSAGE_EMAIL_INVALID]),
            $response->getContent()
        );
    }

    public function testCreateUserSuccessfully(): void
    {
        $data = [
            'email' => 'new-user@example.com',
            'password' => 'password123',
            'firstname' => 'John',
            'name' => 'Doe'
        ];

        // Simuler un utilisateur non existant
        $this->userRepository
            ->method('findOneBy')
            ->willReturn(null);

        // Simuler le token JWT
        $this->userRepository
            ->method('getJWTToken')
            ->willReturn('sample-jwt-token');

        // Simuler la persistance de l'utilisateur
        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(User::class));

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $response = $this->apiRegistration->validateAndCreateUser($data);

        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode([
                'code' => ApiRegistration::CODE_CREATED,
                'token' => 'sample-jwt-token',
                'message' => ApiRegistration::MESSAGE_USER_CREATED_SUCCESS,
            ]),
            $response->getContent()
        );
    }
}
