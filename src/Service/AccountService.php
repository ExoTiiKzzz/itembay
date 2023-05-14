<?php

namespace App\Service;

use App\Entity\Account;
use App\Entity\DefaultItem;
use App\Entity\Discussion;
use App\Entity\Item;
use App\Entity\ItemCurrentCharacteristic;
use App\Entity\PlayerClass;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mercure\HubInterface;
use function PHPUnit\Framework\throwException;

class AccountService
{

    public static function getInventoryItems(Account $account, EntityManagerInterface $em, Request $request): array
    {
        $itemNatures = $request->query->all('itemNature') ?? [];
        $itemTypes = $request->query->all('itemType') ?? [];
        $priceRange = $request->query->all('priceRange') ?? [];
        $minPrice = $priceRange['min'] ?? 0;
        $maxPrice = $priceRange['max'] ?? null;
        $search = $request->query->get('search') ?? '';

        $orderBy = DefaultItemService::ORDER_BY_ARRAY[$request->query->get('orderBy', '')] ?? '';

        $qb = $em->createQueryBuilder();
        $qb->select('i')
            ->from(Item::class, 'i')
            ->join('i.defaultItem', 'di')
            ->where('i.account = :account')
            ->setParameter('account', $account)
            ->andWhere('i.batch IS NULL')
            ->andWhere('i.defaultItem IS NOT NULL');

        if ($request->query->get('search')) {
            $qb->andWhere('di.name LIKE :search')
                ->setParameter('search', '%' . $request->query->get('search') . '%');
        }

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

        $qb->orderBy($orderBy['column'], $orderBy['dir']);

        $inventory = $qb->getQuery()->getResult();

        $items = [];
        foreach ($inventory as $item) {
            /** @var Item $item */
            if ($item->isDefaultItem()) {
                if ($item->getBatch() !== null) {
                    continue;
                }
                if (count($item->getCharacteristics()) > 0) {
                    $key = $item->getDefaultItem()->getId() . ' ';
                    foreach ($item->getCharacteristics() as $itemCharacteristic) {
                        $characteristic = $itemCharacteristic->getCharacteristic();
                        $key .= $characteristic->getName() . ' ' . $itemCharacteristic->getValue() . ' ';
                    }
                    if (isset($items['defaultItems'][$key])) {
                        $items['defaultItems'][$key]->quantity++;
                        continue;
                    } else {
                        $stdItem = new \stdClass();
                        $stdItem->defaultItem = $item->getDefaultItem();
                        $stdItem->id = $item->getDefaultItem()->getId();
                        $stdItem->name = $item->getDefaultItem()->getName();
                        $stdItem->quantity = 1;
                        $stdItem->imageUrl = $item->getDefaultItem()->getImageUrl();
                        $stdItem->uuid = $item->getDefaultItem()->getUuid();
                        $stdItem->level = $item->getDefaultItem()->getLevel();
                        $characteristics = $item->getCharacteristics()->toArray();
                        usort($characteristics, function ($a, $b) {
                            /** @var ItemCurrentCharacteristic $a */
                            /** @var ItemCurrentCharacteristic $b */
                            return $a->getCharacteristic()->getShowOrder() <=> $b->getCharacteristic()->getShowOrder();
                        });
                        $stdItem->characteristics = $characteristics;
                        $items['defaultItems'][$key] = $stdItem;
                    }
                } else {
                    if (isset($items['defaultItems'][$item->getDefaultItem()->getId()])) {
                        $items['defaultItems'][$item->getDefaultItem()->getId()]->quantity++;
                        continue;
                    }
                    $stdItem = new \stdClass();
                    $stdItem->defaultItem = $item->getDefaultItem();
                    $stdItem->id = $item->getDefaultItem()->getId();
                    $stdItem->name = $item->getDefaultItem()->getName();
                    $stdItem->quantity = 1;
                    $stdItem->imageUrl = $item->getDefaultItem()->getImageUrl();
                    $stdItem->uuid = $item->getDefaultItem()->getUuid();
                    $stdItem->level = $item->getDefaultItem()->getLevel();
                    $items['defaultItems'][$item->getDefaultItem()->getId()] = $stdItem;
                }


            } else {
                $items['customItems'][] = $item;
            }
        }
        return $items;
    }

    public static function getItemQuantityInInventory(Account $account, DefaultItem $defaultItem, EntityManagerInterface $em): int
    {
        $items = $em->getRepository(Item::class)->findBy(['account' => $account, 'defaultItem' => $defaultItem]);
        return count($items);
    }

    public static function createAccount(string $name, PlayerClass $class, User $user, EntityManagerInterface $em): Account
    {
        $account = new Account();
        $account->setName($name);
        $account->setClass($class);
        $account->setUser($user);
        $em->persist($account);
        $em->flush();
        return $account;
    }

    /**
     * @throws Exception
     */
    public static function addFriend(Account $account, Account $friend, EntityManagerInterface $em, HubInterface $hub): void
    {
        if ($account->getFollowings()->contains($friend)) {
            throw new Exception('Vous suivez déjà ce joueur');
        }

        $account->addFriend($friend);
        $em->persist($account);
        $em->flush();

        MercureService::sendNotificationToUser([
            'message' => $account->getName() . ' suit votre compte ' . $friend->getName(),
        ], $hub, $friend->getUser()->getId());
    }

    /**
     * @throws Exception
     */
    public static function createDiscussion(Account $account, array $friends, EntityManagerInterface $em, HubInterface $hub)
    {
        if (count($friends) < 1) {
            throw new Exception('Vous devez sélectionner au moins un ami');
        }

        if (count($friends) === 1) {
            $qb = $em->createQueryBuilder();
            $qb->select('d')
                ->from(Discussion::class, 'd')
                ->join('d.accounts', 'a')
                ->where('a IN (:friends)')
                ->setParameter('friends', $friends);
            $discussion = $qb->getQuery()->getOneOrNullResult();
            if ($discussion) {
                throw new Exception('Vous avez déjà une discussion avec ce joueur');
            }
        }


        $discussion = new Discussion();
        $discussion->addAccount($account);
        foreach ($friends as $friend) {
            $discussion->addAccount($friend);
        }
        $em->persist($discussion);
        $em->flush();

        foreach ($friends as $friend) {
            MercureService::sendNotificationToUser([
                'message' => $account->getName() . ' a créé une discussion avec votre compte ' . $friend->getName(),
            ], $hub, $friend->getUser()->getId());
        }
    }
}