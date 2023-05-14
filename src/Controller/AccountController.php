<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\DefaultItem;
use App\Entity\PlayerClass;
use App\Entity\PlayerProfession;
use App\Entity\Profession;
use App\Service\AccountService;
use App\Service\ApiResponseService;
use App\Service\DefaultItemService;
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
        $inventory = AccountService::getInventoryItems($account, $this->em, $this->request);
        return $this->render('account/inventory.html.twig', [
            'inventory' => $inventory,
            'filters'   => DefaultItemService::getItemFilters($this->getRequestData()),
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

    #[Route('/account/{id}/inventory', name: 'app_other_account_inventory')]
    public function otherAccountInventory(Account $account): Response
    {
        $canEdit = false;
        $user = $this->getUser();
        if ($user && $account->getUser() === $user) {
            $canEdit = true;
        }

        $inventory = AccountService::getInventoryItems($account, $this->em, $this->request);
        return $this->render('account/inventory.html.twig', [
            'inventory' => $inventory,
            'filters'   => DefaultItemService::getItemFilters($this->getRequestData()),
            'canEdit'   => $canEdit,
            'account'   => $account,
        ]);
    }

    #[Route('/account/{accountId}/inventory/give/{itemId}', name: 'app_give_item_to_account')]
    public function giveItemToAccount(int $accountId, int $itemId): Response
    {
        $currentUser = $this->getUserOrRedirect();
        if (!$currentUser) {
            return $this->redirectToRoute('app_home');
        }
        //check if user is admin
        if (in_array('ROLE_ADMIN', $currentUser->getRoles())) {
            $account = $this->em->getRepository(Account::class)->find($accountId);
            $item = $this->em->getRepository(DefaultItem::class)->find($itemId);
            if (!$account) {
                $this->addFlash('error', 'Compte introuvable');
                return $this->redirectToRoute('app_home');
            }
            if (!$item) {
                $this->addFlash('error', 'Item introuvable');
                return $this->redirectToRoute('app_home');
            }

            $quantity = $this->getRequestData()['quantity'] ?? 1;
            for ($i = 0; $i < $quantity; $i++) {
                DefaultItemService::generateItemForAccount($item, $this->em, $account, $this->hub, false);
            }

            $this->addFlash('success', 'Item(s) ajouté(s) au compte');
            //get previous page
            $referer = $this->request->headers->get('referer');
            return $this->redirect($referer);
        } else {
            $this->addFlash('error', 'Vous n\'avez pas les droits pour effectuer cette action');
            return $this->redirectToRoute('app_home');
        }
    }

    #[Route('/account/{id}/add', name: 'app_add_friend', methods: ['POST'])]
    public function addFriend(Account $friend): Response
    {
        $account = $this->getActiveAccountOrRedirect();
        $referer = $this->request->headers->get('referer');

        try {
            AccountService::addFriend($account, $friend, $this->em, $this->hub);
        } catch (Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirect($referer);
        }


        $this->addFlash('success', 'Vous êtes maintenant ami avec ce compte');
        return $this->redirect($referer);
    }

    #[Route('/account/{id}/remove', name: 'app_remove_friend', methods: ['POST'])]
    public function removeFriend(Account $friend): Response
    {
        $account = $this->getActiveAccountOrRedirect();
        $referer = $this->request->headers->get('referer');

        if (!$account->getFollowings()->contains($friend)) {
            $this->addFlash('error', 'Vous n\'êtes pas ami avec ce compte');
            return $this->redirect($referer);
        }

        $account->removeFriend($friend);
        $this->em->persist($account);
        $this->em->flush();

        $this->addFlash('success', 'Vous n\'êtes plus ami avec ce compte');
        return $this->redirect($referer);
    }

    #[Route('/account/discussion/create', name: 'app_account_discussion_create', methods: ['GET'])]
    public function createDiscussionForm(): Response
    {
        try {
            $account = $this->getActiveAccountOrThrowException();
            $friends = $account->getFriends();
            if ($friends->isEmpty()) {
                throw new Exception('Vous n\'avez pas d\'amis pour créer une discussion');
            }

            $html = $this->render('account/discussion_create.html.twig', [
                'account' => $account,
            ]);

            return ApiResponseService::success([
                'html' => $html->getContent(),
            ]);

        } catch (Exception $e) {
            return ApiResponseService::error([], $e->getMessage());
        }
    }

    #[Route('/account/discussion/create', name: 'app_account_discussion_create_post', methods: ['POST'])]
    public function createDiscussion(): Response
    {
        try {
            $account = $this->getActiveAccountOrRedirect();
            $friendsIds = $this->getRequestData()['accounts'] ?? [];
            $friends = $this->em->getRepository(Account::class)->findBy(['id' => $friendsIds]);

            AccountService::createDiscussion($account, $friends, $this->em, $this->hub);

            return ApiResponseService::success([], 'Discussion créée');
        } catch (Exception $e) {
            return ApiResponseService::error([], $e->getMessage());
        }
    }

    #[Route('/account/{id}', name: 'app_account_show')]
    public function show(Account $account): Response
    {
        $user = $this->getUserOrRedirect();
        $canEdit = false;
        if ($user && $account->getUser() === $user) {
            $canEdit = true;
        }
        return $this->render('account/show.html.twig', [
            'account'   => $account,
            'canEdit'   => $canEdit,
        ]);
    }
}
