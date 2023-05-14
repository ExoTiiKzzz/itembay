<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\LoginAuthenticator;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends BaseController
{
    #[Route('/register', name: 'app_register', methods: ['GET'])]
    public function register(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig');
    }

    #[Route('/register', name: 'app_register_validation', methods: ['POST'])]
    public function registerPost(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, LoginAuthenticator $authenticator): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        //check crsf token
        $this->isCsrfTokenValid('registration', $request->request->get('_csrf_token'));

        $username = $request->request->get('username');
        $password = $request->request->get('password');

        if (empty($username) || empty($password)) {
            $this->addFlash('error', 'Le nom d\'utilisateur et le mot de passe sont obligatoires');
            return $this->redirectToRoute('app_register');
        }

        if ($this->em->getRepository(User::class)->findOneBy(['username' => $username])) {
            $this->addFlash('error', 'Le nom d\'utilisateur existe déjà');
            return $this->redirectToRoute('app_register');
        }



        $user = UserService::createUser($this->em, $userPasswordHasher, $username, $password);
        // do anything else you need here, like send an email

        return $userAuthenticator->authenticateUser(
            $user,
            $authenticator,
            $request
        );
    }
}
