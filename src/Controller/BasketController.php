<?php

namespace App\Controller;

use App\Entity\DefaultItem;
use App\Entity\Item;
use App\Entity\User;
use App\Service\ApiResponse;
use App\Service\Basket;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BasketController extends AbstractController
{
    protected EntityManagerInterface $em;
    protected RequestStack $requestStack;

    public function __construct(EntityManagerInterface $em, RequestStack $requestStack)
    {
        $this->em = $em;
        $this->requestStack = $requestStack;
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

    #[Route('/basket/total', name: 'app_basket_total')]
    public function total(): Response
    {
        try {
            if (!$this->getUser()) {
                throw new \Exception('Vous devez être connecté pour accéder à votre panier.');
            }
            /** @var User $user */
            $user = $this->getUser();
            return ApiResponse::success([
                'total' => Basket::getTotal($this->em, $this->requestStack->getMainRequest()),
            ]);
        } catch (\Exception $e) {
            return ApiResponse::error([], $e->getMessage());
        }
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

            /** @var DefaultItem $defaultItem */
            $defaultItem = $this->em->getRepository(DefaultItem::class)->find($id);

            if (!$defaultItem) {
                throw $this->createNotFoundException('The default item does not exist');
            }

            //actual item in basket
            $items = $basket->getItems()->toArray();

            //get ids of items in basket
            $ids = array_map(function ($item) {
                return $item->getId();
            }, $items);

            $requestBody = json_decode($this->requestStack->getMainRequest()->getContent(), true);

            $quantity = isset($requestBody['quantity']) ? (int) $requestBody['quantity'] : 1;

            for ($i = 0; $i < $quantity; $i++) {
                $item = \App\Service\DefaultItem::getOneItemAvailable($this->em, $id, $ids);
                $basket->addItem($item);
                $ids[] = $item->getId();
            }

            $this->em->flush();

            return ApiResponse::success([], 'Item added to basket', 201);
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

        $requestBody = json_decode($this->requestStack->getMainRequest()->getContent(), true);

        $quantity = isset($requestBody['quantity']) ? (int) $requestBody['quantity'] : 1;

        for ($i = 0; $i < $quantity; $i++) {
            $item = Basket::getDefaultItem($basket->getItems()->toArray(), $id);
            $basket->removeItem($item);
        }

        $this->em->flush();

        return ApiResponse::success();
    }

    #[Route('/basket/validate', name: 'app_basket_validate')]
    public function validate(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $items = Basket::listItems($user->getBasket()->getItems()->toArray());
        return $this->render('basket/validate.html.twig', [
            'items' => $items,
        ]);
    }

    #[Route('/basket/confirm', name: 'app_basket_confirm')]
    public function confirm(): Response
    {
        dd(json_decode($this->requestStack->getMainRequest()->getContent(), true));
        return new Response('ok');
    }
}
