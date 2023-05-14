<?php
set_time_limit(0);
//retrieve dofus api images

$limit = 50;
$base_url = 'https://api.dofusdb.fr/items?$limit=' . $limit;
$skip = 0;
$items = [];
$url = $base_url . '&$skip=' . $skip;
$json = file_get_contents($url);
$data = json_decode($json, true);
do {
    echo $skip . PHP_EOL;
    $url = $base_url . '&$skip=' . $skip;
    $json = file_get_contents($url);
    $data = json_decode($json, true);
    foreach($data['data'] as $item) {
        $tmp = [
            'id' => $item['id'],
            'img' => $item['img'],
        ];
        $items[] = $tmp;
    }

    $skip += $limit;
} while (count($data['data']) > 0);

echo 'Number of items: ' . count($items) . PHP_EOL;

echo 'Downloading images...' . PHP_EOL;


foreach($items as $item) {
    echo $item['id'] . PHP_EOL;
    $url = $item['img'];
    $image = file_get_contents($url);
    //save image in directory
    $filename = $item['id'] . '.png';
    file_put_contents($filename, $image);
}