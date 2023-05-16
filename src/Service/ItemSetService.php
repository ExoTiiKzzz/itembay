<?php

namespace App\Service;

use App\Entity\Account;
use App\Entity\DefaultItem;
use App\Entity\Item;
use App\Entity\ItemSet;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class ItemSetService
{
    const ORDER_BY_ARRAY = [
        '' => [
            'column' => 's.ankamaId',
            'dir' => 'ASC',
        ],
        'az' => [
            'column' => 's.name',
            'dir' => 'ASC',
        ],
        'za' => [
            'column' => 's.name',
            'dir' => 'DESC',
        ],
    ];

    public static function getItemSets(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator, int $limit = 40): PaginationInterface
    {
        $qb = $em->createQueryBuilder();
        $qb->select('s')
            ->from(ItemSet::class, 's');

        if($request->query->get('search')) {
            $qb->andWhere('s.name LIKE :name')
                ->setParameter('name', '%' . $request->query->get('search') . '%');
        }

        $orderBy = $request->query->get('orderBy');
        if (array_key_exists($orderBy, self::ORDER_BY_ARRAY)) {
            if($orderBy === 'levelAsc' || $orderBy === 'levelDesc')
                $qb->join('s.items', 'di')
                    ->addSelect('di');
            $qb->orderBy(self::ORDER_BY_ARRAY[$orderBy]['column'], self::ORDER_BY_ARRAY[$orderBy]['dir']);
        }

        return $paginator->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1),
            $limit
        );
    }

    public static function getItemSetItemsInInventory(EntityManagerInterface $em, ItemSet $itemSet, Account $account, ): ArrayCollection
    {
        $items = [];
        $qb = $em->createQueryBuilder();
        $qb->select('di')
            ->from(DefaultItem::class, 'di')
            ->join('di.items', 'i')
            ->join('di.itemSet', 's')
            ->join('i.account', 'a')
            ->where('s.id = :id')
            ->andWhere('a.id = :accountId')
            ->setParameter('id', $itemSet->getId())
            ->setParameter('accountId', $account->getId())
            ->groupBy('di.id');

        $defaultItems = $qb->getQuery()->getResult();
        return new ArrayCollection($defaultItems);
    }

    public static function getItemSetsForSelect(EntityManagerInterface $em)
    {
        $qb = $em->createQueryBuilder();
        $qb->select('s')
            ->from(ItemSet::class, 's')
            ->orderBy('s.name', 'ASC');

        return $qb->getQuery()->getResult();
    }
}