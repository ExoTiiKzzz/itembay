<?php

namespace App\Service;

use App\Entity\Account;
use App\Entity\Item;
use App\Entity\TransactionLine;
use App\Entity\UserBasket;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class TransactionService
{
    public static function createTransaction(EntityManagerInterface $em, array $request, UserBasket $basket): Response
    {
        $items = [];
        $ids = [];
        $requestItems = $request['items'];
        foreach ($requestItems as $requestItem) {
            /** @var \App\Entity\DefaultItem $defaultItem */
            $defaultItem = $em->getRepository(\App\Entity\DefaultItem::class)->find($requestItem['id']);
            $quantity = $requestItem['quantity'] ?? 1;
            for ($i = 0; $i < $quantity; $i++) {
                $item = DefaultItemService::getOneItemAvailable($em, $defaultItem->getId(), $ids);
                if (!$item) {
                    if ($i !== $quantity - 1) {
                        return ApiResponseService::error([], 'Il n\'y a plus assez d\'un de vos choix. (' . $defaultItem->getName() . ')', Response::HTTP_UNAUTHORIZED);
                    }
                    break;
                }
                $ids[] = $item->getId();
                $items[] = $item;
            }
        }

        /** @var Account $account */
        $account = $basket->getUser()->getActiveAccount();
        if (!$account) {
            return ApiResponseService::error([], 'Vous n\'avez pas de compte de jeu.', Response::HTTP_UNAUTHORIZED);
        }

        $totalPrice = array_sum(array_map(function ($item) {
            return $item->getSellPrice();
        }, $items));

        if ($totalPrice > $account->getUser()->getMoney()) {
            return ApiResponseService::error([], 'Vous n\'avez pas assez d\'itemcoins.', Response::HTTP_UNAUTHORIZED);
        }

        //create transaction for available items
        $transaction = new \App\Entity\Transaction();
        $transaction->setAccount($basket->getUser()->getActiveAccount);
        $em->persist($transaction);
        $em->flush();

        foreach ($items as $item) {
            $transactionLine = new TransactionLine();
            $transactionLine->setItem($item);
            $transactionLine->setPrice($item->getSellPrice());
            $transactionLine->setTransaction($transaction);
            $em->persist($transactionLine);

            $account->addItem($item);
            $em->persist($account);
        }

        //update user money
        $account->getUser()->setMoney($account->getUser()->getMoney() - $totalPrice);

        //update user basket
        foreach ($items as $item) {
            $basket->removeItem($item);
        }

        $em->flush();

        return ApiResponseService::success([
            'transaction' => [
                'id' => $transaction->getId(),
            ],
        ], 'Transaction ' . $transaction->getId() . ' created successfully.');

    }
}