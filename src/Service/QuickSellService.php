<?php

namespace App\Service;

use App\Entity\Account;
use App\Entity\Batch;
use App\Entity\DefaultItem;
use App\Entity\Item;
use App\Entity\Transaction;
use App\Entity\TransactionLine;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use JetBrains\PhpStorm\Pure;

class QuickSellService
{

    /**
     * @throws Exception
     */
    public static function confirmSell(DefaultItem $defaultItem, int $quantity, Account $account, EntityManagerInterface $em): void
    {
        $items = $em->getRepository(Item::class)->findBy(['account' => $account, 'defaultItem' => $defaultItem, 'isForSell' => 0], null, $quantity);

        if (count($items) < $quantity) {
            throw new Exception('Not enough items in inventory');
        }

        $sellPrice = $defaultItem->getSellPrice() * $quantity;

        $account->getUser()->setMoney($account->getUser()->getMoney() + $sellPrice);

        foreach ($items as $item) {
            $item->setAccount(null);
            $em->persist($item);
        }

        $em->persist($account);
        $em->flush();
    }
}