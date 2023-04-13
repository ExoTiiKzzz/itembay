<?php

namespace App\Controller;

use App\Entity\Batch;
use App\Entity\DefaultItem;
use App\Entity\Item;
use App\Service\ApiResponseService;
use App\Service\BatchService;
use App\Service\MercureService;
use App\Twig\AppExtension;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Routing\Annotation\Route;
use function Symfony\Component\Translation\t;

class BatchController extends BaseController
{
    #[Route('/batch/create/{uuid}', name: 'app_sell_item')]
    public function index(DefaultItem $defaultItem): Response
    {
        try {
            $account = $this->getActiveAccountOrThrowException();
            $inventoryQuantity = count($this->em->getRepository(Item::class)->findBy(['defaultItem' => $defaultItem, 'account' => $account, 'batch' => null]));
            return $this->render('batch/index.html.twig', [
                'defaultItem' => $defaultItem,
                'inventoryQuantity' => $inventoryQuantity,
            ]);
        } catch (Exception $e) {
            return $this->redirectToRoute('app_home');
        }
    }

    #[Route('/batch/create/{uuid}/confirm', name: 'app_sell_item_confirm', methods: ['POST'])]
    public function confirm(DefaultItem $defaultItem): Response
    {
        try {
            $request = json_decode($this->request->getContent(), true);
            $account = $this->getActiveAccountOrThrowException();
            $quantity = (int) $request['quantity'];
            $price = (int) $request['price'];
            BatchService::createBatch($defaultItem, $quantity, $account, $this->em, $price);
            $leftQuantity = count($this->em->getRepository(Item::class)->findBy(['defaultItem' => $defaultItem, 'account' => $account, 'batch' => null]));
            $data = [
                'quantity' => $leftQuantity,
            ];
            return ApiResponseService::success($data);
        } catch (Exception $e) {
            return ApiResponseService::error([], $e->getMessage());
        }
    }

    #[Route('/batch/{id}/buy', name: 'app_batch_buy', methods: ['POST'])]
    public function buy(Batch $batch): Response
    {
        try {
            $account = $this->getActiveAccountOrThrowException();
            BatchService::buyBatch($batch, $account, $this->em);
            $topic = 'http://localhost:8000/user/' . $batch->getAccount()->getUser()->getId();
            $controller = new AppExtension($this->em);
            $formattedPrice = $controller->formatItemPrice($batch->getPrice());

            $actualSellerMoney = $controller->formatItemPrice($batch->getAccount()->getUser()->getMoney());
            $data = [
                'type' => 'batch',
                'message' => 'Vous avez vendu ' . $batch->getQuantity() . ' ' . $batch->getDefaultItem()->getName() . ' pour ' . $formattedPrice,
                'actualMoney' => $actualSellerMoney,
            ];
            MercureService::sendNotification($topic, $data, $this->hub);
            return ApiResponseService::success();
        } catch (Exception $e) {
            return ApiResponseService::error([], $e->getMessage());
        }
    }

    #[Route('/batch/list', name: 'app_batch_list', methods: ['GET'])]
     public function list(): Response
     {
         $account = $this->getActiveAccountOrRedirect();
         $batches = $this->em->getRepository(Batch::class)->findBy(['account' => $account]);
         return $this->render('batch/list.html.twig', [
             'batches' => $batches,
         ]);
     }

    #[Route('/batch/{id}/delete', name: 'app_batch_delete', methods: ['DELETE'])]
    public function delete(Batch $batch): Response
    {
        try {
            $account = $this->getActiveAccountOrThrowException();
            if ($batch->getAccount() !== $account) {
                throw new Exception('Vous ne pouvez pas supprimer ce lot');
            }
            foreach ($batch->getItems() as $item) {
                $item->setBatch(null);
                $item->setIsForSell(false);
                $this->em->persist($item);
            }
            $this->em->remove($batch);
            $this->em->flush();

            $batches = $this->em->getRepository(Batch::class)->findBy(['account' => $account]);
            $html = $this->renderView('batch/table.html.twig', [
                'batches' => $batches,
            ]);

            return ApiResponseService::success([
                'html' => $html,
            ]);
        } catch (Exception $e) {
            return ApiResponseService::error([], $e->getMessage());
        }
    }
}
