<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testGetAndSetEmail(): void
    {
        $user = new User();
        $email = 'test@example.com';
        $user->setEmail($email);
        $this->assertSame($email, $user->getEmail());
    }

    public function testGetAndSetRoles(): void
    {
        $user = new User();
        $roles = ['ROLE_ADMIN'];
        $user->setRoles($roles);
        $this->assertSame(['ROLE_ADMIN', 'ROLE_USER'], $user->getRoles());
    }

    public function testGetAndSetPassword(): void
    {
        $user = new User();
        $password = 'hashed_password';
        $user->setPassword($password);
        $this->assertSame($password, $user->getPassword());
    }

    public function testGetAndSetName(): void
    {
        $user = new User();
        $name = 'John Doe';
        $user->setName($name);
        $this->assertSame($name, $user->getName());
    }

    public function testGetAndSetFirstname(): void
    {
        $user = new User();
        $firstname = 'John';
        $user->setFirstname($firstname);
        $this->assertSame($firstname, $user->getFirstname());
    }

    public function testGetAndSetLastConnexion(): void
    {
        $user = new User();
        $date = new \DateTimeImmutable('2025-01-01');
        $user->setLastConnexion($date);
        $this->assertSame($date, $user->getLastConnexion());
    }

    public function testGetUserEmail(): void
    {
        $user = new User();
        $email = 'JohnDoe@example.com';
        $user->setEmail($email);
        $this->assertSame($email, $user->getUserIdentifier());
    }

    public function testEraseCredentials(): void
    {
        $user = new User();
        $user->eraseCredentials();
        $this->assertEmpty($user->getId());
    }
}
