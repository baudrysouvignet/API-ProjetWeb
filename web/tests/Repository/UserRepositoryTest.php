<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ManagerRegistry;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use PHPUnit\Framework\TestCase;

class UserRepositoryTest extends TestCase
{
    private $entityManager;
    private $jwtManager;
    private $userRepository;
    private $managerRegistry;

    protected function setUp(): void
    {
        $classMetadata = $this->createMock(ClassMetadata::class);
        $classMetadata->name = User::class; // 🚀 Ajoute cette ligne !

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->entityManager->method('getClassMetadata')->willReturn($classMetadata); // 🚀 Ajoute cette ligne !

        $this->managerRegistry = $this->createMock(ManagerRegistry::class);
        $this->managerRegistry->method('getManager')->willReturn($this->entityManager);
        $this->managerRegistry->method('getManagerForClass')->willReturn($this->entityManager);
        $this->managerRegistry->method('getRepository')->willReturn($this->createMock(UserRepository::class));

        $this->jwtManager = $this->createMock(JWTTokenManagerInterface::class);

        $this->userRepository = new UserRepository($this->managerRegistry, $this->jwtManager);
    }

    public function testSetLastConnexion(): void
    {
        $user = $this->createMock(User::class);
        $user->expects($this->once())->method('setLastConnexion');

        $this->entityManager->expects($this->once())->method('flush');

        $this->userRepository->setLastConnexion($user);
    }

    public function testUpgradePassword(): void
    {
        $user = $this->createMock(User::class);
        $newPassword = 'hashed_password';

        $user->expects($this->once())->method('setPassword')->with($newPassword);
        $this->entityManager->expects($this->once())->method('persist')->with($user);
        $this->entityManager->expects($this->once())->method('flush');

        $this->userRepository->upgradePassword($user, $newPassword);
    }

    public function testGetJWTToken(): void
    {
        $user = $this->createMock(User::class);
        $token = 'jwt_token';

        $this->jwtManager->expects($this->once())->method('create')->with($user)->willReturn($token);

        $this->assertEquals($token, $this->userRepository->getJWTToken($user));
    }
}
