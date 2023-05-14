<?php

namespace App\Controller;

use App\Entity\DefaultItem;
use App\Service\AccountService;
use App\Service\ApiResponseService;
use App\Service\DefaultItemService;
use Doctrine\ORM\EntityManager;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FarmController extends BaseController
{
    #[Route('/item/{uuid}/farm', name: 'app_item_farm')]
    public function index(DefaultItem $defaultItem): Response
    {
        $account = $this->getActiveAccountOrRedirect();
//        if (!DefaultItemService::isFarmable($defaultItem, $account, $this->em)) {
//            return $this->redirectToRoute('app_home');
//        }
        return $this->render('farm/index.html.twig', [
            'defaultItem' => $defaultItem,
            'quantity' => AccountService::getItemQuantityInInventory($account, $defaultItem, $this->em),
        ]);
    }

    #[Route('/item/{uuid}/farm/generate', name: 'app_item_farm_generate')]
    public function generate(DefaultItem $defaultItem): Response
    {
        if (!DefaultItemService::isResource($defaultItem)) {
            return ApiResponseService::error([], 'Item is not a resource', 401);
        }
        try {
            $account = $this->getActiveAccountOrThrowException();
            DefaultItemService::generateItemForAccount($defaultItem, $this->em, $account, $this->hub);
            $quantity = AccountService::getItemQuantityInInventory($account, $defaultItem, $this->em);
            return ApiResponseService::success([
                'quantity' => $quantity,
            ], 'Item generated successfully');
        } catch (Exception $e) {
            return ApiResponseService::error([], $e->getMessage());
        }
    }
}
