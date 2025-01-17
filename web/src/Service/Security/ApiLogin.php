<?php

namespace App\Service\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiLogin
{
    private JWTTokenManagerInterface $JWTTokenManager;
    private UserRepository $userRepository;
    public function __construct(
        JWTTokenManagerInterface $JWTTokenManager,
        UserRepository $userRepository
    )
    {
        $this->JWTTokenManager = $JWTTokenManager;
        $this->userRepository = $userRepository;
    }

    public function createToken(array $data):JsonResponse
    {
        $user = $this->userRepository->findOneBy(['email' => $data['email']]);

        if (!$user || !password_verify($data['password'], $user->getPassword())) {
            return new JsonResponse([
                'code' => 404,
                'message' => 'Invalid credentials'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $firstConnexion = $user->getLastConnexion() === null;
        $this->userRepository->setLastConnexion($user);

        return new JsonResponse([
            'token' => $this->JWTTokenManager->create($user),
            'firstConnexion' => $firstConnexion
        ]);
    }
}