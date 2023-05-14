<?php

namespace App\Service;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public static function createUser(EntityManagerInterface $em, UserPasswordHasherInterface $userPasswordHasher, string $username, string $password): User
    {
        $user = new User();
        $user->setUsername($username);
        //set password
        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                $password
            )
        );

        $em->persist($user);
        $em->flush();

        return $user;
    }
}