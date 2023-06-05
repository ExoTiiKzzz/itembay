<?php

namespace App\Service;

use App\Entity\Account;
use App\Entity\BugReportStatus;
use App\Entity\BugReportType;
use App\Entity\Characteristic;
use App\Entity\DefaultItem;
use App\Entity\DefaultItemPossibleCharacteristic;
use App\Entity\Item;
use App\Entity\ItemNature;
use App\Entity\ItemSet;
use App\Entity\ItemType;
use App\Entity\LootBox;
use App\Entity\LootBoxLine;
use App\Entity\PlayerClass;
use App\Entity\PlayerProfession;
use App\Entity\Profession;
use App\Entity\ProfessionExperience;
use App\Entity\Recipe;
use App\Entity\RecipeLine;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class DatabaseSeeder
{

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
        $this->seedCharacteristics();
        $this->seedItemNatures();
        $this->seedItemTypes();
        $this->seedAllItems();
        $this->seedItemSets();
        $this->seedAllProfessionsItems();
        $this->seedXpTable();
        $this->seedUsers();
        $this->seedBugReportRelated();
        $this->seedLootBoxes();
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

    private function seedCharacteristics()
    {
        $characteristicsJson = file_get_contents($this->baseDir . '/characteristics.json');
        $characteristics = json_decode($characteristicsJson, true);
        foreach ($characteristics as $characteristic) {
            $professionEntity = new Characteristic();
            $professionEntity->setAnkamaId($characteristic['id']);
            $professionEntity->setName($characteristic['name']);
            $professionEntity->setShowOrder($characteristic['order']);
            $this->entityManager->persist($professionEntity);
        }
        $this->entityManager->flush();
    }

    private function seedItemNatures(): void
    {
        $itemNaturesJson = file_get_contents($this->baseDir . '/itemNatures.json');
        $itemNatures = json_decode($itemNaturesJson, true);
        foreach ($itemNatures as $itemNature) {
            $itemNatureEntity = new ItemNature();
            $itemNatureEntity->setName($itemNature['name']);
            $itemNatureEntity->setAnkamaId($itemNature['id']);
            $this->entityManager->persist($itemNatureEntity);
        }
        $this->entityManager->flush();
    }

    private function seedItemTypes(): void
    {
        $itemTypesJson = file_get_contents($this->baseDir . '/itemTypes.json');
        $itemTypes = json_decode($itemTypesJson, true);
        foreach ($itemTypes as $itemType) {
            $itemNatureEntity = $this->entityManager->getRepository(ItemNature::class)->findOneBy(['ankamaId' => $itemType['nature']]);
            if (!$itemNatureEntity) {
                continue;
            }
            $itemTypeEntity = new ItemType();
            $itemTypeEntity->setName($itemType['name']);
            $itemTypeEntity->setItemNature($itemNatureEntity);
            $itemTypeEntity->setAnkamaId($itemType['id']);
            $this->entityManager->persist($itemTypeEntity);
        }
        $this->entityManager->flush();
    }

    private function seedAllItems(): void
    {
        $unknownItemNature = new ItemNature();
        $unknownItemNature->setName('Unknown');
        $unknownItemNature->setAnkamaId(0);
        $this->entityManager->persist($unknownItemNature);


        $unknownItemType = new ItemType();
        $unknownItemType->setName('Unknown');
        $unknownItemType->setAnkamaId(0);
        $unknownItemType->setItemNature($unknownItemNature);
        $this->entityManager->persist($unknownItemType);

        $this->entityManager->flush();

        $itemNatures = [];
        $itemNatures[$unknownItemNature->getName()] = $unknownItemNature;

        $itemTypes = [];
        $itemTypes[$unknownItemType->getName()] = $unknownItemType;

        $items = json_decode(file_get_contents($this->baseDir . '/items2.json'), true);
        foreach ($items as $item) {
            $nature = $item['nature'];
            if (!isset($itemNatures[$nature])) {
                $itemNature = $this->entityManager->getRepository(ItemNature::class)->findOneBy(['name' => $nature]);
                if (!$itemNature) {
                    $itemNature = $unknownItemNature;
                }
                $itemNatures[$nature] = $itemNature;
            } else {
                $itemNature = $itemNatures[$nature];
            }

            if (!isset($itemTypes[$item['type']])) {
                $itemType = $this->entityManager->getRepository(ItemType::class)->findOneBy(['name' => $item['type']]);
                if (!$itemType) {
                    $itemType = $unknownItemType;
                }
                $itemTypes[$item['type']] = $itemType;
            } else {
                $itemType = $itemTypes[$item['type']];
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
            $itemEntity->setImageUrl($item['imageUrl'] ?? '');
            $this->entityManager->persist($itemEntity);
        }
        $this->entityManager->flush();
        $itemsEffectsJson = json_decode(file_get_contents($this->baseDir . '/itemEffects.json'), true);
        foreach ($itemsEffectsJson as $item) {
            $defaultItem = $this->entityManager->getRepository(DefaultItem::class)->findOneBy(['ankamaId' => $item['id']]);
            if ($defaultItem === null) {
                continue;
            }
            $itemEffects = $item['effects'];

            foreach ($itemEffects as $itemEffect) {
                $effectId = $itemEffect['effectId'];
                if($effectId === -1) {
                   $effectId = 92;
                }
                $characteristic = $this->entityManager->getRepository(Characteristic::class)->findOneBy(['ankamaId' => $effectId]);
                if ($characteristic === null) {
                    continue;
                }
                $itemEffectEntity = new DefaultItemPossibleCharacteristic();
                $itemEffectEntity->setDefaultItem($defaultItem);
                $itemEffectEntity->setCharacteristic($characteristic);
                $itemEffectEntity->setMin($itemEffect['min']);
                $itemEffectEntity->setMax($itemEffect['max']);
                $this->entityManager->persist($itemEffectEntity);
            }
        }
        $this->entityManager->flush();

        $this->seedRecipes();

        $defaultItems = $this->entityManager->getRepository(DefaultItem::class)->findAll();

        /** @var DefaultItem $defaultItem */
        foreach ($defaultItems as $defaultItem) {
            if ($defaultItem->getRecipe() === null) {
                for ($i = 0; $i < rand(1, 10); $i++) {
                    $this->generateItemForMemory($defaultItem);
                }
            }
        }
    }

    private function seedItemSets(): void
    {
        $itemSetsJson = json_decode(file_get_contents($this->baseDir . '/itemSets.json'), true);
        foreach ($itemSetsJson as $itemSet) {
            $itemSetEntity = new ItemSet();
            $itemSetEntity->setAnkamaId($itemSet['id']);
            $itemSetEntity->setName($itemSet['name']);
            foreach ($itemSet['items'] as $id) {
                $defaultItem = $this->entityManager->getRepository(DefaultItem::class)->findOneBy(['ankamaId' => $id]);
                if ($defaultItem === null) {
                    continue;
                }
                $itemSetEntity->addItem($defaultItem);
            }
            $this->entityManager->persist($itemSetEntity);
        }
        $this->entityManager->flush();
    }

    private function seedRecipes(): void
    {
        $itemProfessionTypes = json_decode(file_get_contents($this->baseDir . '/types.json'), true);
        $itemJson = file_get_contents($this->baseDir . '/items2.json');
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
        $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
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

    private function generateItemForMemory(DefaultItem $defaultItem): void
    {
        $item = new Item();
        $item->setDefaultItem($defaultItem);
        $item->setIsForSell(false);
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

    private function seedLootBoxes()
    {
        $lootBoxesArray = json_decode(file_get_contents($this->baseDir . '/lootBoxes.json'), true);

        foreach ($lootBoxesArray as $lootBoxItem) {
            $lootBox = new LootBox();
            $lootBox->setName($lootBoxItem['name']);
            $lootBox->setPrice($lootBoxItem['price']);
            $lootBox->setColor($lootBoxItem['color']);
            $lootBox->setMaxFreePerDay($lootBoxItem['maxFreePerDay']);
            $this->entityManager->persist($lootBox);

            //get 20 random items between price / 2 and price * 2
            $lowestPrice = $lootBox->getPrice() / 2;
            $highestPrice = $lootBox->getPrice() * 2;
            $items = $this->entityManager->getRepository(DefaultItem::class)->findRandomByPriceRange($lowestPrice, $highestPrice, 20);
            foreach ($items as $item) {
                $lootBoxItem = new LootBoxLine();
                $lootBoxItem->setDefaultItem($item);
                $lootBoxItem->setLootBox($lootBox);
                $this->entityManager->persist($lootBoxItem);
            }
        }

        $this->entityManager->flush();
    }
}