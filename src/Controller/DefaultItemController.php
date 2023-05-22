<?php

namespace App\Controller;

use App\Entity\Batch;
use App\Entity\DefaultItem;
use App\Service\BatchService;
use App\Service\DefaultItemService;
use App\Service\ItemNatureService;
use App\Service\ItemTypeService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultItemController extends BaseController
{

    #[Route('/', name: 'app_home')]
    public function index(DefaultItemService $defaultItemService): Response
    {
        $requestData = $this->request->query->all();

        $topSelledItems = $defaultItemService::getTopSelledItems($this->em);
        return $this->render('item/list.html.twig', [
            'controller_name'   => 'DefaultItemController',
            'items'             => $defaultItemService->getItems(),
            'topSelledItems'    => $topSelledItems,
            'filters'           => DefaultItemService::getItemFilters($this->getRequestData())
        ]);
    }



    #[Route('/items/{uuid}', name: 'app_item')]
    public function item(string $uuid): Response
    {
        $item = $this->em->getRepository(DefaultItem::class)->findOneBy(['uuid' => $uuid]);

        $user = $this->getUser();
        $account = null;
        if ($user) {
            $account = $user->getActiveAccount() ?? null;
        }

        return $this->render('item/item.html.twig', [
            'item'          => $item,
            'stock'         => DefaultItemService::getStock($item, $this->em),
            'isFarmable'    => DefaultItemService::isFarmable($item, $account, $this->em),
            'batchs'        => BatchService::getBatchs($this->em, $item),
        ]);
    }

    #[Route('/items', name: 'app_items_list')]
    public function itemsList(DefaultItemService $defaultItemService): Response
    {
        $requestData = $this->request->query->all();

        return $this->render('item/list.html.twig', [
            'controller_name'   => 'DefaultItemController',
            'items'             => $defaultItemService->getItems(),
            'filters'           => DefaultItemService::getItemFilters($this->getRequestData()),
            'context'           => 'encyclopediaItemsList'
        ]);
    }
    

    //custom routes for admin to generate items
    #[Route('/item/{uuid}/give/{quantity}', name: 'app_item_give')]
    public function giveItem(DefaultItem $defaultItem, int $quantity): Response
    {
        $isadmin = $this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN');
        if (!$isadmin) {
            return $this->redirectToRoute('app_home');
        }
        $account = $this->getActiveAccountOrRedirect();

        for ($i = 0; $i < $quantity; $i++) {
            DefaultItemService::generateItemForAccount($defaultItem, $this->em, $account, $this->hub, false);
        }

        return $this->redirectToRoute('app_item', ['uuid' => $defaultItem->getUuid()]);
    }

}
