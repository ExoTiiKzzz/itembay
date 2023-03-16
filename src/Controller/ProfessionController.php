<?php

namespace App\Controller;

use App\Entity\PlayerProfession;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfessionController extends AbstractController
{
    public function __construct(
        protected EntityManagerInterface $em
    )
    {
    }

    #[Route('/jobs', name: 'app_player_jobs')]
    public function index(): Response
    {

        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $jobs = $this->em->getRepository(PlayerProfession::class)->findBy(['player' => $this->getUser()->getActiveAccount()], ['profession' => 'ASC']);
        return $this->render('profession/index.html.twig', [
            'jobs' => $jobs
        ]);
    }

    #[Route('/jobs/{id}', name: 'app_player_job')]
    public function show(int $id): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        return $this->render('profession/show.html.twig');
    }
}
