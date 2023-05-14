<?php
const API_URL = 'https://api.dofusdb.fr/characteristics';

function getAllCharacteristics(): array
{
    $characteristics = [];
    $skip = 0;
    $limit = 50;
    do {
        $url = API_URL . '?$skip=' . $skip . '&$limit=' . $limit;
        $response = file_get_contents($url);
        $response = json_decode($response, true);
        $characteristics = array_merge($characteristics, $response['data']);
        $skip += $limit;
    } while (count($response['data']) > 0);
    return $characteristics;
}

$tmp = getAllCharacteristics();
$characteristics = [];
foreach ($tmp as $item) {
    $std = new stdClass();
    $std->id = $item['id'];
    if (isset($item['name']['fr'])) {
        $std->name = $item['name']['fr'];
    } else {
        $std->name = 'Inconnu';
    }
    $std->categoryId = $item['categoryId'];
    $std->order = $item['order'];
    $characteristics[] = $std;
}


file_put_contents('src/DataFixtures/characteristics.json', json_encode($characteristics, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
