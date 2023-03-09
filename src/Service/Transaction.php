<?php

namespace App\Service;

use App\Entity\Item;
use App\Entity\UserBasket;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class Transaction
{
    public static function createTransaction(EntityManagerInterface $em, array $request, UserBasket $basket): Response
    {
        $items = $basket->getItems()->toArray();
        //check if the items are still available
        $availableItems = [];
        $unavailableItems = [];
        $ids = [];

        /** @var Item $item */
        foreach ($items as $basketItem) {
            if ($basketItem->getAccount() !== null) {
                $unavailableItems[] = $basketItem;
            } else {
                $item = DefaultItem::getOneItemAvailable($em, $basketItem->getDefaultItem()->getId(), $ids);
                if ($item) {
                    $ids[] = $item->getId();
                    $availableItems[] = $item;
                } else {
                    $unavailableItems[] = $basketItem;
                }
            }
        }

        $account = $basket->getUser()->getAccounts()[0];
        if (!$account) {
            return ApiResponse::error([], 'No account found for this user', Response::HTTP_UNAUTHORIZED);
        }
        //create transaction for available items
        $transaction = new \App\Entity\Transaction();
        $transaction->setAccount($basket->getUser()->getAccounts()[0]);

    }
}