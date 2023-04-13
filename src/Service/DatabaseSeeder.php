<?php

namespace App\Service;

use App\Entity\Account;
use App\Entity\BugReportStatus;
use App\Entity\BugReportType;
use App\Entity\DefaultItem;
use App\Entity\Item;
use App\Entity\ItemNature;
use App\Entity\ItemType;
use App\Entity\PlayerClass;
use App\Entity\PlayerProfession;
use App\Entity\Profession;
use App\Entity\ProfessionExperience;
use App\Entity\Recipe;
use App\Entity\RecipeLine;
use App\Entity\User;
use App\Repository\BugReportStatusRepository;
use App\Repository\BugReportTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class DatabaseSeeder
{
    private array $json = [];

    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $userPasswordHasher,
        private string $baseDir
    )
    {
    }

    public function seed(): void
    {
        $this->seedClasses();
        $this->seedProfessions();
        $this->seedAllItems();
        $this->seedAllProfessionsItems();
        $this->seedXpTable();
        $this->seedUsers();
        $this->seedBugReportRelated();
    }

    private function seedClasses(): void
    {
        //get json file from current directory
        $classesJson = file_get_contents($this->baseDir . '/player_classes.json');
        //decode json to array
        $classes = json_decode($classesJson, true);
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
        $professionsJson = file_get_contents($this->baseDir . '/professions.json');
        $professions = json_decode($professionsJson, true);
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


        $unknownItemType = new ItemType();
        $unknownItemType->setName('Unknown');
        $unknownItemType->setItemNature($unknownItemNature);
        $this->entityManager->persist($unknownItemType);

        $itemNatures = [];
        $itemNatures[$unknownItemNature->getName()] = $unknownItemNature;

        $itemTypeString = 'type';

        $items = json_decode(file_get_contents($this->baseDir . '/items.json'), true);
        foreach ($items as $item) {
            $nature = $item['nature'];
            if (!isset($itemNatures[$nature])) {
                $itemNature = new ItemNature();
                $itemNature->setName($nature);
                $this->entityManager->persist($itemNature);
                $itemNatures[$nature] = $itemNature;
            } else {
                $itemNature = $itemNatures[$nature];
            }

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
            $buyPrice = $this->randomPrice($item['level']);
            $itemEntity = new DefaultItem();
            $itemEntity->setAnkamaId($item['_id']);
            $itemEntity->setName($item['name'] ?? '');
            $itemEntity->setDescription($item['description'] ?? '');
            $itemEntity->setItemType($itemType);
            $itemEntity->setBuyPrice($buyPrice);
            $itemEntity->setSellPrice($buyPrice * 0.8);
            $itemEntity->setItemNature($itemNature);
            $itemEntity->setLevel($item['level'] ?? 0);
            $this->entityManager->persist($itemEntity);
        }
        $this->entityManager->flush();

        $this->seedRecipes();

        $defaultItems = $this->entityManager->getRepository(DefaultItem::class)->findAll();

        /** @var DefaultItem $defaultItem */
        foreach ($defaultItems as $defaultItem) {
            if (in_array($defaultItem->getItemNature()->getName(), ['Consommables', 'Ressources'])) {
//                $toGenerate = $this->randomGeneration($defaultItem->getLevel() ?? 1);
                $toGenerate = rand(1, 1000);
            } else {
//                $toGenerate = $this->randomGeneration($defaultItem->getLevel() ?? 1, 100, 1000);
                $toGenerate = rand(1, 100);
            }

            for ($i = 0; $i < rand(1, 10); $i++) {
                $this->generateItemForMemory($defaultItem);
            }


        }
//        $this->entityManager->flush();
    }

    private function seedRecipes(): void
    {
        $itemProfessionTypes = json_decode(file_get_contents($this->baseDir . '/types.json'), true);
        $itemJson = file_get_contents($this->baseDir . '/items.json');
        $items = json_decode($itemJson, true);
        foreach ($items as $item) {
            if (array_key_exists('recipe', $item) && $item['recipe'] !== null) {
                $recipe = new Recipe();
                foreach ($item['recipe'] as $recipeLineItem) {
                    $recipeLine = new RecipeLine();
                    $recipeObject = $this->entityManager->getRepository(DefaultItem::class)->findOneBy(['ankamaId' => $recipeLineItem['item_id']]);
                    if ($recipeObject === null) {
                        continue;
                    }
                    $recipeLine->setItem($recipeObject);
                    $recipeLine->setQuantity($recipeLineItem['quantity']);
                    $this->entityManager->persist($recipeLine);
                    $recipe->addRecipeLine($recipeLine);
                }
                /** @var DefaultItem $itemEntity */
                $itemEntity = $this->entityManager->getRepository(DefaultItem::class)->findOneBy(['ankamaId' => $item['_id']]);
                if ($itemEntity === null) {
                    continue;
                }
                $itemEntityType = $itemEntity->getItemType();
                foreach ($itemProfessionTypes as $itemType) {
                    if (in_array($itemEntityType->getName(), $itemType['types'])) {
                        $profession = $this->entityManager->getRepository(Profession::class)->findOneBy(['ankamaId' => $itemType['id']]);
                        if ($profession === null) {
                            continue;
                        }
                        $recipe->setProfession($profession);
                    }
                }
                $this->entityManager->persist($recipe);
                $itemEntity->setRecipe($recipe);
                $this->entityManager->persist($itemEntity);
            }
        }
        $this->entityManager->flush();
    }

    private function seedAllProfessionsItems(): void
    {
        $professionsJson = file_get_contents($this->baseDir . '/professions.json');
        $professions = json_decode($professionsJson, true);
        foreach ($professions as $profession) {
            /** @var Profession $profesionEntity */
            $profesionEntity = $this->entityManager->getRepository(Profession::class)->findOneBy(['ankamaId' => $profession['_id']]);
            if ($profesionEntity === null) {
                continue;
            }
            if (array_key_exists('harvests', $profession)) {
                foreach ($profession['harvests'] as $harvest) {
                    $item = $this->entityManager->getRepository(DefaultItem::class)->findOneBy(['ankamaId' => $harvest['_id']]);
                    if ($item === null) {
                        continue;
                    }
                    $profesionEntity->addHarvestItem($item);
                }
            }

            $this->entityManager->persist($profesionEntity);
        }

        $this->entityManager->flush();
    }

    private function seedXpTable(): void
    {
        //load json file in src/DataFixtures/xpTable.json
        $json = file_get_contents($this->baseDir . '/xpTable.json');
        $xpTable = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $json), true);
        foreach ($xpTable as $row) {
            $xpEntity = new ProfessionExperience();
            $xpEntity->setLevel((int) $row['lvl']);
            $xpEntity->setExp((int) $row['xp']);
            $this->entityManager->persist($xpEntity);
        }

        $this->entityManager->flush();
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
            $playerProfession->setExp(0);
            $playerProfession->setPlayer($account);
            $this->entityManager->persist($playerProfession);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    private function randomPrice(int $level, int $min = 1, int $max = 100): int
    {
        $price = rand($min, $max);
        return ($price * ($level - 1)) + rand(1, 100);
    }


    private function randomGeneration(int $level, int $min_count = 10000, int $max_count = 100000): int
    {

        // Calculer la plage de nombres possibles pour le nombre d'items en fonction du niveau.
        $min_level = 1;
        $max_level = 200;
        $min_item_count = 3000;
        $max_item_count = 20000;

        $range_min = $min_count + (($max_count - $min_count) / ($max_level - $min_level)) * ($level - $min_level);
        $range_max = $min_count + (($max_count - $min_count) / ($max_level - $min_level)) * ($level - $min_level + 1);
        return (int) rand($range_min, $range_max);
    }

    private function generateItemForMemory(DefaultItem $defaultItem): void
    {
        $item = new Item();
        $item->setDefaultItem($defaultItem);
        $this->entityManager->persist($item);
    }

    private function seedBugReportRelated()
    {

        $types = BugReportType::TYPES;
        $status = BugReportStatus::STATUSES;

        foreach ($types as $type) {
            $bugReportType = new BugReportType();
            $bugReportType->setName($type);
            $this->entityManager->persist($bugReportType);
        }

        foreach ($status as $stat) {
            $bugReportStatus = new BugReportStatus();
            $bugReportStatus->setName($stat);
            $this->entityManager->persist($bugReportStatus);
        }

        $this->entityManager->flush();
    }
}