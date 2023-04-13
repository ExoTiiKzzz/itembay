<?php

namespace App\Service;

use App\Entity\PlayerProfession;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mercure\HubInterface;

class ExpService
{
    public static function addExpToPlayerProfession(PlayerProfession $playerProfession, int $exp, HubInterface $hub, EntityManagerInterface $em): void
    {
        $currentLevel = ProfessionService::getProfessionLevelFromExp($playerProfession->getExp(), $em);
        $playerProfession->setExp($playerProfession->getExp() + $exp);
        $newLevel = ProfessionService::getProfessionLevelFromExp($playerProfession->getExp(), $em);
        if ($currentLevel !== $newLevel) {
            $topic = 'http://localhost:8000/user/' . $playerProfession->getPlayer()->getUser()->getId();
            $data = [
                'message' => 'Votre métier de ' . $playerProfession->getProfession()->getName() . ' est passé au niveau ' . $newLevel . ' !',
            ];
            MercureService::sendNotification($topic, $data, $hub);
        }
    }
}