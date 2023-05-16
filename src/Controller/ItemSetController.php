<?php

namespace App\Controller;

use App\Entity\ItemSet;
use App\Service\DefaultItemService;
use App\Service\EncyclopediaService;
use App\Service\ItemSetService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ItemSetController extends BaseController
{
    #[Route('/sets', name: 'app_item_set_list')]
    public function index(PaginatorInterface $paginator): Response
    {
        return $this->render('encyclopedia/item_set/index.html.twig', [
            'filters' => DefaultItemService::getItemFilters($this->getRequestData()),
            'itemSets' => ItemSetService::getItemSets($this->request, $this->em, $paginator)
        ]);
    }

    #[Route('/sets/{id}', name: 'app_item_set_show')]
    public function show(ItemSet $itemSet): Response
    {
        $account = $this->getUser()?->getActiveAccount();
        if ($account) {
            $itemsInInventory = ItemSetService::getItemSetItemsInInventory($this->em, $itemSet, $account);
        } else {
            $itemsInInventory = [];
        }
        return $this->render('encyclopedia/item_set/show.html.twig', [
            'itemSet' => $itemSet,
            'itemsInInventory' => $itemsInInventory,
        ]);
    }
}
