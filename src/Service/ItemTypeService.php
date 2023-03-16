<?php

namespace App\Service;

use App\Entity\ItemNature;
use App\Entity\ItemType;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\Pure;

class ItemTypeService
{
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
}