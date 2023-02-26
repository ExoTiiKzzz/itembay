<?php

namespace App\DataFixtures;

use App\Entity\Account;
use App\Entity\DefaultItem;
use App\Entity\Item;
use App\Entity\ItemType;
use App\Entity\PlayerClass;
use App\Entity\User;
use App\Service\DatabaseSeeder;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordEncoder;
    private EntityManagerInterface $entityManager;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager)
    {
        $this->passwordEncoder = $userPasswordHasher;
        $this->entityManager = $entityManager;
    }

    public function load(ObjectManager $manager): void
    {
        $seeder = new DatabaseSeeder($this->entityManager);
        $seeder->seed();
    }
}
