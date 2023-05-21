<?php

namespace App\Service;

use App\Entity\ItemNature;
use App\Entity\ItemType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\Request;

class ItemTypeService
{
    const ORDER_BY_ARRAY = [
        '' => [
            'column' => 't.name',
            'dir' => 'ASC',
        ],
        'az' => [
            'column' => 't.name',
            'dir' => 'ASC',
        ],
        'za' => [
            'column' => 't.name',
            'dir' => 'DESC',
        ],
    ];

    #[Pure] public static function getItemTypesForSelect(EntityManagerInterface $em, array $itemNatures)
    {
        $qb = $em->createQueryBuilder();
        //get all item types except "Unknown"
        $qb->select('itemType')
            ->from(ItemType::class, 'itemType')
            ->where('itemType.name != :unknown')
            ->setParameter('unknown', 'Unknown');
        if (count($itemNatures) > 0) {
            $qb->andWhere('itemType.itemNature IN (:natures)')
                ->setParameter('natures', $itemNatures);
        }
        $qb->orderBy('itemType.name', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public static function getItemTypes(Request $request, EntityManagerInterface $em): ArrayCollection
    {
        $qb = $em->createQueryBuilder();
        $qb->select('t')
            ->from(ItemType::class, 't');

        if($request->query->get('search')) {
            $qb->andWhere('t.name LIKE :name')
                ->setParameter('name', '%' . $request->query->get('search') . '%');
        }

        $orderBy = $request->query->get('orderBy');
        if (array_key_exists($orderBy, self::ORDER_BY_ARRAY)) {
            $qb->orderBy(self::ORDER_BY_ARRAY[$orderBy]['column'], self::ORDER_BY_ARRAY[$orderBy]['dir']);
        }

        return new ArrayCollection($qb->getQuery()->getResult());
    }
}