<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EncyclopediaController extends AbstractController
{
    #[Route('/encyclopedia', name: 'app_encyclopedia')]
    public function index(): Response
    {
        return $this->render('encyclopedia/index.html.twig');
    }
}
