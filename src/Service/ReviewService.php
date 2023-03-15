<?php

namespace App\Service;

use App\Entity\DefaultItem;
use App\Entity\Review;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ReviewService
{
    public static function createReview(EntityManagerInterface $em, RequestStack $requestStack, DefaultItem $defaultItem, User $user): void
    {
        $data = $requestStack->getMainRequest()->request->all();
        $review = new Review();
        $review->setAccount($user->getActiveAccount());
        $review->setComment($data['comment']);
        $review->setNote((int) $data['rating']);
        $review->setItem($defaultItem);

        $em->persist($review);
        $em->flush();
    }
}