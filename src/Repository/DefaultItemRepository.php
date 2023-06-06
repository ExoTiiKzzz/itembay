<?php

namespace App\Repository;

use App\Entity\DefaultItem;
use App\Entity\ItemNature;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DefaultItem>
 *
 * @method DefaultItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method DefaultItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method DefaultItem[]    findAll()
 * @method DefaultItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DefaultItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DefaultItem::class);
    }

    public function save(DefaultItem $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DefaultItem $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getAllIds(ItemNature $nature = null): array
    {
        $qb = $this->createQueryBuilder('d');
        $qb->select('d.ankamaId as id');
        if ($nature) {
            $qb->andWhere('d.nature = :nature');
            $qb->setParameter('nature', $nature);
        }
        $qb->orderBy('d.id', 'ASC');
        $result = $qb->getQuery()->getResult();
        $ids = [];
        foreach ($result as $row) {
            $ids[] = $row['id'];
        }
        return $ids;
    }

    public function findRandomByPriceRange(int $min, int $max, int $limit): array
    {
        $qb = $this->createQueryBuilder('d');
        $qb->andWhere('d.buy_price >= :min');
        $qb->andWhere('d.buy_price <= :max');
        $qb->setParameter('min', $min);
        $qb->setParameter('max', $max);
        $qb->setMaxResults($limit);
        return $qb->getQuery()->getResult();
    }

//    /**
//     * @return DefaultItem[] Returns an array of DefaultItem objects
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

//    public function findOneBySomeField($value): ?DefaultItem
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
