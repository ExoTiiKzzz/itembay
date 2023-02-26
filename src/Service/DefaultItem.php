<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class DefaultItem
{
    private PaginatorInterface $paginator;
    private EntityManagerInterface $em;
    private RequestStack $requestStack;
    private array $orderBy = [
        '' => [
            'column' => 'i.id',
            'dir' => 'DESC',
        ],
        'cheaper' => [
            'column' => 'i.buy_price',
            'dir' => 'ASC',
        ],
        'expensive' => [
            'column' => 'i.buy_price',
            'dir' => 'DESC',
        ],
        'az' => [
            'column' => 'i.name',
            'dir' => 'ASC',
        ],
        'za' => [
            'column' => 'i.name',
            'dir' => 'DESC',
        ],
    ];
    public function __construct(PaginatorInterface $paginator, EntityManagerInterface $em, RequestStack $requestStack)
    {
        $this->paginator = $paginator;
        $this->em = $em;
        $this->requestStack = $requestStack;
    }

    public function getItems(): PaginationInterface
    {
        $request = $this->requestStack->getMainRequest();
        $limit = $request->query->getInt('limit', 40);
        $page = $request->query->getInt('page', 1);

        $itemNature = $request->query->all('nature');
        $priceRange = $request->query->all('priceRange') ?? [];
        $minPrice = $priceRange['min'] ?? 0;
        $maxPrice = $priceRange['max'] ?? null;

        $orderBy = $this->orderBy[$request->query->get('orderBy', '')] ?? 'i.id';

        $qb = $this->em->createQueryBuilder();
        $qb->select('i')
            ->from(\App\Entity\DefaultItem::class, 'i');

        if ($itemNature) {
            if (is_array($itemNature)) {
                $qb->andWhere('i.itemNature IN (:itemNature)')
                    ->setParameter('itemNature', $itemNature);
            } else {
                $qb->andWhere('i.itemNature = :itemNature')
                    ->setParameter('itemNature', $itemNature);
            }
        }

        if ($minPrice) {
            $qb->andWhere('i.buy_price >= :minPrice')
                ->setParameter('minPrice', $minPrice);
        }

        if ($maxPrice) {
            $qb->andWhere('i.buy_price <= :maxPrice')
                ->setParameter('maxPrice', $maxPrice);
        }

        $qb->orderBy($orderBy['column'], $orderBy['dir']);

        return $this->paginator->paginate(
            $qb->getQuery(),
            $page,
            $limit
        );
    }
}