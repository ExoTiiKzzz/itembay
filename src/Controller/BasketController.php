<?php

namespace App\Controller;

use App\Entity\DefaultItem;
use App\Entity\Item;
use App\Entity\User;
use App\Service\ApiResponse;
use App\Service\Basket;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BasketController extends AbstractController
{
    protected EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/basket', name: 'app_basket')]
    public function index(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        /** @var User $user */
        $user = $this->getUser();
        $items = Basket::listItems($user->getBasket()->getItems()->toArray());

        return $this->render('basket/basket.html.twig', [
            'items' => $items,
        ]);
    }

    #[Route('/basket/default/add/{id}', name: 'app_basket_add')]
    public function add(int $id): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        /** @var User $user */
        $user = $this->getUser();
        $basket = $user->getBasket();

        $item = new Item();

        /** @var DefaultItem $defaultItem */
        $defaultItem = $this->em->getRepository(DefaultItem::class)->find($id);

        if (!$defaultItem) {
            throw $this->createNotFoundException('The default item does not exist');
        }
        $item
            ->setIsDefaultItem(true)
            ->setDefaultItem($defaultItem)
            ->setBuyPrice($defaultItem->getBuyPrice())
            ->setSellPrice($defaultItem->getSellPrice());

        $this->em->persist($item);

        $basket->addItem($item);

        $this->em->flush();

        return ApiResponse::success([
            'basketCount' => $basket->getItems()->count(),
        ], 'Item added to basket', 201);
    }
}
