<?php

namespace App\Controller;

use App\Entity\DefaultItem;
use App\Entity\Item;
use App\Entity\User;
use App\Service\ApiResponse;
use App\Service\Basket;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BasketController extends AbstractController
{
    protected EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/basket', name: 'app_basket')]
    public function index(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        /** @var User $user */
        $user = $this->getUser();
        $items = Basket::listItems($user->getBasket()->getItems()->toArray());

        $html = $this->render('basket/basket.html.twig', [
            'items' => $items,
        ]);

        return ApiResponse::success([
            'html' => $html->getContent(),
        ]);
    }

    #[Route('/basket/count', name: 'app_basket_count')]
    public function default(): Response
    {
        if (!$this->getUser()) {
            return ApiResponse::success([
                'basketCount' => 0,
            ]);
        }
        /** @var User $user */
        $user = $this->getUser();
        $basket = $user->getBasket();
        return ApiResponse::success([
            'basketCount' => $basket->getItems()->count(),
        ]);
    }

    #[Route('/basket/default/add/{id}', name: 'app_basket_add_default')]
    public function add(int $id): Response
    {
        try {
            if (!$this->getUser()) {
                return $this->redirectToRoute('app_login');
            }
            /** @var User $user */
            $user = $this->getUser();
            $basket = $user->getBasket();

            $item = new Item();

            /** @var DefaultItem $defaultItem */
            $defaultItem = $this->em->getRepository(DefaultItem::class)->find($id);

            if (!$defaultItem) {
                throw $this->createNotFoundException('The default item does not exist');
            }
            $item
                ->setIsDefaultItem(true)
                ->setDefaultItem($defaultItem)
                ->setBuyPrice($defaultItem->getBuyPrice())
                ->setSellPrice($defaultItem->getSellPrice());

            $this->em->persist($item);

            $basket->addItem($item);

            $this->em->flush();

            return ApiResponse::success([
                'basketCount' => $basket->getItems()->count(),
            ], 'Item added to basket', 201);
        } catch (\Exception $e) {
            return ApiResponse::error([], $e->getMessage());
        }
    }

    #[Route('/basket/custom/add/{id}', name: 'app_basket_add_custom')]
    public function addCustom(int $id): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        /** @var User $user */
        $user = $this->getUser();
        $basket = $user->getBasket();

        $item = $this->em->getRepository(Item::class)->find($id);

        $basket->addItem($item);

        $this->em->flush();

        return ApiResponse::success([
            'basketCount' => $basket->getItems()->count(),
        ], 'Item added to basket', 201);
    }

    #[Route('/basket/default/remove/{id}', name: 'app_basket_remove_default')]
    public function remove(int $id): Response
    {
        if (!$this->getUser()) {
            return ApiResponse::error([], 'You are not logged in', 401);
        }
        /** @var User $user */
        $user = $this->getUser();
        $basket = $user->getBasket();

        $item = Basket::getDefaultItem($basket->getItems()->toArray(), $id);

        $basket->removeItem($item);

        $this->em->flush();

        return ApiResponse::success();
    }
}
