<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class BaseController extends AbstractController
{
    protected Request $request;
    public function __construct(
        protected EntityManagerInterface $em,
        protected RequestStack $requestStack,
        protected HubInterface $hub,
    )
    {
        $this->request = $requestStack->getCurrentRequest();
    }

    public function getUserOrRedirect(): UserInterface|RedirectResponse|User|null
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            $response = $this->redirectToRoute('app_login');
            $response->send();
        }
        return $user;
    }

    /**
     * @throws Exception
     */
    public function getUserOrThrowException(): UserInterface|User
    {
        $user = $this->getUser();
        if (!$user) {
            throw new Exception('User not found');
        }

        if ($user instanceof User) {
            return $user;
        }
        throw new Exception('User not found');
    }

    public function getActiveAccountOrRedirect(): RedirectResponse | Account
    {
        $user = $this->getUserOrRedirect();
        $account = $user->getActiveAccount();
        if (!$account) {
            return $this->redirectToRoute('app_accounts');
        }
        return $account;
    }

    /**
     * @throws Exception
     */
    public function getActiveAccountOrThrowException(): Account
    {
        $user = $this->getUserOrThrowException();
        $account = $user->getActiveAccount();
        if (!$account) {
            throw new Exception('Account not found');
        }
        return $account;
    }

    public function getRequestData(): array
    {
        //get request method
        $method = $this->request->getMethod();

        if($method === 'GET') {
            return $this->request->query->all();
        } elseif ($method === 'POST') {
            return json_decode($this->request->getContent(), true);
        } else {
            return [];
        }
    }
}