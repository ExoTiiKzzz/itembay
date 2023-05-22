<?php

namespace App\Controller;

use App\Entity\DefaultItem;
use App\Entity\Review;
use App\Service\ReviewService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReviewController extends BaseController
{
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
    public function create(DefaultItem $defaultItem): Response
    {
        $account = $this->getActiveAccountOrRedirect();
        ReviewService::createReview($this->em, $this->request, $defaultItem, $account);

        return $this->redirectToRoute('app_item', ['uuid' => $defaultItem->getUuid()]);
    }

    #[Route('/reviews/{id}', name: 'app_review_show')]
    public function show(Review $review): Response
    {
        return $this->render('review/show.html.twig', ['review' => $review]);
    }
}
