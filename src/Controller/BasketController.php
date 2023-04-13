<?php

namespace App\Controller;

use App\Entity\DefaultItem;
use App\Entity\Item;
use App\Entity\User;
use App\Service\ApiResponseService;
use App\Service\BasketService;
use App\Service\DefaultItemService;
use App\Service\TransactionService;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BasketController extends BaseController
{

    #[Route('/basket', name: 'app_basket')]
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->getUserOrRedirect();
        $items = BasketService::listItems($user->getBasket()->getItems()->toArray());

        $html = $this->render('basket/basket.html.twig', [
            'items' => $items,
        ]);

        return ApiResponseService::success([
            'html' => $html->getContent(),
        ]);
    }

    #[Route('/basket/count', name: 'app_basket_count')]
    public function default(): Response
    {
        try {
            /** @var User $user */
            $user = $this->getUserOrThrowException();
            $basket = $user->getBasket();
            return ApiResponseService::success([
                'basketCount' => $basket->getItems()->count(),
            ]);
        } catch (Exception $e) {
            return ApiResponseService::error([], $e->getMessage());
        }

    }

    #[Route('/basket/total', name: 'app_basket_total')]
    public function total(): Response
    {
        try {
            $this->getUserOrThrowException();
            return ApiResponseService::success([
                'total' => BasketService::getTotal($this->em, $this->request),
            ]);
        } catch (Exception $e) {
            return ApiResponseService::error([], $e->getMessage());
        }
    }

    #[Route('/basket/default/add/{id}', name: 'app_basket_add_default')]
    public function add(int $id): Response
    {
        try {
            /** @var User $user */
            $user = $this->getUserOrThrowException();
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
                $item = DefaultItemService::getOneItemAvailable($this->em, $id, $ids);
                $basket->addItem($item);
                $ids[] = $item->getId();
            }

            $this->em->flush();

            return ApiResponseService::success([], 'Item added to basket', 201);
        } catch (Exception $e) {
            return ApiResponseService::error([], $e->getMessage());
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

        return ApiResponseService::success([
            'basketCount' => $basket->getItems()->count(),
        ], 'Item added to basket', 201);
    }

    #[Route('/basket/default/remove/{id}', name: 'app_basket_remove_default')]
    public function remove(int $id): Response
    {
        try {
            /** @var User $user */
            $user = $this->getUserOrThrowException();
            $basket = $user->getBasket();

            $requestBody = json_decode($this->request->getContent(), true);

            $quantity = isset($requestBody['quantity']) ? (int) $requestBody['quantity'] : 1;

            for ($i = 0; $i < $quantity; $i++) {
                $item = BasketService::getDefaultItem($basket->getItems()->toArray(), $id);
                $basket->removeItem($item);
            }

            $this->em->flush();

            return ApiResponseService::success();
        } catch (Exception $e) {
            return ApiResponseService::error([], $e->getMessage());
        }

    }

    #[Route('/basket/validate', name: 'app_basket_validate')]
    public function validate(): Response
    {
        /** @var User $user */
        $user = $this->getUserOrRedirect();
        $items = BasketService::listItems($user->getBasket()->getItems()->toArray());
        return $this->render('basket/validate.html.twig', [
            'items' => $items,
        ]);
    }

    #[Route('/basket/confirm', name: 'app_basket_confirm')]
    public function confirm(): Response
    {
        $user = $this->getUserOrRedirect();
        $request = json_decode($this->requestStack->getMainRequest()->getContent(), true);
        return TransactionService::createTransaction($this->em, $request, $user->getBasket());
    }
}
