<?php

namespace App\Service;

use App\Entity\Account;
use App\Entity\DefaultItem;
use App\Entity\Item;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\Pure;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class DefaultItemService
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

        $itemNature = $request->query->all('itemNature') ?? [];
        $itemType = $request->query->all('itemType') ?? [];
        $priceRange = $request->query->all('priceRange') ?? [];
        $minPrice = $priceRange['min'] ?? 0;
        $maxPrice = $priceRange['max'] ?? null;
        $search = $request->query->get('search') ?? '';

        $orderBy = $this->orderBy[$request->query->get('orderBy', '')] ?? 'i.id';

        $qb = $this->em->createQueryBuilder();
        $qb->select('i')
            ->from(\App\Entity\DefaultItem::class, 'i');

        if ($itemNature) {
            $qb->andWhere('i.itemNature IN (:itemNature)')
                ->setParameter('itemNature', $itemNature);
        }

        if ($itemType) {
            $qb->andWhere('i.itemType IN (:itemType)')
                ->setParameter('itemType', $itemType);
        }

        if ($minPrice) {
            $qb->andWhere('i.buy_price >= :minPrice')
                ->setParameter('minPrice', $minPrice);
        }

        if ($maxPrice) {
            $qb->andWhere('i.buy_price <= :maxPrice')
                ->setParameter('maxPrice', $maxPrice);
        }

        if ($search) {
            $qb->andWhere('i.name LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        $qb->orderBy($orderBy['column'], $orderBy['dir']);

        return $this->paginator->paginate(
            $qb->getQuery(),
            $page,
            $limit
        );
    }

    public static function getDefaultItemsAvailable(EntityManagerInterface $em, $id = null): array
    {
        $qb = $em->createQueryBuilder();
        $qb->select('i')
            ->from(\App\Entity\Item::class, 'i')
            ->innerJoin(\App\Entity\DefaultItem::class, 'di', 'WITH', 'i.defaultItem = di.id');

        if ($id) {
            $qb->andWhere('di.id = :id')
                ->setParameter('id', $id);
        }

        return $qb->getQuery()->getResult();
    }

    public static function getOneItemAvailable(EntityManagerInterface $em, int $id = null, array $notIn = null): ?Item
    {
        $qb = $em->createQueryBuilder();
        $qb->select('i')
            ->from(\App\Entity\Item::class, 'i')
            ->innerJoin(\App\Entity\DefaultItem::class, 'di', 'WITH', 'i.defaultItem = di.id')
            ->andWhere('i.account IS NULL');

        if ($id) {
            $qb->andWhere('di.id = :id')
                ->setParameter('id', $id);
        }

        if ($notIn) {
            $qb->andWhere('i.id NOT IN (:notIn)')
                ->setParameter('notIn', $notIn);
        }

        return $qb->setMaxResults(1)->getQuery()->getOneOrNullResult();
    }

    #[Pure] public static function getAvailableDefaultItem(array $items): ?Item
    {
        /** @var Item $item */
        foreach ($items as $item) {
            if ($item->getAccount() === null) {
                return $item;
            }
        }
        return null;
    }

    public static function getStock(\App\Entity\DefaultItem $defaultItem, EntityManagerInterface $em): int
    {
        return count($em->getRepository(\App\Entity\Item::class)->findBy([
            'defaultItem'   => $defaultItem,
            'isDefaultItem' => true,
            'account'       => null,
        ]));
    }

    public static function canAccountReview(DefaultItem $defaultItem, Account $account, EntityManagerInterface $em): bool
    {
        $item = $em->getRepository(Item::class)->findOneBy([
            'defaultItem'   => $defaultItem,
            'account'       => $account,
        ]);



        return $item !== null;
    }
}