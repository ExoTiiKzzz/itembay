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

class BatchService
{
    const ALLOWED_QUANTITIES = [1, 10, 100];

    /**
     * @throws Exception
     */
    public static function createBatch(DefaultItem $defaultItem, int $quantity, Account $account, EntityManagerInterface $em, int $price): void
    {
        if (!in_array($quantity, self::ALLOWED_QUANTITIES)) {
            throw new Exception('Invalid quantity');
        }

        $items = $em->getRepository(Item::class)->findBy(['account' => $account, 'defaultItem' => $defaultItem, 'isForSell' => 0], null, $quantity);

        if (count($items) < $quantity) {
            throw new Exception('Not enough items in inventory');
        }

        $batch = new Batch();
        $batch->setAccount($account);
        $batch->setDefaultItem($defaultItem);
        $batch->setPrice($price);
        $batch->setQuantity($quantity);

        foreach ($items as $item) {
            $item->setBatch($batch);
            $item->setIsForSell(1);
            $em->persist($item);
        }

        $em->persist($batch);
        $em->flush();
    }

    /**
     * @throws Exception
     */
    public static function buyBatch(Batch $batch, Account $account, EntityManagerInterface $em)
    {
        if ($batch->getAccount() === $account) {
            throw new Exception('You can\'t buy your own item');
        }

        if($account->getUser()->getMoney() < $batch->getPrice()) {
            throw new Exception('Not enough balance');
        }

        if (!self::isBatchValid($batch)) {
            throw new Exception('Batch is not valid');
        }

        $transaction = new Transaction();
        $transaction->setAccount($account);
        $transaction->setSeller($batch->getAccount());

        $em->persist($transaction);

        $items = $em->getRepository(Item::class)->findBy(['batch' => $batch]);

        foreach ($items as $item) {
            $transactionLine = new TransactionLine();
            $transactionLine->setTransaction($transaction);
            $transactionLine->setItem($item);
            $transactionLine->setPrice($batch->getPrice() / $batch->getQuantity());
            $em->persist($transactionLine);

            $item->setBatch(null);
            $item->setIsForSell(0);
            $item->setAccount($account);
            $em->persist($item);
        }

        $account->getUser()->setMoney($account->getUser()->getMoney() - $batch->getPrice());
        $em->persist($account);

        $batch->getAccount()->getUser()->setMoney($batch->getAccount()->getUser()->getMoney() + $batch->getPrice());
        $em->persist($batch->getAccount());

        $em->remove($batch);
        $em->flush();
    }

    #[Pure] public static function isBatchValid(Batch $batch): bool
    {
        $items = $batch->getItems();
        $quantity = $batch->getQuantity();

        if (count($items) !== $quantity) {
            return false;
        }

        return true;
    }

    public static function getBatchs(EntityManagerInterface $em, DefaultItem $item): array
    {

        $batchs = [];
        $one = $em->getRepository(Batch::class)->findBy(
            ['defaultItem' => $item, 'quantity' => 1],
            ['price' => 'ASC'],
            10
        );

        $ten = $em->getRepository(Batch::class)->findBy(
            ['defaultItem' => $item, 'quantity' => 10],
            ['price' => 'ASC'],
            10
        );

        $hundred = $em->getRepository(Batch::class)->findBy(
            ['defaultItem' => $item, 'quantity' => 100],
            ['price' => 'ASC'],
            10
        );

        if (!empty($one)) {
            $batchs['1'] = $one;
        }

        if (!empty($ten)) {
            $batchs['10'] = $ten;
        }

        if (!empty($hundred)) {
            $batchs['100'] = $hundred;
        }

        return $batchs;
    }
}