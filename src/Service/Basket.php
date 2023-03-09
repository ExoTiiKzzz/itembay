<?php

namespace App\Service;

use App\Entity\Item;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\Request;

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

    public static function getTotal(EntityManagerInterface $em, ?Request $request): int
    {
        $request = json_decode($request->getContent(), true);
        $ids = [];
        $total = 0;
        foreach ($request['items'] as $requestItem){
            /** @var \App\Entity\DefaultItem $defaultItem */
            $defaultItem = $em->getRepository(\App\Entity\DefaultItem::class)->find($requestItem['id']);
            if (!$defaultItem) {
                continue;
            } else {
                for ($i = 0; $i < $requestItem['quantity']; $i++) {
                    /** @var Item $item */
                    $item = DefaultItem::getOneItemAvailable($em, $defaultItem->getId(), $ids);
                    if ($item) {
                        $ids[] = $item->getId();
                        $total += $item->getBuyPrice();
                    } else {
                        break;
                    }
                }
            }

        }
        return $total;
    }

    public static function getAllAvailableItems(EntityManagerInterface $em, array $items): array
    {
        $ids = [];
        $defaultItems = [];
        $customItems = [];
        /** @var Item $item */
        foreach ($items as $item) {
            if ($item->isDefaultItem()) {
                $defaultItems[] = $item;
                $ids[] = $item->getId();
            } else {
                $customItems[] = $item;
            }
        }
        $defaultItems = DefaultItem::getAllAvailableItems($em, $ids);
        return array_merge($defaultItems, $customItems);
    }
}