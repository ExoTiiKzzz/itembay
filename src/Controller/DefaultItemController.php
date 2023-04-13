<?php

namespace App\Controller;

use App\Entity\Batch;
use App\Entity\DefaultItem;
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

        $activeItemNatures = $this->request->query->all('itemNature') ?? [];
        $activeItemTypes = $this->request->query->all('itemType') ?? [];
        $priceRange = $this->request->query->all('priceRange') ?? [];
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
            'search'            => $this->request->query->get('search') ?? '',
        ]);
    }

    #[Route('/item/{uuid}', name: 'app_item')]
    public function item(string $uuid): Response
    {
        $item = $this->em->getRepository(DefaultItem::class)->findOneBy(['uuid' => $uuid]);
        $batchs = [];
        $one = $this->em->getRepository(Batch::class)->findBy(
            ['defaultItem' => $item, 'quantity' => 1],
            ['price' => 'ASC'],
            10
        );

        $ten = $this->em->getRepository(Batch::class)->findBy(
            ['defaultItem' => $item, 'quantity' => 10],
            ['price' => 'ASC'],
            10
        );

        $hundred = $this->em->getRepository(Batch::class)->findBy(
            ['defaultItem' => $item, 'quantity' => 100],
            ['price' => 'ASC'],
            10
        );

        if (!empty($one)) {
            $batchs['1'] = $one;
        }

        if (!empty($ten)) {
            $batchs['10'] = $ten;
        }

        if (!empty($hundred)) {
            $batchs['100'] = $hundred;
        }

        return $this->render('item/item.html.twig', [
            'item'          => $item,
            'stock'         => DefaultItemService::getStock($item, $this->em),
            'isFarmable'    => DefaultItemService::isFarmable($item),
            'batchs'        => $batchs,
        ]);
    }
    

    #[Route('/item/generate/image', name: 'app_generate_image')]
    public function generateImage(): Response
    {
        $items = $this->em->getRepository(DefaultItem::class)->findAll();
        foreach ($items as $item) {
            file_put_contents( $item->getId() . '.png', file_get_contents($item->getImageUrl()));
        }
        return new Response('ok');
    }

}
