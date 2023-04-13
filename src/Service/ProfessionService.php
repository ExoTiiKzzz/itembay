<?php

namespace App\Service;

use App\Entity\Account;
use App\Entity\PlayerProfession;
use App\Entity\Profession;
use App\Entity\ProfessionExperience;
use App\Entity\Recipe;
use App\Repository\ProfessionExperienceRepository;
use Doctrine\ORM\EntityManagerInterface;
use stdClass;
use Symfony\Component\HttpFoundation\RequestStack;

class ProfessionService
{
    const MAX_LEVEL = 200;

    public static function getProfessionLevelFromExp(int $exp, EntityManagerInterface $em): int
    {

        $professionExperience = $em->getRepository(ProfessionExperience::class)->findBy([], ['exp' => 'DESC']);

        /** @var ProfessionExperience $experience */
        foreach ($professionExperience as $experience) {
            if ($exp >= $experience->getExp()) {
                return $experience->getLevel();
            }
        }

        return self::MAX_LEVEL;
    }

    public static function getRecipes(Profession $profession, EntityManagerInterface $em, RequestStack $requestStack, Account $account = null): array
    {
        $playerProfession = $em->getRepository(PlayerProfession::class)->findOneBy([
            'player' => $account,
            'profession' => $profession
        ]);
        $request = $requestStack->getCurrentRequest();
        if ($request->getMethod() === 'POST') {
            $data = json_decode($request->getContent(), true);
            //get the keys starting with filter and remove the filter part of the key
            $tmp = array_filter($data, function ($key) {
                return str_starts_with($key, 'filter');
            }, ARRAY_FILTER_USE_KEY);
            $filters = [];
            foreach ($tmp as $key => $value) {
                //remove the filter part of the key and brackets
                $key = str_replace(['[', ']', 'filter'], '', $key);
                $filters[$key] = $value;
            }
        } else {
            $filters = $request->query->all('filter');
        }
        $qb = $em->createQueryBuilder();
        $qb->select('r')
            ->from(Recipe::class, 'r')
            ->innerJoin('r.item', 'i')
            ->where('r.profession = :profession')
            ->setParameter('profession', $profession);
        if (isset($filters['minLevel'])) {
            $qb->andWhere('i.level >= :minLevel')
                ->setParameter('minLevel', $filters['minLevel']);
        }
        if (isset($filters['maxLevel'])) {
            $qb->andWhere('i.level <= :maxLevel')
                ->setParameter('maxLevel', $filters['maxLevel']);
        } else {
            $qb->andWhere('i.level <= :maxLevel')
                ->setParameter('maxLevel', $playerProfession->getLevel($em));
        }
        $qb->orderBy('i.level', 'ASC');

        $results = $qb->getQuery()->getResult();

        if ($filters && isset($filters['onlyCraftables']) && $filters['onlyCraftables'] === 'on' && $account) {
            $results = array_filter($results, function (Recipe $recipe) use ($account, $em) {
                $isCraftable = RecipeService::isRecipePossible($recipe, $account, $em);
                return $isCraftable['possible'];
            });
        }

        $recipes = [];

        /** @var Recipe $recipe */
        foreach ($results as $recipe) {
            $std = new stdClass();
            $std->id = $recipe->getId();
            $std->item = $recipe->getItem();
            $std->recipeLines = $recipe->getRecipeLines();
            $std->maxCraftable = RecipeService::maxRecipePossible($account, $recipe, $em);
            $recipes[] = $std;
        }

        return $recipes;
    }

    public static function getProfessionActualLevelMinExp(int $exp, EntityManagerInterface $em): ?int
    {
        $actualLevel = self::getProfessionLevelFromExp($exp, $em);

        $actualProfessionExperience = $em->getRepository(ProfessionExperience::class)->findOneBy([
            'level' => $actualLevel
        ]);

        return $actualProfessionExperience?->getExp();
    }

    public static function getProfessionNextLevelMinExp(int $exp, EntityManagerInterface $em): ?int
    {
        $actualLevel = self::getProfessionLevelFromExp($exp, $em);

        $nextProfessionExperience = $em->getRepository(ProfessionExperience::class)->findOneBy([
            'level' => $actualLevel + 1
        ]);

        return $nextProfessionExperience?->getExp();
    }
}