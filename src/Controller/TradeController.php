<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Trade;
use App\Service\ApiResponseService;
use App\Service\MercureService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TradeController extends BaseController
{
    #[Route('/trade/room/{id}', name: 'app_trade_room')]
    public function room(int $id): Response
    {
        $trade = $this->em->getRepository(Trade::class)->find($id);

        if (!$trade) {
            return $this->redirectToRoute('app_home');
        }

        return $this->render('trade/room.html.twig', [
            'trade' => $trade
        ]);
    }

    #[Route('/trade/list', name: 'app_trade_list')]
    public function list(): Response
    {
        $html = $this->render('trade/trade_list.html.twig');
        return ApiResponseService::success([
            'html' => $html->getContent(),
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

        $existingTrade = $this->em->getRepository(Trade::class)->findOneBy([
            'firstAccount' => $firstAccount,
            'secondAccount' => $secondAccount
        ]);

        if ($existingTrade) {
            return ApiResponseService::error([
                'message' => 'Similar trade request already exists',
            ]);
        }

        $trade = new Trade();
        $trade->setTopic('topic');
        $trade->setFirstAccount($firstAccount);
        $trade->setSecondAccount($secondAccount);
        $trade->setStatus('pending');

        $this->em->persist($trade);
        $this->em->flush();

        MercureService::sendNotificationToUser([
            'type' => 'trade',
            'message' => "{$firstAccount->getName()} veut échanger avec votre {$secondAccount->getName()}.",
        ], $this->hub, $secondAccount->getUser()->getId());
        return ApiResponseService::success([
            'message' => 'Trade request sent',
        ]);
    }

    #[Route('/trade/accept/{id}', name: 'app_trade_accept')]
    public function acceptTrade(int $id): Response
    {
        $trade = $this->em->getRepository(Trade::class)->find($id);
        $trade->setStatus('accepted');
        $this->em->flush();

        //Redirect users in a room
        MercureService::sendNotificationToUser([
            'type' => 'start-trade',
            'trade' => $trade->getId()
        ], $this->hub, $trade->getFirstAccount()->getUser()->getId());
        MercureService::sendNotificationToUser([
            'type' => 'start-trade',
            'trade' => $trade->getId()
        ], $this->hub, $trade->getSecondAccount()->getUser()->getId());

        return ApiResponseService::success([
            'type' => 'trade-accept',
            'message' => 'Trade accepted'
        ]);
    }

    #[Route('/trade/refuse/{id}', name: 'app_trade_refuse')]
    public function refuseTrade(int $id): Response
    {
        $trade = $this->em->getRepository(Trade::class)->find($id);
        MercureService::sendNotificationToUser([
            'type' => 'trade',
            'message' => "{$trade->getSecondAccount()->getName()} à refusé d'échanger avec votre {$trade->getFirstAccount()->getName()}.",
        ], $this->hub, $trade->getFirstAccount()->getUser()->getId());

        $this->em->remove($trade);
        $this->em->flush();

        return ApiResponseService::success([
            'message' => 'Trade refused',
        ]);
    }

    #[Route('/trade/delete/{id}', name: 'app_trade_delete')]
    public function deleteTrade(int $id): Response
    {
        $trade = $this->em->getRepository(Trade::class)->find($id);
        MercureService::sendNotificationToUser([
            'type' => 'trade',
            'message' => "{$trade->getFirstAccount()->getName()} ne veut plus échanger avec votre {$trade->getSecondAccount()->getName()}.",
        ], $this->hub, $trade->getSecondAccount()->getUser()->getId());

        $this->em->remove($trade);
        $this->em->flush();

        return ApiResponseService::success([
            'message' => 'Trade deleted',
        ]);
    }

    #[Route('/trade/abort/{id}', name: 'app_trade_abort')]
    public function abortTrade(int $id): Response
    {
        $trade = $this->em->getRepository(Trade::class)->find($id);
        MercureService::sendNotificationToUser([
            'type' => 'abort-trade',
            'trade' => $trade->getId()
        ], $this->hub, $trade->getFirstAccount()->getUser()->getId());
        MercureService::sendNotificationToUser([
            'type' => 'abort-trade',
            'trade' => $trade->getId()
        ], $this->hub, $trade->getSecondAccount()->getUser()->getId());

        $this->em->remove($trade);
        $this->em->flush();

        return ApiResponseService::success([
            'message' => 'Trade aborted',
        ]);
    }

    #[Route('/trade/confirm/{id}', name: 'app_trade_confirm')]
    public function confirmTrade(int $id): Response
    {
        $trade = $this->em->getRepository(Trade::class)->find($id);
        $data = json_decode($this->request->getContent(), true);

        $accountId = $data['accountId'];
        $confirmation = $data['confirmation'];

        $sendTo = ($accountId === $trade->getFirstAccount()->getId()) ? $trade->getSecondAccount() : $trade->getFirstAccount();

        MercureService::sendNotificationToUser([
            'type' => 'confirm-trade',
            'confirmation' => $confirmation,
        ], $this->hub, $sendTo->getUser()->getId());

        return ApiResponseService::success([
            'message' => 'Trade progressing',
        ]);
    }

    #[Route('/trade/do/{id}', name: 'app_trade_do')]
    public function doTrade(int $id): Response
    {
        $trade = $this->em->getRepository(Trade::class)->find($id);
        $trade->setStatus('done');
        $this->em->flush();

        return ApiResponseService::success([
            'message' => 'Trade done',
        ]);
    }

//    #[Route('/trade/item/add/form', name: 'app_trade_item_add_form')]
//    public function itemAddForm(): Response
//    {
//        $account = $this->getActiveAccountOrRedirect();
//        $items = AccountService::getInventoryItems($account, $this->em, $this->request);
//        $html = $this->render('trade/add-item.html.twig', [
//            'items' => $items,
//        ]);
//
//        return ApiResponseService::success([
//            'html' => $html->getContent(),
//        ]);
//    }

//    #[Route('/trade/item/add/{id}', name: 'app_trade_item_add')]
//    public function itemAdd(int $id): Response
//    {
//        $account = $this->getActiveAccountOrRedirect();
//        $item = $this->em->getRepository(Item::class)->find($id);
//        $html = $this->render('trade/add-item.html.twig', [
//            'items' => $items,
//        ]);
//
//        return ApiResponseService::success([
//            'html' => $html->getContent(),
//        ]);
//    }
}
