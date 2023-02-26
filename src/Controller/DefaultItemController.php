<?php

namespace App\Controller;

use App\Entity\ItemNature;
use App\Service\DefaultItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultItemController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/', name: 'app_home')]
    public function index(DefaultItem $defaultItemService, RequestStack $requestStack): Response
    {
        $request = $requestStack->getMainRequest();
        $requestData = $request->query->all();
        $activeItemNatures = $request->query->all('nature');
        $priceRange = $request->query->all('priceRange') ?? [];
        $minPrice = $priceRange['min'] ?? 0;
        $maxPrice = $priceRange['max'] ?? null;
        return $this->render('item/list.html.twig', [
            'controller_name'   => 'DefaultItemController',
            'items'             => $defaultItemService->getItems(),
            'itemNatures'       => $this->em->getRepository(ItemNature::class)->findAll(),
            'selectedNatures'   => $activeItemNatures ? $activeItemNatures : [],
            'minPrice'          => $minPrice,
            'maxPrice'          => $maxPrice,
            'requestData'       => $requestData,
        ]);
    }

    #[Route('/item/generate/image', name: 'app_item')]
    public function generateImage(): Response
    {
        $items = $this->em->getRepository(\App\Entity\DefaultItem::class)->findAll();
        foreach ($items as $item) {
            file_put_contents( $item->getId() . '.png', file_get_contents($item->getImageUrl()));
        }
        return new Response('ok');
    }
}
