<?php

namespace App\Service;

use App\Entity\Account;
use App\Entity\DefaultItem;
use App\Entity\Item;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\Pure;

class AccountService
{

    public static function getInventoryItems(Account $account): array
    {
        $items = [];
        foreach ($account->getInventory() as $item) {
            if ($item->isDefaultItem()) {
                if (isset($items['defaultItems'][$item->getDefaultItem()->getId()]) && $item->getBatch() === null) {
                    $items['defaultItems'][$item->getDefaultItem()->getId()]->quantity++;
                    continue;
                }
                if ($item->getBatch() === null) {
                    $stdItem = new \stdClass();
                    $stdItem->id = $item->getDefaultItem()->getId();
                    $stdItem->name = $item->getDefaultItem()->getName();
                    $stdItem->quantity = 1;
                    $stdItem->imageUrl = $item->getDefaultItem()->getImageUrl();
                    $stdItem->uuid = $item->getDefaultItem()->getUuid();
                    $items['defaultItems'][$item->getDefaultItem()->getId()] = $stdItem;
                }

            } else {
                $items['customItems'][] = $item;
            }
        }
        return $items;
    }

    public static function getItemQuantityInInventory(Account $account, DefaultItem $defaultItem, EntityManagerInterface $em): int
    {
        $items = $em->getRepository(Item::class)->findBy(['account' => $account, 'defaultItem' => $defaultItem]);
        return count($items);
    }
}