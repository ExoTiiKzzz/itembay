<?php

namespace App\Tests;

use App\Entity\Account;
use App\Entity\DefaultItem;
use App\Entity\PlayerClass;
use App\Entity\User;
use App\Service\AccountService;
use App\Service\ConstantService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AccountControllerTest extends WebTestCase
{
    public function testShow(): void
    {
        $client = static::createClient();
        $em = self::getContainer()->get('doctrine')->getManager();
        $userPasswordHasher = self::getContainer()->get('security.user_password_hasher');
        $account = $em->getRepository(Account::class)->find(1);
        $item = $em->getRepository(DefaultItem::class)->findOneBy([]);

        $url = ConstantService::BASE_URL . '/account/' . $account->getId() . '/inventory/give/' . $item->getId();


        $crawler = $client->request('GET', $url);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $userNotAdmin = UserService::createUser($em, $userPasswordHasher, 'test', 'test');
        $class = $em->getRepository(PlayerClass::class)->findOneBy([]);
        $newUserAccount = AccountService::createAccount('test', $class, $userNotAdmin, $em);
        $userNotAdmin->setActiveAccount($newUserAccount);
        $em->persist($userNotAdmin);
        $em->flush();

        //login
        $client->loginUser($userNotAdmin);

        $crawler = $client->request('GET', $url);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $userAdmin = $em->getRepository(User::class)->find(1);
        $client->loginUser($userAdmin);

        $crawler = $client->request('GET', $url);
        //get flash message in session
        $flashBag = $client->getContainer()->get('session')->getFlashBag();
        $this->assertResponseIsSuccessful();

    }
}
