<?php

namespace App\Service;

use App\Entity\ItemNature;
use App\Entity\ItemSet;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class ItemNatureService
{
    const ORDER_BY_ARRAY = [
        '' => [
            'column' => 'n.name',
            'dir' => 'ASC',
        ],
        'az' => [
            'column' => 'n.name',
            'dir' => 'ASC',
        ],
        'za' => [
            'column' => 'n.name',
            'dir' => 'DESC',
        ],
    ];

    public static function getItemNatures(Request $request, EntityManagerInterface $em): ArrayCollection
    {
        $qb = $em->createQueryBuilder();
        $qb->select('n')
            ->from(ItemNature::class, 'n');

        if($request->query->get('search')) {
            $qb->andWhere('n.name LIKE :name')
                ->setParameter('name', '%' . $request->query->get('search') . '%');
        }

        $orderBy = $request->query->get('orderBy');
        if (array_key_exists($orderBy, self::ORDER_BY_ARRAY)) {
            $qb->orderBy(self::ORDER_BY_ARRAY[$orderBy]['column'], self::ORDER_BY_ARRAY[$orderBy]['dir']);
        }

        return new ArrayCollection($qb->getQuery()->getResult());
    }


    public static function getItemNaturesForSelect(EntityManagerInterface $em)
    {
        $qb = $em->createQueryBuilder();
        //get all item natures except "Unknown"
        $qb->select('itemNature')
            ->from(ItemNature::class, 'itemNature')
            ->where('itemNature.name != :unknown')
            ->setParameter('unknown', 'Unknown')
            ->orderBy('itemNature.name', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public static function getSelectedNatures(EntityManagerInterface $em, array $activeItemNatures)
    {
        $qb = $em->createQueryBuilder();
        //get all item natures except "Unknown"
        $qb->select('itemNature')
            ->from(ItemNature::class, 'itemNature')
            ->where('itemNature.name != :unknown')
            ->setParameter('unknown', 'Unknown');
            if (count($activeItemNatures) > 0) {
                $qb->andWhere('itemNature.id IN (:natures)')
                    ->setParameter('natures', $activeItemNatures);
            }
        $qb->orderBy('itemNature.name', 'ASC');

        return $qb->getQuery()->getResult();
    }


}