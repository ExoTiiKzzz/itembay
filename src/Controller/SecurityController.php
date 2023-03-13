<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    private Request $request;
    public function __construct(RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig',
            [
                'last_username' => $lastUsername,
                'error'         => $error,
                'referer'       => $this->request->headers->get('referer')
            ]
        );
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {

    }

    #[Route(path: '/profile', name: 'app_profile', methods: ['GET'])]
    public function profile(): Response
    {
        return $this->render('security/profile.html.twig');
    }

    /**
     * @throws Exception
     */
    #[Route(path: '/update-profile', name: 'app_update_profile', methods: ['POST'])]
    public function updateProfile(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->find($this->getUser()->getId());
        if (!$user) return $this->redirectToRoute('app_login');

        $user->setUsername($request->request->get('username'));
        $avatar = $request->files->get('avatar');
        if ($avatar) {
            if (!$avatar->isValid() || !in_array($avatar->getMimeType(), ['image/jpeg', 'image/png', 'image/webp'])) {
                throw new Exception('Invalid file type. Only JPG, PNG and GIF images are allowed.');
            }

            $oldAvatar = $user->getAvatar();
            if ($oldAvatar) {
                $oldAvatarPath = $this->getParameter('avatars_directory') . '/' . $oldAvatar;
                if (file_exists($oldAvatarPath) && $oldAvatar != 'default.png') {
                    unlink($oldAvatarPath);
                }
            }

            $file = uniqid() . '.' . $avatar->guessExtension();
            try {
                $avatar->move(
                    $this->getParameter('avatars_directory'),
                    $file
                );
                $user->setAvatar($file);
            } catch (FileException $e) {
                throw new Exception('Error while downloading the file.');
            }
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_profile');
    }
}
