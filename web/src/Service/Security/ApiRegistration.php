<?php

namespace App\Service\Security;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiRegistration
{
    public const CODE_BAD_REQUEST = 400;
    public const CODE_CONFLICT = 409;
    public const CODE_CREATED = 201;

    public const MESSAGE_EMAIL_INVALID = 'Email is invalid';
    public const MESSAGE_USER_EXISTS = 'User with this email already exists';
    public const MESSAGE_USER_CREATED_SUCCESS = 'User created successfully';


    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function validateAndCreateUser(
        array $data,
    ):JsonResponse
    {
        $email = $data['email'];
        $password = $data['password'];

        if (!$this->isValidEmail($email)) {
            return new JsonResponse([
                'code' => self::CODE_BAD_REQUEST,
                'message' => self::MESSAGE_EMAIL_INVALID,
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        if ($this->isUserExists($email)) {
            return new JsonResponse([
                'code' => self::CODE_CONFLICT,
                'message' => self::MESSAGE_USER_EXISTS,
            ], JsonResponse::HTTP_CONFLICT);
        }

        return $this->createUser($data);
    }
    private function createUser(array $data): JsonResponse
    {
        $user = new User();
        $user->setEmail($data['email']);

        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
        $user->setPassword($hashedPassword);

        if (isset($data['firstname'])) {
            $user->setFirstname($data['firstname']);
        }
        if (isset($data['name'])) {
            $user->setName($data['name']);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse([
            'code' => self::CODE_CREATED,
            'message' => self::MESSAGE_USER_CREATED_SUCCESS,
        ], JsonResponse::HTTP_CREATED);
    }

    private function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    private function isUserExists(string $email): bool
    {
        return $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]) !== null;
    }
}
