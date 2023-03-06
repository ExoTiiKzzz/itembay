<?php

namespace App\Service;

use App\Entity\Item;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

class Basket
{
    #[Pure] #[ArrayShape(['defaultItems' => "array", 'customItems' => "array", 'totalCount' => "int"])] public static function listItems(array $items): array
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

    #[Pure] public static function getDefaultItem(array $items, int $id): ?Item
    {
        /** @var Item $item */
        foreach ($items as $item) {
            if ($item->getDefaultItem()->getId() === $id) {
                return $item;
            }
        }
        return null;
    }
}