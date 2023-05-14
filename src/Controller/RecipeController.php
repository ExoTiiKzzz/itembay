<?php

namespace App\Controller;

use App\Entity\PlayerProfession;
use App\Entity\Recipe;
use App\Service\ApiResponseService;
use App\Service\ProfessionService;
use App\Service\RecipeService;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecipeController extends BaseController
{
    #[Route('/item/{id}/craft', name: 'app_item_craft')]
    public function craft(Recipe $recipe): Response
    {
        try {
            $data = json_decode($this->request->getContent(), true);
            $account = $this->getActiveAccountOrThrowException();
            $quantity = $data['craftNumber'] ?? 1;
            if(RecipeService::craftItem($recipe->getItem(), $account, $this->em, $this->hub, $this->request, $quantity)) {
                $playerProfession = $this->em->getRepository(PlayerProfession::class)->find($data['playerProfessionId']);
                $recipes = ProfessionService::getRecipes($playerProfession->getProfession(), $this->em, $this->requestStack, $account);
                $data = [
                    'recipesHtml' => $this->renderView('profession/parts/recipes.html.twig', [
                        'recipes' => $recipes,
                    ]),
                    'level' => ProfessionService::getProfessionLevelFromExp($playerProfession->getExp(), $this->em),
                ];
                return ApiResponseService::success($data);
            } else {
                throw new Exception('Impossible de craft cet objet');
            }
        } catch (Exception $e) {
            return ApiResponseService::error([], $e->getMessage());
        }
    }
}
