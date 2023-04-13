<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\PlayerClass;
use App\Entity\PlayerProfession;
use App\Entity\Profession;
use App\Service\AccountService;
use App\Service\ApiResponseService;
use App\Twig\AppExtension;
use Exception;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends BaseController
{
    #[Route('/mercuretest', name: 'mercure_test')]
    public function mercure_test(HubInterface $hub): Response
    {
        $update = new Update(
            'http://localhost:8000/mercuretest',
            json_encode(['hello' => 'world'])
        );

        $hub->publish($update);

        return new Response('Published!');
    }

    #[Route('/accounts', name: 'app_accounts')]
    public function accounts(): Response
    {
        return $this->render('account/accounts.html.twig');
    }

    #[Route('/account/list', name: 'app_account_list')]
    public function list(): Response
    {

        $html = $this->render('account/list_account.html.twig');

        return ApiResponseService::success([
            'html' => $html->getContent(),
        ]);
    }

    #[Route('/account/add', name: 'app_account_add')]
    public function add(): Response
    {
        $classes = $this->em->getRepository(PlayerClass::class)->findBy([], ['name' => 'ASC']);
        $html = $this->render('account/form.html.twig', [
            'classes' => $classes,
        ]);

        return ApiResponseService::success([
            'html' => $html->getContent(),
        ]);
    }

    #[Route('/account/create', name: 'app_account_create')]
    public function create(): Response
    {
        try {
            $user = $this->getUserOrThrowException();

            $data = json_decode($this->request->getContent(), true);

            $username = $data['username'];
            $class = $data['class'];

            if (empty($username)) {
                throw new Exception('Username is required');
            }

            if (empty($class)) {
                throw new Exception('Class is required');
            }

            $class = $this->em->getRepository(PlayerClass::class)->find($class);

            if (!$class) {
                throw new Exception('Class not found');
            }

            $account = new Account();
            $account->setUser($user);
            $account->setName($username);
            $account->setClass($class);

            if ($user->getActiveAccount() === null) $user->setActiveAccount($account);


            $this->em->persist($account);

            foreach ($this->em->getRepository(Profession::class)->findAll() as $profession) {
                $job = new PlayerProfession();
                $job->setPlayer($account);
                $job->setProfession($profession);
                $job->setExp(0);
                $this->em->persist($job);
            }
            $this->em->flush();

            return ApiResponseService::success([
                'message' => 'Account created',
            ]);

        } catch (Exception $e) {
            return ApiResponseService::error([], $e->getMessage());
        }
    }

    #[Route('/account/edit/{id}', name: 'app_account_edit')]
    public function edit(int $id): Response
    {
        $classes = $this->em->getRepository(PlayerClass::class)->findBy([], ['name' => 'ASC']);
        $account = $this->em->getRepository(Account::class)->find($id);
        $html = $this->render('account/form.html.twig', [
            'classes' => $classes,
            'account' => $account,
        ]);

        return ApiResponseService::success([
            'html' => $html->getContent(),
        ]);
    }

    #[Route('/account/update', name: 'app_account_update')]
    public function update(RequestStack $request): Response
    {
        $request = $request->getCurrentRequest();
        try {
            if (!$this->getUser()) {
                throw new Exception('You must be logged in to update an account');
            }

            $data = json_decode($request->getContent(), true);

            $username = $data['username'];
            $id = $data['id'];

            if (empty($username)) {
                throw new Exception('Username is required');
            }

            $account = $this->em->getRepository(Account::class)->find($id);

            if (!$account) {
                throw new Exception('Account not found');
            }

            $account->setName($username);

            $this->em->persist($account);
            $this->em->flush();

            return ApiResponseService::success([
                'message' => 'Account updated',
            ]);

        } catch (Exception $e) {
            return ApiResponseService::error([], $e->getMessage());
        }
    }


    #[Route('/inventory', name: 'app_account_inventory')]
    public function inventory(): Response
    {
        $account = $this->getActiveAccountOrRedirect();
        $inventory = AccountService::getInventoryItems($account);
        return $this->render('account/inventory.html.twig', [
            'inventory' => $inventory,
        ]);
    }

    #[Route('/account/delete/{id}', name: 'app_account_delete')]
    public function delete(int $id): Response
    {
        $account = $this->em->getRepository(Account::class)->find($id);
        $html = $this->render('account/delete.html.twig', [
            'account' => $account,
        ]);

        return ApiResponseService::success([
            'html' => $html->getContent(),
        ]);
    }

    #[Route('/account/confirm-delete/{id}', name: 'app_account_confirm_delete')]
    public function confirmDelete(int $id): Response
    {
        try {
            $this->getUserOrThrowException();

            $account = $this->em->getRepository(Account::class)->find($id);

            if (!$account) {
                throw new Exception('Account not found');
            }

            $this->em->remove($account);
            $this->em->flush();

            return ApiResponseService::success([
                'message' => 'Account deleted',
            ]);

        } catch (Exception $e) {
            return ApiResponseService::error([], $e->getMessage());
        }
    }

    #[Route('/account/activate/{id}', name: 'app_account_activate')]
    public function activate(int $id): Response
    {
        try {
            $user = $this->getUserOrThrowException();

            $account = $this->em->getRepository(Account::class)->find($id);
            if (!$account) {
                throw new Exception('Account not found');
            }

            $user->setActiveAccount($account);
            $this->em->persist($user);
            $this->em->flush();

            $html = $this->render('account/list_account.html.twig');
            return ApiResponseService::success([
                'html' => $html->getContent(),
            ], 'Account activated');

        } catch (Exception $e) {
            return ApiResponseService::error([], $e->getMessage());
        }
    }

    #[Route('/balance', name: 'app_user_balance')]
    public function balance(): Response
    {
        try {
            $user = $this->getUserOrThrowException();

            $controller = new AppExtension($this->em);
            $formattedMoney = $controller->formatItemPrice($user->getMoney());


            return ApiResponseService::success([
                'balance' => $formattedMoney,
            ]);

        } catch (Exception $e) {
            return ApiResponseService::error([], $e->getMessage());
        }
    }
}
