<?php

namespace App\Service;

use App\Entity\Account;
use App\Entity\DefaultItem;
use App\Entity\Item;
use App\Entity\PlayerProfession;
use App\Entity\Profession;
use App\Entity\ProfessionExperience;
use App\Entity\Recipe;
use App\Repository\ProfessionExperienceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;

class RecipeService
{
    public static function isRecipePossible(Recipe $recipe, Account $account, EntityManagerInterface $em, Request $request): array
    {
        $inventory = AccountService::getInventoryItems($account, $em, $request);
        if (count($inventory) === 0) {
            return [
                'possible'      => false,
                'missingItems'  => []
            ];
        }
        $inventory = $inventory['defaultItems'];
        $recipeLines = $recipe->getRecipeLines();
        $recipeItems = [];
        foreach ($recipeLines as $recipeLine) {
            $recipeItems[$recipeLine->getItem()->getId()] = $recipeLine->getQuantity();
        }

        foreach ($inventory as $item) {
            if (isset($recipeItems[$item->id])) {
                $recipeItems[$item->id] -= $item->quantity;
            }
        }

        $missingItems = [];
        foreach ($recipeItems as $id => $quantity) {
            if ($quantity > 0) {
                $missingItems[] = $em->getRepository(DefaultItem::class)->find($id)->getName();
            }
        }

        return [
            'possible' => count($missingItems) === 0,
            'missingItems' => $missingItems
        ];

    }

    /**
     * @throws Exception
     */
    public static function craftItem(DefaultItem $defaultItem, Account $account, EntityManagerInterface $em, HubInterface $hub, Request $request, int $quantity = 1)
    {
        $profession = $defaultItem->getRecipe()->getProfession();
        if ($profession) {
            $playerProfession = $em->getRepository(PlayerProfession::class)->findOneBy([
                'player' => $account,
                'profession' => $profession
            ]);
            if (!$playerProfession) {
                throw new Exception('Vous n\'avez pas cette profession');
            }
            if ($playerProfession->getLevel($em) < $defaultItem->getLevel()) {
                throw new Exception('Vous n\'avez pas le niveau requis pour craft cet objet');
            }
        }
        $recipeLines = $defaultItem->getRecipe()->getRecipeLines();
        $recipeItems = [];
        foreach ($recipeLines as $recipeLine) {
            $recipeItems[] = [
                'item' => $recipeLine->getItem(),
                'quantity' => $recipeLine->getQuantity()
            ];
        }

        for ($i = 0; $i < $quantity; $i++) {
            $isPossible = self::isRecipePossible($defaultItem->getRecipe(), $account, $em, $request);
            if (!$isPossible['possible']) {
                throw new Exception('Il vous manque des items pour craft cet objet ( ' . implode(', ', $isPossible['missingItems']) . ' )');
            }

            $inventory = AccountService::getInventoryItems($account, $em, $request);
            $inventory = $inventory['defaultItems'];

            foreach ($recipeItems as $recipeItem) {
                $items = $em->getRepository(Item::class)->findBy([
                    'account' => $account,
                    'defaultItem' => $recipeItem['item']
                ], null, $recipeItem['quantity']);
                foreach ($items as $item) {
                    $em->remove($item);
                }
            }

            DefaultItemService::generateItemForAccount($defaultItem, $em, $account, $hub);
        }

        return true;
    }

    public static function maxRecipePossible(Account $account, Recipe $recipe, EntityManagerInterface $em, Request $request): int
    {
        $inventory = AccountService::getInventoryItems($account, $em, $request);
        if (count($inventory) === 0) {
            return 0;
        }
        $isPossible = self::isRecipePossible($recipe, $account, $em, $request)['possible'];
        $inventory = $inventory['defaultItems'];
        $recipeLines = $recipe->getRecipeLines();
        $recipeItems = [];
        foreach ($recipeLines as $recipeLine) {
            $recipeItems[$recipeLine->getItem()->getId()] = $recipeLine->getQuantity();
        }

        $inventory = array_filter($inventory, function ($item) use ($recipeItems) {
            return isset($recipeItems[$item->id]);
        });

        if (count($inventory) === 0) {
            return 0;
        }

        $max = [];
        foreach ($inventory as $item) {
            if ($isPossible) {
                $max[$item->id] = floor($item->quantity / $recipeItems[$item->id]);
            } else  {
                $max[$item->id] = 0;
            }
        }

        return min($max);
    }
}