<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Trade;
use App\Service\ApiResponseService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TradeController extends BaseController
{
    #[Route('/trade', name: 'app_trade')]
    public function index(): Response
    {
        return $this->render('trade/index.html.twig', [
            'controller_name' => 'TradeController',
        ]);
    }

    #[Route('/trade/ask/form', name: 'app_trade_ask_form')]
    public function tradeAskForm(): Response
    {
        $accounts = $this->em->getRepository(Account::class)
            ->createQueryBuilder('a')
            ->where('a.user != :currentUser')
            ->setParameter('currentUser', $this->getUser())
            ->getQuery()
            ->getResult();

        $html = $this->render('trade/ask.html.twig', [
            'accounts' => $accounts,
        ]);

        return ApiResponseService::success([
            'html' => $html->getContent(),
        ]);
    }

    #[Route('/trade/ask/{accountId}', name: 'app_trade_ask')]
    public function tradeAsk(int $accountId): Response
    {
        $firstAccount = $this->getUser()->getActiveAccount();
        $secondAccount = $this->em->getRepository(Account::class)->find($accountId);

        $trade = new Trade();
        $trade->setTopic('topic');
        $trade->setFirstAccount($firstAccount);
        $trade->setSecondAccount($secondAccount);
        $trade->setStatus('pending');

        $this->em->persist($trade);
        $this->em->flush();

        return ApiResponseService::success([
            'message' => 'Trade request sent',
        ]);
    }

    #[Route('/trade/delete/{id}', name: 'app_trade_delete')]
    public function deleteTrade(int $id): Response
    {
        $trade = $this->em->getRepository(Trade::class)->find($id);
        $this->em->remove($trade);
        $this->em->flush();

        return ApiResponseService::success([
            'message' => 'Trade deleted',
        ]);
    }

    #[Route('/trade/accept/{id}', name: 'app_trade_accept')]
    public function acceptTrade(int $id): Response
    {
        $trade = $this->em->getRepository(Trade::class)->find($id);
        $trade->setStatus('accepted');
        $this->em->flush();

        return ApiResponseService::success([
            'message' => 'Trade accepted',
        ]);
    }

    #[Route('/trade/refuse/{id}', name: 'app_trade_refuse')]
    public function refuseTrade(int $id): Response
    {
        $trade = $this->em->getRepository(Trade::class)->find($id);
        $trade->setStatus('refused');
        $this->em->flush();

        return ApiResponseService::success([
            'message' => 'Trade refused',
        ]);
    }
}
