<?php

namespace App\Controller;

use App\Service\DefaultItemService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EncyclopediaController extends BaseController
{
    #[Route('/encyclopedia', name: 'app_encyclopedia')]
    public function index(): Response
    {
        return $this->render('encyclopedia/index.html.twig');
    }
}
