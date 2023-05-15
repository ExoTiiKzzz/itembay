<?php

namespace App\Controller;

use App\Entity\ItemSet;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ItemSetController extends BaseController
{
    #[Route('/sets', name: 'app_item_set')]
    public function index(): Response
    {
        $itemSets = $this->em->getRepository(ItemSet::class)->findAll();
        return $this->render('encyclopedia/item_set/index.html.twig', [
            'controller_name' => 'ItemSetController',
            'itemSets' => $itemSets
        ]);
    }

    #[Route('/sets/{id}', name: 'app_item_set_show')]
    public function show(ItemSet $itemSet): Response
    {
        return $this->render('encyclopedia/item_set/show.html.twig', [
            'controller_name' => 'ItemSetController',
            'itemSet' => $itemSet
        ]);
    }
}
