<?php

namespace App\Controller;

use App\Entity\Transaction;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TransactionController extends BaseController
{

    #[Route('/transaction', name: 'app_transaction_list')]
    public function index(): Response
    {
        $account = $this->getActiveAccountOrRedirect();
        $transactions = $this->em->getRepository(Transaction::class)->findBy(['account' => $account]);
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
