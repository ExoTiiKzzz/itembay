<?php

namespace App\Controller;

use App\Entity\PlayerProfession;
use App\Service\ProfessionService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfessionController extends BaseController
{

    #[Route('/jobs', name: 'app_player_jobs')]
    public function index(): Response
    {
        $account = $this->getActiveAccountOrRedirect();
        $jobs = $this->em->getRepository(PlayerProfession::class)->findBy(['player' => $account], ['profession' => 'ASC']);
        return $this->render('profession/index.html.twig', [
            'jobs' => $jobs
        ]);
    }

    #[Route('/jobs/{id}', name: 'app_player_job')]
    public function show(int $id): Response
    {
        $account = $this->getActiveAccountOrRedirect();
        $playerProfession = $this->em->getRepository(PlayerProfession::class)->findOneBy([
            'player' => $account,
            'profession' => $id
        ]);

        $filters = $this->requestStack->getCurrentRequest()->query->all('filter');
        return $this->render('profession/show.html.twig', [
            'playerProfession' => $playerProfession,
            'recipes' => ProfessionService::getRecipes($playerProfession->getProfession(), $this->em, $this->requestStack, $account),
            'filters' => $filters
        ]);
    }
}
