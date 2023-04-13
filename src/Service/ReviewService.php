<?php

namespace App\Service;

use App\Entity\Account;
use App\Entity\DefaultItem;
use App\Entity\Review;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class ReviewService
{
    public static function createReview(EntityManagerInterface $em, Request $request, DefaultItem $defaultItem, Account $account): void
    {
        $data = $request->request->all();
        $review = new Review();
        $review->setAccount($account);
        $review->setComment($data['comment']);
        $review->setNote((int) $data['rating']);
        $review->setItem($defaultItem);

        $em->persist($review);
        $em->flush();
    }
}