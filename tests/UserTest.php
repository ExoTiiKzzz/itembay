<?php

namespace App\Tests;

use App\Entity\User;
use App\Service\UserService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{

    public function testAdmin(): void
    {
        // (1) boot the Symfony kernel
        self::bootKernel();

        // (2) use static::getContainer() to access the service container
        $container = static::getContainer();

        //get entity manager
        $em = $container->get('doctrine')->getManager();

        //get user repository
        $userRepository = $em->getRepository(User::class);

        //get user with name 'admin'
        $user = $userRepository->findOneBy(['username' => 'admin']);

        //test if user is not null
        $this->assertNotNull($user, 'Admin is null');

        //test if user is instance of User
        $this->assertInstanceOf(User::class, $user, 'User is not instance of User');

        //test if user has role ROLE_ADMIN
        $this->assertContains('ROLE_ADMIN', $user->getRoles(), 'User has not role ROLE_ADMIN');

        //test if user has role ROLE_USER
        $this->assertContains('ROLE_USER', $user->getRoles(), 'User has not role ROLE_USER');

        //test if user has account
        $this->assertNotNull($user->getAccounts(), 'User has not account');

        //test if user has active account
        $this->assertNotNull($user->getActiveAccount(), 'User has not active account');
    }

    public function testUserCreation(): void
    {
        // (1) boot the Symfony kernel
        self::bootKernel();

        // (2) use static::getContainer() to access the service container
        $container = static::getContainer();

        //get entity manager
        $em = $container->get('doctrine')->getManager();

        //get user repository
        $userRepository = $em->getRepository(User::class);

        $userPasswordHasher = $container->get('security.user_password_hasher');

        //create new user
        $user = UserService::createUser($em, $userPasswordHasher, 'test', 'test');

        //test if user is not null
        $this->assertNotNull($user, 'User is null');

        //test to retrieve user
        $user = $userRepository->findOneBy(['username' => 'test']);

        //test if user is not null
        $this->assertNotNull($user, 'User is null');

        //create new user with same username
        $this->expectException(UniqueConstraintViolationException::class);
        $duplicateUser = UserService::createUser($em, $userPasswordHasher, 'test', 'test');

    }
}
