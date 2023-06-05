<?php

namespace App\Service;

use App\Entity\Account;
use App\Entity\Batch;
use App\Entity\DefaultItem;
use App\Entity\Item;
use App\Entity\LootBox;
use App\Entity\LootBoxOpening;
use App\Entity\Transaction;
use App\Entity\TransactionLine;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use JetBrains\PhpStorm\Pure;

class LootBoxService
{
    public static function freeRemaining(EntityManagerInterface $em, Account $account, LootBox $lootBox): int
    {
        $maxPerDay = $lootBox->getMaxFreePerDay();
        if ($maxPerDay === 0) {
            return 0;
        }

        $today = new \DateTime();
        $today->setTime(0, 0, 0);
        $today->setTimezone(new \DateTimeZone('UTC'));

        $qb = $em->createQueryBuilder();
        $qb->select('COUNT(lo.id)')
            ->from(LootBoxOpening::class, 'lo')
            ->where('lo.account = :account')
            ->andWhere('lo.lootBox = :lootBox')
            ->andWhere('lo.created_at >= :today')
            ->setParameter('account', $account)
            ->setParameter('lootBox', $lootBox)
            ->setParameter('today', $today);

        $count = $qb->getQuery()->getSingleScalarResult();

        return $maxPerDay - $count;
    }

    public static function open(EntityManagerInterface $em, Account $account, LootBox $lootBox): DefaultItem
    {
        $freeRemaining = self::freeRemaining($em, $account, $lootBox);

        if ($freeRemaining <= 0) {
            if ($account->getUser()->getMoney() < $lootBox->getPrice()) {
                throw new Exception('Pas assez d\'argent');
            }
        }

        $lines = $lootBox->getLootBoxLines()->toArray();
        //randomize the lines order
        shuffle($lines);
        $probs = [];
        $proba = 0;
        foreach ($lines as $line) {
            $probs[] = [
                'line' => $line,
                'min' => $proba,
                'max' => $proba + $line->getProbability(),
            ];
            $proba += $line->getProbability();
        }

        $random = mt_rand(0, $proba);

        $defaultItem = null;
        foreach ($probs as $prob) {
            if ($prob['min'] <= $random && $random <= $prob['max']) {
                $defaultItem = $prob['line']->getDefaultItem();
                break;
            }
        }

        if ($defaultItem === null) {
            throw new Exception('No item found');
        }

        if ($freeRemaining <= 0) {
            $account->getUser()->setMoney($account->getUser()->getMoney() - $lootBox->getPrice());
        }

        $lootBoxOpening = new LootBoxOpening();
        $lootBoxOpening->setAccount($account);
        $lootBoxOpening->setLootBox($lootBox);
        $lootBoxOpening->setCreatedAt(new DateTimeImmutable());

        $em->persist($lootBoxOpening);

        $em->persist($account->getUser());
        $em->flush();
        return $defaultItem;
    }
}