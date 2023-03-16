<?php

namespace App\Service;

use App\Entity\Account;
use App\Entity\DefaultItem;
use App\Entity\Item;
use App\Entity\ItemNature;
use App\Entity\ItemType;
use App\Entity\PlayerClass;
use App\Entity\PlayerProfession;
use App\Entity\Profession;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class DatabaseSeeder
{
    private string $baseApiUrl = 'https://fr.dofus.dofapi.fr/';

    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public function seed(): void
    {
        $this->seedClasses();
        $this->seedProfessions();
        $this->seedAllItems();
        $this->seedUsers();
    }

    private function getAllData(string $suffix): array
    {
        return json_decode(file_get_contents($this->baseApiUrl . $suffix), true);
    }

    private function seedClasses(): void
    {
        $classes = $this->getAllData('classes');
        foreach ($classes as $class) {
            $classEntity = new PlayerClass();
            $classEntity->setAnkamaId($class['_id']);
            $classEntity->setName($class['name']);
            $classEntity->setDescription($class['description']);
            $this->entityManager->persist($classEntity);
        }
        $this->entityManager->flush();
    }

    private function seedProfessions(): void
    {
        $professions = $this->getAllData('professions');
        foreach ($professions as $profession) {
            $professionEntity = new Profession();
            $professionEntity->setAnkamaId($profession['_id']);
            $professionEntity->setName($profession['name']);
            $professionEntity->setDescription($profession['description']);
            $this->entityManager->persist($professionEntity);
        }
        $this->entityManager->flush();
    }

    private function seedAllItems(): void
    {
        $unknownItemNature = new ItemNature();
        $unknownItemNature->setName('Unknown');
        $this->entityManager->persist($unknownItemNature);
        $this->entityManager->flush();


        $itemType = new ItemType();
        $itemType->setName('Unknown');
        $itemType->setItemNature($unknownItemNature);
        $this->entityManager->persist($itemType);
        $itemNatures = [
            [
                'url' => 'consumables',
                'itemNature' => 'Consommables'
            ],
            [
                'url' => 'equipments',
                'itemNature' => 'Equipements'
            ],
            [
                'url' => 'resources',
                'itemNature' => 'Ressources'
            ],
            [
                'url' => 'weapons',
                'itemNature' => 'Armes'
            ],
        ];
        foreach ($itemNatures as $itemNature) {
            $itemNatureEntity = new ItemNature();
            $itemNatureEntity->setName($itemNature['itemNature']);
            $this->entityManager->persist($itemNatureEntity);
            $this->seedItemEntitys($itemNature['url'], $itemNatureEntity);
        }
        $this->entityManager->flush();

        $defaultItems = $this->entityManager->getRepository(DefaultItem::class)->findAll();

        foreach ($defaultItems as $defaultItem) {
            for ($i = 0; $i < rand(1, 10); $i++) {
                $item = new Item();
                $item->setDefaultItem($defaultItem);
                $this->entityManager->persist($item);
            }
        }
//        $this->entityManager->flush();
    }

    private function seedItemEntitys(string $suffix, ItemNature $itemNature)
    {
        $itemTypeString = 'type';
        $data = $this->getAllData($suffix);
        $itemTypes = [];
        $unknownItemType = $this->entityManager->getRepository(ItemType::class)->findOneBy(['name' => 'Unknown']);
        foreach ($data as $item) {
            if (array_key_exists($itemTypeString, $item) && $item[$itemTypeString] !== null) {
                if (!isset($itemTypes[$item[$itemTypeString]])) {
                    $itemType = new ItemType();
                    $itemType->setName($item[$itemTypeString]);
                    $itemType->setItemNature($itemNature);
                    $this->entityManager->persist($itemType);
                    $itemTypes[$item[$itemTypeString]] = $itemType;
                } else {
                    $itemType = $itemTypes[$item[$itemTypeString]];
                }
            } else {
                $itemType = $unknownItemType;
            }
            $buyPrice = rand(1, 1000);
            $itemEntity = new DefaultItem();
            $itemEntity->setAnkamaId($item['_id']);
            $itemEntity->setName($item['name'] ?? '');
            $itemEntity->setDescription($item['description'] ?? '');
            $itemEntity->setItemType($itemType);
            $itemEntity->setBuyPrice($buyPrice);
            $itemEntity->setSellPrice((int)$buyPrice * 0.8);
            $itemEntity->setItemNature($itemNature);
            $itemEntity->setLevel($item['level'] ?? 0);
            $this->entityManager->persist($itemEntity);
        }
    }

    private function seedUsers(): void
    {
        $account = new Account();
        $account->setClass($this->entityManager->getRepository(PlayerClass::class)->findAll()[0]);
        $account->setName('Yhaourt');
        $this->entityManager->persist($account);

        $user = new User();
        $user->setUsername('admin');
        $user->setAvatar('default.png');
        $user->setPassword($this->userPasswordHasher->hashPassword($user, 'admin'));
        $user->setMoney(1000000);
        $user->setRoles(['ROLE_ADMIN']);
        $user->addAccount($account);
        $user->setActiveAccount($account);

        foreach ($this->entityManager->getRepository(Profession::class)->findAll() as $profession)
        {
            $playerProfession = new PlayerProfession();
            $playerProfession->setProfession($profession);
            $playerProfession->setLevel(1);
            $playerProfession->setPlayer($account);
            $this->entityManager->persist($playerProfession);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}