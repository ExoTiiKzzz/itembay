<?php
ini_set('memory_limit', '4096M');
const ITEM_API_URL = 'https://api.dofusdb.fr/items';
const RECIPE_API_URL = 'https://api.dofusdb.fr/recipes';
const API_BASE_URL = 'https://api.dofusdb.fr/';



//get recipes 50 by 50
$skip = 0;
$limit = 50;

$natures = [];
do {
    echo 'Natures :' . $skip . PHP_EOL;
    $url = API_BASE_URL . 'item-super-types?$skip=' . $skip . '&$limit=' . $limit;
    $response = file_get_contents($url);
    $response = json_decode($response, true);
    foreach ($response['data'] as $nature) {
        $natureStd = new stdClass();
        $natureStd->id = $nature['id'];
        $natureStd->name = $nature['name']['fr'] ?? 'Inconnu';
        $natures[] = $natureStd;
    }

    $skip += $limit;
} while (count($response['data']) > 0);

file_put_contents('src/DataFixtures/itemNatures.json', json_encode($natures, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));

$types = [];
$skip = 0;
do {
    echo 'Types :' . $skip . PHP_EOL;
    $url = API_BASE_URL . 'item-types?$skip=' . $skip . '&$limit=' . $limit;
    $response = file_get_contents($url);
    $response = json_decode($response, true);
    foreach ($response['data'] as $type) {
        $typeStd = new stdClass();
        $typeStd->id = $type['id'];
        $typeStd->name = $type['name']['fr'] ?? 'Inconnu';
        $typeStd->nature = $type['superTypeId'];
        $types[] = $typeStd;
    }

    $skip += $limit;
} while (count($response['data']) > 0);

file_put_contents('src/DataFixtures/itemTypes.json', json_encode($types, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));

//get effects 50 by 50
$itemEffects = [];
$skip = 0;
$itemsData = [];

do {
    echo 'Items :' . $skip . PHP_EOL;
    $url = ITEM_API_URL . '?$skip=' . $skip . '&$limit=' . $limit;
    $response = file_get_contents($url);
    $response = json_decode($response, true);
    $itemsData = array_merge($itemsData, $response['data']);

    $skip += $limit;
} while (count($response['data']) > 0);

$skip = 0;
$recipes = [];
do {
    echo 'Recipes :' . $skip . PHP_EOL;
    $url = RECIPE_API_URL . '?$skip=' . $skip . '&$limit=' . $limit;
    $response = file_get_contents($url);
    $response = json_decode($response, true);
    foreach ($response['data'] as $recipe) {
        $itemRecipe = [];

        for ($i = 0; $i < count($recipe['ingredientIds']); $i++) {
            $row = [
                'item_id' => $recipe['ingredientIds'][$i],
                'quantity' => $recipe['quantities'][$i]
            ];
            $itemRecipe[] = $row;
        }
        $recipes[$recipe['resultId']] = $itemRecipe;
    }

    $skip += $limit;
} while (count($response['data']) > 0);

$items = [];
foreach ($itemsData as $item) {
    $effectStd = new stdClass();
    $effectStd->id = $item['id'];
    $effects = [];
    foreach ($item['effects'] as $effect) {
        if($effect['from'] === 0 && $effect['to'] === 0) {
            continue;
        }
        $effects[] = [
            'effectId' => $effect['characteristic'],
            'min' => $effect['from'],
            'max' => $effect['to'] === 0 ? $effect['from'] : $effect['to'],
        ];
    }
    $effectStd->effects = $effects;
    $itemEffects[] = $effectStd;

    $itemStd = new stdClass();
    $itemStd->_id = $item['id'];
    $itemStd->name = $item['name']['fr'] ?? 'Inconnu';
    $itemStd->type = $item['type']['name']['fr'] ?? 'Inconnu';
    $itemStd->level = $item['level'];
    $itemStd->description = $item['description']['fr'] ?? 'Inconnu';
    $itemStd->nature = $item['type']['superType']['name']['fr'] ?? 'Inconnu';
    $itemStd->imageUrl = $item['img'] ?? '/images/no-photo.png';
    if (isset($recipes[$item['id']])) {
        $itemStd->recipe = $recipes[$item['id']];
    }

    $items[] = $itemStd;
}

file_put_contents('src/DataFixtures/itemEffects.json', json_encode($itemEffects, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));

file_put_contents('src/DataFixtures/items2.json', json_encode($items, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
