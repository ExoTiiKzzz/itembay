<?php

namespace App\Controller;

use App\Entity\DefaultItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultItemController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $em): Response
    {
        $items = $this->em->getRepository(DefaultItem::class)->findAll();
        return $this->render('item/list.html.twig', [
            'controller_name' => 'DefaultItemController',
            'items' => $items
        ]);
    }
}
