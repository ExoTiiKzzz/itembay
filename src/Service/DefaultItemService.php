<?php

namespace App\Service;

use App\Entity\Account;
use App\Entity\DefaultItem;
use App\Entity\Item;
use App\Entity\ItemCurrentCharacteristic;
use App\Entity\PlayerProfession;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mercure\HubInterface;

class DefaultItemService
{
    private PaginatorInterface $paginator;
    private EntityManagerInterface $em;
    private RequestStack $requestStack;

    const ORDER_BY_ARRAY = [
        '' => [
            'column' => 'di.ankamaId',
            'dir' => 'ASC',
        ],
        'cheaper' => [
            'column' => 'di.buy_price',
            'dir' => 'ASC',
        ],
        'expensive' => [
            'column' => 'di.buy_price',
            'dir' => 'DESC',
        ],
        'az' => [
            'column' => 'di.name',
            'dir' => 'ASC',
        ],
        'za' => [
            'column' => 'di.name',
            'dir' => 'DESC',
        ],
        'levelAsc' => [
            'column' => 'di.level',
            'dir' => 'ASC',
        ],
        'levelDesc' => [
            'column' => 'di.level',
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

        $itemNatures = $request->query->all('itemNature') ?? [];
        $itemTypes = $request->query->all('itemType') ?? [];
        $priceRange = $request->query->all('priceRange') ?? [];
        $minPrice = $priceRange['min'] ?? 0;
        $maxPrice = $priceRange['max'] ?? null;
        $search = $request->query->get('search') ?? '';

        $orderBy = self::ORDER_BY_ARRAY[$request->query->get('orderBy', '')] ?? '';

        $qb = $this->em->createQueryBuilder();
        $qb->select('di')
            ->from(\App\Entity\DefaultItem::class, 'di');

        if ($itemNatures) {
            $qb->andWhere('di.itemNature IN (:itemNature)')
                ->setParameter('itemNature', $itemNatures);
        }

        if ($itemTypes) {
            $qb->andWhere('di.itemType IN (:itemType)')
                ->setParameter('itemType', $itemTypes);
        }

        if ($minPrice) {
            $qb->andWhere('di.buy_price >= :minPrice')
                ->setParameter('minPrice', $minPrice);
        }

        if ($maxPrice) {
            $qb->andWhere('di.buy_price <= :maxPrice')
                ->setParameter('maxPrice', $maxPrice);
        }

        if ($search) {
            $qb->andWhere('di.name LIKE :search')
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

    public static function generateItemForAccount(DefaultItem $defaultItem, EntityManagerInterface $em, Account $account, HubInterface $hub, bool $upProfession = true): Item
    {
        $item = new Item();
        $item->setAccount($account);
        $item->setIsDefaultItem(true);
        $item->setDefaultItem($defaultItem);
        $item->setBuyPrice($defaultItem->getBuyPrice());
        $item->setSellPrice($defaultItem->getSellPrice());
        $item->setIsForSell(false);

        foreach ($defaultItem->getPossibleCharacteristics() as $possibleCharacteristic) {
            $value = rand($possibleCharacteristic->getMin(), $possibleCharacteristic->getMax());
            $itemCharacteristic = new ItemCurrentCharacteristic();
            $itemCharacteristic->setCharacteristic($possibleCharacteristic->getCharacteristic());
            $itemCharacteristic->setValue($value);
            $em->persist($itemCharacteristic);
            $item->addCharacteristic($itemCharacteristic);
        }

        $em->persist($item);

        if ($upProfession) {
            $profession = $defaultItem->getProfession();
            $expToAdd = $defaultItem->getLevel();
            if (!$profession && $defaultItem->getRecipe() !== null && $defaultItem->getRecipe()->getProfession() !== null) {
                $profession = $defaultItem->getRecipe()->getProfession();
                $expToAdd = $expToAdd * 4;
            }
            if ($profession) {
                /** @var PlayerProfession $playerProf */
                $playerProf = $account->getPlayerProfessions()->filter(function (PlayerProfession $playerProfession) use ($profession) {
                    return $playerProfession->getProfession() === $profession;
                })->first();
                ExpService::addExpToPlayerProfession($playerProf, $expToAdd, $hub, $em);
                $em->persist($playerProf);
            }
        }

        $em->flush();

        return $item;
    }

    #[Pure] public static function isResource(DefaultItem $defaultItem): bool
    {
        return $defaultItem->getItemNature()->getName() === 'Ressources';
    }

    public static function isFarmable(DefaultItem $defaultItem, ?Account $account, EntityManagerInterface $em): bool
    {
        if (!self::isResource($defaultItem)) {
            return false;
        }

        if ($defaultItem->getRecipe() !== null) {
            return false;
        }

        if ($account === null) {
            return false;
        }

        $itemProfession = $defaultItem->getProfession();

        if ($itemProfession) {
            /** @var PlayerProfession $playerProfession */
            $playerProfession = $account->getPlayerProfessions()->filter(function (PlayerProfession $playerProfession) use ($itemProfession) {
                return $playerProfession->getProfession() === $itemProfession;
            })->first();
            if ($playerProfession === null) {
                return false;
            }

            if ($playerProfession->getLevel($em) < $defaultItem->getLevel()) {
                return false;
            }
        }

        return true;

    }

    public static function getTopSelledItems(EntityManagerInterface $em, int $limit = 10): array
    {
        $qb = $em->createQueryBuilder()
            ->select('di as defaultItem, COUNT(tl.id) as totalSales')
            ->from(DefaultItem::class, 'di')
            ->leftJoin('di.items', 'i')
            ->leftJoin('i.transactionLines', 'tl')
            ->groupBy('di.id')
            ->where('tl.transaction IS NOT NULL')
            ->orderBy('totalSales', 'DESC')
            ->setMaxResults(10);

        $result = $qb->getQuery()->getResult();
        $items = [];
        foreach ($result as $item) {
            $items[] = $item['defaultItem'];
        }

        return $items;
    }

    #[ArrayShape(['selectedItemNatures' => "array", 'selectedItemTypes' => "array", 'minPrice' => "int", 'maxPrice' => "int|null", 'search' => "null|string", 'orderBy' => "null|string"])]
    public static function getItemFilters(array $data): array
    {
        /** @var array $activeItemNatures */
        $activeItemNatures = $data['itemNature'] ?? [];
        /** @var array $activeItemTypes */
        $activeItemTypes = $data['itemType'] ?? [];
        /** @var array $priceRange */
        $priceRange = $data['priceRange'] ?? [];
        /** @var int $minPrice */
        $minPrice = $priceRange['min'] ?? 0;
        /** @var int|null $maxPrice */
        $maxPrice = $priceRange['max'] ?? null;
        /** @var string|null $search */
        $search = $data['search'] ?? null;
        /** @var string|null $orderBy */
        $orderBy = $data['orderBy'] ?? null;

        return [
            'selectedItemNatures'       => $activeItemNatures,
            'selectedItemTypes'         => $activeItemTypes,
            'minPrice'                  => $minPrice,
            'maxPrice'                  => $maxPrice,
            'search'                    => $search,
            'orderBy'                   => $orderBy,
        ];
    }
}