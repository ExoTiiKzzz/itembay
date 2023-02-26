<?php

namespace App\Service;

use App\Entity\DefaultItem;
use App\Entity\Item;
use App\Entity\ItemNature;
use App\Entity\ItemType;
use App\Entity\PlayerClass;
use Doctrine\ORM\EntityManagerInterface;

class DatabaseSeeder
{
    private int $apiMaxLimit = 100;
    private string $baseApiUrl = 'https://eldenring.fanapis.com/api/';
    private EntityManagerInterface $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function seed(): void
    {
        $this->seedClasses();
        $this->seedAllItems();
    }

    private function getAllData(string $url): array
    {
        $data = [];
        $page = 0;
        do {
            $response = json_decode(file_get_contents($url . '?page=' . $page . '&limit=' . $this->apiMaxLimit), true);
            $data = array_merge($data, $response['data']);
            $page++;
        } while (count($response['data']) === $this->apiMaxLimit);
        return $data;
    }

    private function seedClasses(): void
    {
        $classes = $this->getAllData($this->baseApiUrl . 'classes');
        foreach ($classes as $class) {
            $classEntity = new PlayerClass();
            $classEntity->setName($class['name']);
            $classEntity->setDescription($class['description']);
            $classEntity->setImageUrl($class['image']);
            $this->entityManager->persist($classEntity);
        }
        $this->entityManager->flush();
    }

    private function seedAllItems(): void
    {
        $itemType = new ItemType();
        $itemType->setName('Unknown');
        $this->entityManager->persist($itemType);
        $itemNatures = [
            [
                'url' => 'ammos',
                'itemTypeString' => 'type',
                'itemNature' => 'Ammo'
            ],
            [
                'url' => 'armors',
                'itemTypeString' => 'category',
                'itemNature' => 'Armor'
            ],
            [
                'url' => 'ashes',
                'itemTypeString' => 'unknown',
                'itemNature' => 'Ash of War'
            ],
            [
                'url' => 'items',
                'itemTypeString' => 'type',
                'itemNature' => 'Item'
            ],
            [
                'url' => 'shields',
                'itemTypeString' => 'category',
                'itemNature' => 'Shield'
            ],
            [
                'url' => 'talismans',
                'itemTypeString' => 'unknown',
                'itemNature' => 'Talisman'
            ],
            [
                'url' => 'weapons',
                'itemTypeString' => 'category',
                'itemNature' => 'Weapon'
            ]
        ];
        foreach ($itemNatures as $itemNature) {
            $itemNatureEntity = new ItemNature();
            $itemNatureEntity->setName($itemNature['itemNature']);
            $this->entityManager->persist($itemNatureEntity);
            $this->seedItemEntitys($itemNature['url'], $itemNature['itemTypeString'], $itemNatureEntity);
        }
        $this->entityManager->flush();
    }

    private function seedItemEntitys(string $url, string $itemTypeString, ItemNature $itemNature) {
        $data = $this->getAllData($this->baseApiUrl . $url);
        $itemTypes = [];
        $unknownItemType = $this->entityManager->getRepository(ItemType::class)->findOneBy(['name' => 'Unknown']);
        foreach ($data as $item) {
            if (array_key_exists($itemTypeString, $item) && $item[$itemTypeString] !== null) {
                if (!isset($itemTypes[$item[$itemTypeString]])) {
                    $itemType = new ItemType();
                    $itemType->setName($item[$itemTypeString]);
                    $this->entityManager->persist($itemType);
                    $itemTypes[$item[$itemTypeString]] = $itemType;
                }  else {
                    $itemType = $itemTypes[$item[$itemTypeString]];
                }
            } else {
                $itemType = $unknownItemType;
            }
            $buyPrice = rand(1, 1000);
            $itemEntity = new DefaultItem();
            $itemEntity->setName($item['name'] ?? '');
            $itemEntity->setDescription($item['description'] ?? '');
            $itemEntity->setImageUrl($item['image'] ?? '');
            $itemEntity->setItemType($itemType);
            $itemEntity->setBuyPrice($buyPrice);
            $itemEntity->setSellPrice((int) $buyPrice * 0.8);
            $itemEntity->setItemNature($itemNature);
            $this->entityManager->persist($itemEntity);
        }
    }
}