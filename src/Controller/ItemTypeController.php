<?php

namespace App\Controller;

use App\Entity\ItemNature;
use App\Entity\ItemType;
use App\Service\DefaultItemService;
use App\Service\ItemNatureService;
use App\Service\ItemTypeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGenerator;

class ItemTypeController extends BaseController
{
    #[Route('/types', name: 'app_types_list')]
    public function index(): Response
    {
        return $this->render('item_type/index.html.twig', [
            'filters' => DefaultItemService::getItemFilters($this->getRequestData()),
            'itemTypes' => ItemTypeService::getItemTypes($this->request, $this->em),
        ]);
    }

    #[Route('/types/{id}', name: 'app_types_show')]
    public function show(ItemType $itemType): Response
    {
        $url = $this->generateUrl('app_items_list');
        $url .= '?itemType[]=' . $itemType->getId();

        return $this->redirect($url);
    }
}
