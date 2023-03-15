<?php

namespace App\Controller;

use App\Entity\DefaultItem;
use App\Entity\Review;
use App\Service\ReviewService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReviewController extends AbstractController
{
    public function __construct(
        protected EntityManagerInterface $em,
    )
    {
    }


    #[Route('/item/{uuid}/review/new', name: 'app_item_review_new')]
    public function review(DefaultItem $defaultItem, string $uuid): Response
    {
//        if (DefaultItemService::)
        return $this->render('item/new_review.html.twig', [
            'uuid'  => $uuid,
//            'item'  => $item,

        ]);
    }

    #[Route('/item/{uuid}/review/create', name: 'app_item_review_create')]
    public function create(DefaultItem $defaultItem, RequestStack $requestStack): Response
    {
        ReviewService::createReview($this->em, $requestStack, $defaultItem, $this->getUser());

        return $this->redirectToRoute('app_item', ['uuid' => $defaultItem->getUuid()]);
    }
}
