<?php

namespace App\Controller;

use App\Entity\DefaultItem;
use App\Entity\Item;
use App\Service\ApiResponseService;
use App\Service\QuickSellService;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuickSellController extends BaseController
{
    #[Route('/sell/{uuid}', name: 'app_quick_sell')]
    public function index(DefaultItem $defaultItem): Response
    {
        $account = $this->getActiveAccountOrRedirect();
        $inventoryQuantity = count($this->em->getRepository(Item::class)->findBy(['defaultItem' => $defaultItem, 'account' => $account, 'batch' => null]));
        return $this->render('quick_sell/index.html.twig', [
            'defaultItem' => $defaultItem,
            'inventoryQuantity' => $inventoryQuantity,
        ]);
    }

    #[Route('/sell/{uuid}/{quantity}', name: 'app_quick_sell_confirm', methods: ['POST'])]
    public function confirm(DefaultItem $defaultItem, int $quantity): Response
    {
        try {
            $account = $this->getActiveAccountOrThrowException();
            QuickSellService::confirmSell($defaultItem, $quantity, $account, $this->em);
            $leftQuantity = count($this->em->getRepository(Item::class)->findBy(['defaultItem' => $defaultItem, 'account' => $account, 'batch' => null]));
            $html = $this->renderView('quick_sell/content.html.twig', [
                'defaultItem' => $defaultItem,
                'inventoryQuantity' => $leftQuantity,
            ]);
            return ApiResponseService::success([
                'html' => $html,
                'quantity' => $leftQuantity,
            ]);
        } catch (Exception $e) {
            return ApiResponseService::error([], $e->getMessage());
        }
    }
}
