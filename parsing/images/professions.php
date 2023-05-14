<?php
ini_set('max_execution_time', '6000');
//retrieve dofus api images

//get all classes
const API_URL = 'https://api.dofusdb.fr/jobs';
$professions = [];
$skip = 0;
$limit = 50;

do {
    $url = API_URL . '?$skip=' . $skip . '&$limit=' . $limit;
    echo $url . PHP_EOL;
    $json = file_get_contents($url);
    $data = json_decode($json, true);
    $professions = array_merge($professions, $data['data']);
    $skip += $limit;
} while (count($data['data']) > 0);

foreach ($professions as $profession) {
    $professionId = $profession['id'];
    $professionUrl = $profession['img'];
    $professionPath = 'metiers/' . $professionId . '.png';

    file_put_contents($professionPath, file_get_contents($professionUrl));
}
