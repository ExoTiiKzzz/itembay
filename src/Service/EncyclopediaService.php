<?php

namespace App\Service;

use App\Entity\ItemSet;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class EncyclopediaService
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
}