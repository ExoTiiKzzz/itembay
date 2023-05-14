<?php
const API_URL = 'https://api.dofusdb.fr/item-sets?$select[]=id&$select[]=name&$select[]=items';

function getAllItemSets(): array
{
    $itemSets = [];
    $skip = 0;
    $limit = 50;
    do {
        $url = API_URL . '&$skip=' . $skip . '&$limit=' . $limit;
        $response = file_get_contents($url);
        $response = json_decode($response, true);
        $itemSets = array_merge($itemSets, $response['data']);
        $skip += $limit;
        echo $skip . PHP_EOL;
    } while (count($response['data']) > 0);
    return $itemSets;
}

$tmp = getAllItemSets();
$itemSets = [];

foreach ($tmp as $set) {
    $std = new stdClass();
    $std->id = $set['id'];
    if (isset($set['name']['fr'])) {
        $std->name = $set['name']['fr'];
    } else {
        $std->name = 'Inconnu';
    }
    $itemsIds = [];
    foreach ($set['items'] as $item) {
        $itemsIds[] = $item['id'];
    }
    $std->items = $itemsIds;
    $itemSets[] = $std;
}

file_put_contents('src/DataFixtures/itemSets.json', json_encode($itemSets, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));