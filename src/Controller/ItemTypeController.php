<?php

namespace App\Controller;

use App\Entity\ItemNature;
use App\Service\DefaultItemService;
use App\Service\ItemNatureService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGenerator;

class ItemTypeController extends BaseController
{
    #[Route('/types', name: 'app_types_list')]
    public function index(): Response
    {
        $itemNatures = $this->em->getRepository(ItemNature::class)->findAll();
        return $this->render('item_nature/index.html.twig', [
            'filters' => DefaultItemService::getItemFilters($this->getRequestData()),
            'itemNatures' => ItemNatureService::getItemNatures($this->request, $this->em),
        ]);
    }

    #[Route('/natures/{id}', name: 'app_natures_show')]
    public function show(ItemNature $itemNature): Response
    {
        $url = $this->generateUrl('app_items_list');
        $url .= '?itemNature[]=' . $itemNature->getId();

        return $this->redirect($url);
    }
}
