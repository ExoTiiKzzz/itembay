<?php

namespace App\Repository;

use App\Entity\LootBoxLine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LootBoxLine>
 *
 * @method LootBoxLine|null find($id, $lockMode = null, $lockVersion = null)
 * @method LootBoxLine|null findOneBy(array $criteria, array $orderBy = null)
 * @method LootBoxLine[]    findAll()
 * @method LootBoxLine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LootBoxLineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LootBoxLine::class);
    }

    public function save(LootBoxLine $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(LootBoxLine $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return LootBoxLine[] Returns an array of LootBoxLine objects
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

//    public function findOneBySomeField($value): ?LootBoxLine
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
