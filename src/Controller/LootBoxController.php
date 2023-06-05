<?php

namespace App\Controller;

use App\Entity\LootBox;
use App\Service\ApiResponseService;
use App\Service\DefaultItemService;
use App\Service\LootBoxService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LootBoxController extends BaseController
{
    #[Route('/lootbox', name: 'app_lootbox')]
    public function index(): Response
    {
        $lootboxes = $this->em->getRepository(LootBox::class)->findAll();
        return $this->render('loot_box/index.html.twig', [
            'lootboxes' => $lootboxes,
        ]);
    }

    #[Route('/lootbox/{id}', name: 'app_lootbox_show', methods: ['GET'])]
    public function show(LootBox $lootbox): Response
    {
        return $this->render('loot_box/show.html.twig', [
            'lootbox' => $lootbox,
            'freeRemaining' => LootBoxService::freeRemaining($this->em, $this->getActiveAccountOrRedirect(), $lootbox),
        ]);
    }

    #[Route('/lootbox/{id}/open', name: 'app_lootbox_open', methods: ['POST'])]
    public function open(LootBox $lootbox): Response
    {
        try {
            $lootedItem = LootBoxService::open($this->em, $this->getActiveAccountOrThrowException(), $lootbox);
            DefaultItemService::generateItemForAccount($lootedItem, $this->em, $this->getActiveAccountOrThrowException(), $this->hub);
            $freeRemaining = LootBoxService::freeRemaining($this->em, $this->getActiveAccountOrThrowException(), $lootbox);
            $this->em->flush();
            return ApiResponseService::success([
                'message' => 'Tu as reçu l\'item suivant : ' . $lootedItem->getName(),
                'lootedItem' => $lootedItem->getId(),
                'freeRemaining' => $freeRemaining,
                'price' => $lootbox->getPrice(),
            ]);
        } catch (\Exception $e) {
            return ApiResponseService::error(['message' => $e->getMessage()], $e->getMessage());
        }
    }
}
