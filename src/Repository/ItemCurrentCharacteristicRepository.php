<?php

namespace App\Repository;

use App\Entity\ItemCurrentCharacteristic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ItemCurrentCharacteristic>
 *
 * @method ItemCurrentCharacteristic|null find($id, $lockMode = null, $lockVersion = null)
 * @method ItemCurrentCharacteristic|null findOneBy(array $criteria, array $orderBy = null)
 * @method ItemCurrentCharacteristic[]    findAll()
 * @method ItemCurrentCharacteristic[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ItemCurrentCharacteristicRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ItemCurrentCharacteristic::class);
    }

    public function save(ItemCurrentCharacteristic $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ItemCurrentCharacteristic $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return DefaultItemCurrentCharacteristic[] Returns an array of DefaultItemCurrentCharacteristic objects
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

//    public function findOneBySomeField($value): ?DefaultItemCurrentCharacteristic
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
