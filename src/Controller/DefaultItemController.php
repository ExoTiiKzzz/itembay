<?php

namespace App\Controller;

use App\Entity\DefaultItem;
use App\Entity\ItemNature;
use App\Service\DefaultItemService;
use App\Service\ItemNatureService;
use App\Service\ItemTypeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultItemController extends AbstractController
{
    protected EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/', name: 'app_home')]
    public function index(DefaultItemService $defaultItemService, RequestStack $requestStack): Response
    {
        $request = $requestStack->getMainRequest();
        $requestData = $request->query->all();

        $activeItemNatures = $request->query->all('itemNature') ?? [];
        $activeItemTypes = $request->query->all('itemType') ?? [];
        $priceRange = $request->query->all('priceRange') ?? [];
        $minPrice = $priceRange['min'] ?? 0;
        $maxPrice = $priceRange['max'] ?? null;

        $itemNatures = ItemNatureService::getItemNaturesForSelect($this->em);

        $itemTypes = ItemTypeService::getItemTypesForSelect($this->em, $itemNatures);
        return $this->render('item/list.html.twig', [
            'controller_name'   => 'DefaultItemController',
            'items'             => $defaultItemService->getItems(),
            'itemNatures'       => $itemNatures,
            'selectedItemNatures'   => $activeItemNatures,
            'itemTypes'         => $itemTypes,
            'selectedItemTypes' => $activeItemTypes,
            'minPrice'          => $minPrice,
            'maxPrice'          => $maxPrice,
            'requestData'       => $requestData,
            'search'            => $request->query->get('search') ?? '',
        ]);
    }

    #[Route('/item/{uuid}', name: 'app_item')]
    public function item(string $uuid): Response
    {
        $item = $this->em->getRepository(\App\Entity\DefaultItem::class)->findOneBy(['uuid' => $uuid]);
        return $this->render('item/item.html.twig', [
            'item'  => $item,
            'stock' => DefaultItemService::getStock($item, $this->em),
        ]);
    }
    

    #[Route('/item/generate/image', name: 'app_generate_image')]
    public function generateImage(): Response
    {
        $items = $this->em->getRepository(\App\Entity\DefaultItem::class)->findAll();
        foreach ($items as $item) {
            file_put_contents( $item->getId() . '.png', file_get_contents($item->getImageUrl()));
        }
        return new Response('ok');
    }

}
