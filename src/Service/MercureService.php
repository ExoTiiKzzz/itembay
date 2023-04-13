<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class MercureService
{
    public static function sendNotification(string $topic, array $data, HubInterface $hub): void
    {
        $update = new Update(
            $topic,
            json_encode($data)
        );

        $hub->publish($update);
    }
}