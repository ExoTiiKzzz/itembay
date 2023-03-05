<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\PlayerClass;
use App\Service\ApiResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/account/list', name: 'app_account_list')]
    public function list(): Response
    {

        $html = $this->render('user/parts/list_account.html.twig');

        return ApiResponse::success([
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

        return ApiResponse::success([
            'html' => $html->getContent(),
        ]);
    }

    #[Route('/account/create', name: 'app_account_create')]
    public function create(RequestStack $request): Response
    {
        $request = $request->getCurrentRequest();
        try {
            if (!$this->getUser()) {
                throw new \Exception('You must be logged in to create an account');
            }

            $data = json_decode($request->getContent(), true);

            $username = $data['username'];
            $class = $data['class'];

            if (empty($username)) {
                throw new \Exception('Username is required');
            }

            if (empty($class)) {
                throw new \Exception('Class is required');
            }

            $class = $this->em->getRepository(PlayerClass::class)->find($class);

            if (!$class) {
                throw new \Exception('Class not found');
            }

            $account = new Account();
            $account->setUser($this->getUser());
            $account->setName($username);
            $account->setClass($class);

            $this->em->persist($account);
            $this->em->flush();

            return ApiResponse::success([
                'message' => 'Account created',
            ]);

        } catch (\Exception $e) {
            return ApiResponse::error([], $e->getMessage());
        }
    }

    #[Route('/account/edit/{id}', name: 'app_account_add')]
    public function edit(int $id): Response
    {
        $classes = $this->em->getRepository(PlayerClass::class)->findBy([], ['name' => 'ASC']);
        $account = $this->em->getRepository(Account::class)->find($id);
        $html = $this->render('account/form.html.twig', [
            'classes' => $classes,
            'account' => $account,
        ]);

        return ApiResponse::success([
            'html' => $html->getContent(),
        ]);
    }

    #[Route('/account/update', name: 'app_account_update')]
    public function update(RequestStack $request): Response
    {
        $request = $request->getCurrentRequest();
        try {
            if (!$this->getUser()) {
                throw new \Exception('You must be logged in to update an account');
            }

            $data = json_decode($request->getContent(), true);

            $username = $data['username'];
            $id = $data['id'];

            if (empty($username)) {
                throw new \Exception('Username is required');
            }

            $account = $this->em->getRepository(Account::class)->find($id);

            if (!$account) {
                throw new \Exception('Account not found');
            }

            $account->setName($username);

            $this->em->persist($account);
            $this->em->flush();

            return ApiResponse::success([
                'message' => 'Account updated',
            ]);

        } catch (\Exception $e) {
            return ApiResponse::error([], $e->getMessage());
        }
    }
}
