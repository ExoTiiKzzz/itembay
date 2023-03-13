<?php

namespace App\Controller;

use App\Entity\Transaction;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TransactionController extends AbstractController
{
    public function __construct(protected EntityManagerInterface $em)
    {
    }

    #[Route('/transaction', name: 'app_transaction_list')]
    public function index(): Response
    {
        $transactions = $this->em->getRepository(Transaction::class)->findBy(['account' => $this->getUser()->getAccounts()[0]]);
        return $this->render('transaction/index.html.twig', [
            'transactions' => $transactions,
        ]);
    }

    #[Route('/transaction/{id}', name: 'app_transaction_details')]
    public function detail(Transaction $transaction): Response
    {
        return $this->render('transaction/details.html.twig', [
            'transaction' => $transaction,
        ]);
    }
}
