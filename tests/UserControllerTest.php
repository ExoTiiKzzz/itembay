<?php

namespace App\Tests;

use App\Entity\User;
use App\Service\ConstantService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testShow(): void
    {
        $client = static::createClient();
        $em = self::getContainer()->get('doctrine')->getManager();
        $crawler = $client->request('GET', ConstantService::BASE_URL . '/user/1');

        $user = $em->getRepository(User::class)->find(1);

        $this->assertResponseStatusCodeSame(200);
        $this->assertSelectorTextContains('h1', 'Bienvenue sur la page de ' . $user->getUsername());
    }
}
