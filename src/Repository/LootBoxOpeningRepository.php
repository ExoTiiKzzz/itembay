<?php

namespace App\Repository;

use App\Entity\LootBoxOpening;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LootBoxOpening>
 *
 * @method LootBoxOpening|null find($id, $lockMode = null, $lockVersion = null)
 * @method LootBoxOpening|null findOneBy(array $criteria, array $orderBy = null)
 * @method LootBoxOpening[]    findAll()
 * @method LootBoxOpening[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LootBoxOpeningRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LootBoxOpening::class);
    }

    public function save(LootBoxOpening $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(LootBoxOpening $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return LootBoxOpening[] Returns an array of LootBoxOpening objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?LootBoxOpening
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
