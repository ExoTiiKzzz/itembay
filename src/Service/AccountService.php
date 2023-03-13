<?php

namespace App\Service;

use App\Entity\Account;

class AccountService
{
    public static function getInvetoryItems(Account $account) {
        $items = [];
        foreach ($account->getInventory() as $item) {
            if ($item->isDefaultItem()) {
                if (isset($items['defaultItems'][$item->getDefaultItem()->getId()])) {
                    $items['defaultItems'][$item->getDefaultItem()->getId()]->quantity++;
                    continue;
                }
                $stdItem = new \stdClass();
                $stdItem->id = $item->getDefaultItem()->getId();
                $stdItem->name = $item->getDefaultItem()->getName();
                $stdItem->quantity = 1;
                $stdItem->imageUrl = $item->getDefaultItem()->getImageUrl();
                $stdItem->uuid = $item->getDefaultItem()->getUuid();
                $items['defaultItems'][$item->getDefaultItem()->getId()] = $stdItem;
            } else {
                $items['customItems'][] = $item;
            }
        }
        return $items;
    }
}