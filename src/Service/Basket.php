<?php

namespace App\Service;

use App\Entity\Item;

class Basket
{
    public static function listItems(array $items): array
    {
        $defaultItems = [];
        $customItems = [];
        $defaultItemsTotal = 0;
        $customItemsTotal = 0;
        /** @var Item $item */
        foreach ($items as $item) {
            if ($item->isDefaultItem()) {
                $defaultItemsTotal += $item->getBuyPrice();
                if (isset($defaultItems[$item->getDefaultItem()->getId()])) {
                    $defaultItems[$item->getDefaultItem()->getId()]['quantity']++;
                    continue;
                }
                $defaultItems[$item->getDefaultItem()->getId()] = [
                    'defaultItem'   => $item->getDefaultItem(),
                    'quantity'      => 1,
                    'buyPrice'      => $item->getBuyPrice(),
                    'sellPrice'     => $item->getSellPrice(),
                ];
            } else {
                $customItemsTotal += $item->getBuyPrice();
                $customItems[] = $item;
            }
        }

        return [
            'defaultItems'      => [
                'items' => $defaultItems,
                'total' => $defaultItemsTotal,
            ],
            'customItems'       => [
                'items' => $customItems,
                'total' => $customItemsTotal,
            ],
            'totalCount'        => count($defaultItems) + count($customItems),
        ];
    }
}