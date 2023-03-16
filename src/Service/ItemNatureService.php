<?php

namespace App\Service;

use App\Entity\ItemNature;
use Doctrine\ORM\EntityManagerInterface;

class ItemNatureService
{
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