<?php

namespace App\Repository;

use App\Entity\DefaultItemPossibleCharacteristic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DefaultItemPossibleCharacteristic>
 *
 * @method DefaultItemPossibleCharacteristic|null find($id, $lockMode = null, $lockVersion = null)
 * @method DefaultItemPossibleCharacteristic|null findOneBy(array $criteria, array $orderBy = null)
 * @method DefaultItemPossibleCharacteristic[]    findAll()
 * @method DefaultItemPossibleCharacteristic[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DefaultItemPossibleCharacteristicRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DefaultItemPossibleCharacteristic::class);
    }

    public function save(DefaultItemPossibleCharacteristic $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DefaultItemPossibleCharacteristic $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return DefaultItemCharacteristic[] Returns an array of DefaultItemCharacteristic objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DefaultItemCharacteristic
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
